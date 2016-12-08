LexxpavlovSettingsBundle
=================

This bundle helps you to manage your settings in Symfony2/3 project.

Settings has one of types: Boolean, Integer, Float, String, Text, Html. You may get one concrete setting or fetch group
of settings. Fetching of settings may be cached by your cache provider used in project.

Management of settings provides by SonataAdminBundle. In other case you may manage settings via code by use special 
functions or predefined forms.

Installation
------------

### Composer

Download LexxpavlovSettingsBundle and its dependencies to the vendor directory.

You can use Composer for the automated process:

```bash
$ composer require lexxpavlov/settingsbundle
```

or manually add link to bundle into your `composer.json` and run `$ composer update`:

```json
{
    "require" : {
        "lexxpavlov/settingsbundle": "~1.2"
    }
}
```

Composer will install bundle to `vendor/lexxpavlov` directory.

### Adding bundle to your application kernel

```php

// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Lexxpavlov\SettingsBundle\LexxpavlovSettingsBundle(),
        // ...
    );
}
```

### Configuration

Bundle does not need any required parameters and will work without changes in `config.yml`. But you may config some 
parameters, read more below.

Now you need create the tables in your database:

```bash
$ php bin/console doctrine:schema:update --dump-sql
```
or in Symfony2:
```bash
$ php app/console doctrine:schema:update --dump-sql
```

This will show SQL queries for creating of tables in the database. You may manually run these queries.

> **Note.**
You may also execute `php bin/console doctrine:schema:update --force` command, and Doctrine will create needed
tables for you. But I strongly recommend you to execute `--dump-sql` first and check SQL, which Doctrine will execute.

> **Note.**
If you use 1.1.* version of bundle, you need to update database.

Usage
-----

Use SonataAdminBundle for manage your settings. Otherwise use predefined forms. You feel free to 
use the bundle if you configure settings with database tool (phpMyAdmin or other) or by use special functions called in 
your code (see below).

You may put settings to group or not. Groups may be used for fetching several settings at one query.

Fetching of settings are supported in twig templates or in controller (or in any script where settings service are injected).

For example, you created 3 settings:
* `page_title` without group
* `description` and `keywords` in `meta` group.

##### Use in controller

```php
namespace App\YourBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $settings = $this->get('settings');

        $title = $settings->get('page_title');
        $meta = $settings->group('meta');

        return array(
            'title' => $title,
            'meta_description' => $meta['description'],
            'meta_keywords' => $meta['keywords'],
        );
    }
}
```

##### Use in template

```twig
{% extends '::layout.html.twig'%}

{% block meta %}
<title>{{ settings('page_title') }}</title>
<meta name="description" content="{{ settings('meta', 'description') }}">
<meta name="keywords" content="{{ settings('meta', 'keywords') }}">
{% endblock %}
```

Advanced usage
--------------

### Full configuration

Here is the default configuration for the bundle (all parameters are optional):

```yaml
lexxpavlov_settings:
    enable_short_service: true # default true, use false for disable registering 'settings' service
    html_widget: ckeditor      # default null, valid values are 'null', 'ckeditor'
    cache_provider: cache      # default null, for enable database caching set up name of caching service 
    ckeditor:                  # set parameters of ckeditor. Not need if IvoryCKEditorBundle is installed
        base_path: /ckeditor/            
        js_path: /ckeditor/ckeditor.js
```

`ckeditor` form type may be added by [IvoryCKEditorBundle](https://github.com/egeloen/IvoryCKEditorBundle). If you are 
using CKEditor without `IvoryCKEditorBundle`, you must specify the parameters `base_path` and `js_path`.

### Groups of settings

For fetch several of settings from one group you may use one of two cases:

```php
$param1 = $settings->get('category', 'param1');
$param2 = $settings->get('category', 'param2');
// or
$cat = $settings->group('category');
$param1 = $cat['param1'];
$param2 = $cat['param2'];
```

Both of cases has an identical perfomance - the whole group will fetch while first access to it, fetching of data will 
be only one time.

### Using groups in twig

```twig
{% set params = settings_group('category') %}

<ul>
<li>{{ params.param1 }}</li>
<li>{{ params['param-2'] }}</li>
</ul>
```

### Perfomance and caching

Use caching for increase of fetching settings. If you don't use caching already - it is perfect time to do! It's very simple!

```yaml
# app/config/services.yml
services:
    cache:
        class: Doctrine\Common\Cache\FilesystemCache
        arguments: ["%kernel.cache_dir%/cache"]

# app/config/config.yml
lexxpavlov_settings:
    cache_provider: cache
```

The bundle will use registered service `cache` for cache data. Cache provider must extend `Doctrine\Common\Cache\CacheProvider`.

### Arrange Settings admin group in SonataAdminBundle

`SonataAdminBundle` arranges admin groups by its bundles in `AppKernel::registerBundles()`. If `LexxpavlovSettingsBundle` 
is added above your app bundles, then Settings group will be first group in menu (before your content or service groups, 
created in your bundles). If you want that settings group will be last group (below your groups), you add 
`LexxpavlovSettingsBundle` after your bundle in `AppKernel::registerBundles()`:
```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new AppBundle\AppBundle(),
        new Lexxpavlov\SettingsBundle\LexxpavlovSettingsBundle(),
    );
}
```

### Manage settings without Sonata Admin

If you don't use SonataAdminBundle in your project, you may use predefined forms or special functions.

##### Use predefined forms (in controller)

Save setting:
```php
$form = $this->createForm('lexxpavlov_settings');
// $form->setData($setting); // use for edit of existed setting
if ($request->isMethod('POST')) {
    $form->handleRequest($request);
    if ($form->isValid()) {
        $this->get('settings')->save($form->getData());
    }
}
return array( 'form' => $form->createView() );
```
Save group:
```php
$form = $this->createForm('lexxpavlov_settings_category');
if ($request->isMethod('POST')) {
    $form->handleRequest($request);
    if ($form->isValid()) {
        $this->get('settings')->saveGroup($form->getData());
    }
}
return array( 'form' => $form->createView() );
```

For use predefined forms, you need add form theme:
```yaml
# app/config/config.yml
twig:
    # ...
    form_themes:
        - 'LexxpavlovSettingsBundle:Form:setting_value_edit.html.twig'
```

##### Manual create and update settings

```php
use Lexxpavlov\SettingsBundle\DBAL\SettingsType;

// In controller:

// Get service from container
$settings = $this->get('settings');

// Update a existed setting
$settings->update('param', 'new value');
$settings->update('category', 'param_in_cat', 'new value');

// Create a new setting
$settings->create(null, 'new.1', SettingsType::Boolean, true, 'comment - setting w/o group');
$settings->create('test', 'new.2', SettingsType::Text, 'test text', 'comment - setting in group');

// Create a new empty group
$settings->createGroup('new-cat', 'comment of group');
```
