<?php

namespace Lexxpavlov\SettingsBundle\DBAL;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;

class SettingsType extends Type
{
    const Boolean = 'boolean';
    const Integer = 'int';
    const Float = 'float';
    const String = 'string';
    const Text = 'text';
    const Html = 'html';

    private static $values = array(
        self::Boolean => 'Boolean',
        self::Integer => 'Integer',
        self::Float  => 'Float',
        self::String  => 'String',
        self::Text    => 'Text',
        self::Html    => 'Html',
    );

    protected $name = 'LexxpavlovSettingsEnumType';

    public static function getValues()
    {
        return array_keys(static::$values);
    }

    public static function getChoices()
    {
        return static::$values;
    }

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = array();
        foreach (static::getValues() as $value) $values[] = "'$value'";

        // thanks to https://github.com/fre5h/DoctrineEnumBundle
        if ($platform instanceof SqlitePlatform) {
            return sprintf('TEXT CHECK(%s IN (%s))', $fieldDeclaration['name'], $values);
        }
        if ($platform instanceof PostgreSqlPlatform) {
            return sprintf('VARCHAR(255) CHECK(%s IN (%s))', $fieldDeclaration['name'], $values);
        }

        return "ENUM(".implode(", ", $values).") COMMENT '(DC2Type:$this->name)'";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, static::getValues())) {
            throw new \InvalidArgumentException("Invalid value '$value' for enum '$this->name'.");
        }
        return $value;
    }

    public function getName()
    {
        return $this->name;
    }
}
