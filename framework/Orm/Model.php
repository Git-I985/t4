<?php

namespace T4\Orm;

use T4\Core\Std;
use T4\Dbal\DriverFactory;

abstract class Model
    extends Std
{

    use TMagic, TCrud, TRelations;

    /**
     * Имя поля первичного ключа
     */
    const PK = '__id';

    /**
     * Типы связей
     */
    const HAS_ONE = 'hasOne';
    const BELONGS_TO = 'belongsTo';
    const HAS_MANY = 'hasMany';
    const MANY_TO_MANY = 'manyToMany';

    /**
     * Схема модели
     * db: name of DB connection from application config
     * table: table name
     * columns[] : columns
     * - type*
     * - length
     * relations[] : relations
     * - type*
     * - model*
     * - on
     * @var array
     */
    static protected $schema = [];

    /**
     * Расширения, подключаемые к модели
     * @var array
     */
    static protected $extensions = [];

    /**
     * Схема модели
     * с учетом изменений, внесенных расширениями
     * @return array
     */
    public static function getSchema()
    {
        static $schema = null;
        if (null === $schema) {
            $class = get_called_class();
            $schema = $class::$schema;
            $extensions = $class::getExtensions();
            foreach ( $extensions as $extension ) {
                $extensionClassName = '\\T4\\Orm\\Extensions\\'.ucfirst($extension);
                $extension = new $extensionClassName;
                $schema['columns'] = $extension->prepareColumns($schema['columns'], $class);
                $schema['relations'] = $extension->prepareRelations(isset($schema['relations']) ? $schema['relations'] : [], $class);
            }
        }
        return $schema;
    }

    /**
     * Список полей модели
     * @return array
     */
    public static function getColumns() {
        $schema = static::getSchema();
        return $schema['columns'];
    }

    /**
     * Список расширений, подключаемых к модели
     * @return array
     */
    public static function getExtensions()
    {
        return !empty(static::$extensions) ?
            array_merge(['standard'], static::$extensions) :
            ['standard'];
    }

    /**
     * Имя таблицы в БД, соответствующей данной модели
     * @return string Имя таблицы в БД
     */
    public static function getTableName()
    {
        $schema = static::getSchema();
        if (isset($schema['table']))
            return $schema['table'];
        else {
            $className = explode('\\', get_called_class());
            return strtolower(array_pop($className)) . 's';
        }
    }

    public static function getDbDriver()
    {
        $schema = static::getSchema();
        $dbConnectionName = !empty($schema['db']) ? $schema['db'] : 'default';
        if ('cli'==PHP_SAPI) {
            $app = \T4\Console\Application::getInstance();
        } else {
            $app = \T4\Mvc\Application::getInstance();
        }
        $driver = $app->config->db->{$dbConnectionName}->driver;
        return DriverFactory::getDriver($driver);
    }

    public static function getDbConnection()
    {
        $schema = static::getSchema();
        $dbConnectionName = !empty($schema['db']) ? $schema['db'] : 'default';
        if ('cli'==PHP_SAPI) {
            $app = \T4\Console\Application::getInstance();
        } else {
            $app = \T4\Mvc\Application::getInstance();
        }
        $connection = $app->db[$dbConnectionName];
        return $connection;
    }

}