<?php

namespace Lexxpavlov\SettingsBundle\Entity;

use Lexxpavlov\SettingsBundle\DBAL\SettingsType;

class Settings
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $comment;

    public function __toString()
    {
        return $this->name ?: 'n/a';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Settings
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return Settings
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Settings
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Settings
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        switch ($this->type) {
            case SettingsType::Boolean: return $this->value ? true : false;
            case SettingsType::Integer: return (int)$this->value;
            case SettingsType::Float: return (float)$this->value;
            default: return $this->value;
        }
    }

    /**
     * @param mixed $value
     * @return Settings
     */
    public function setValue($value)
    {
        switch ($this->type) {
            case SettingsType::Boolean: $this->value = ($value === true || (float)$value != 0 || mb_strtolower($value, 'UTF-8') == 'on') ? '1' : '0'; break;
            default: $this->value = (string)$value; break;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return Settings
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

}