<?php
namespace Core\Database;

use Core\Application;

class Model
{
    /**
     * @var Application
     */
    protected static $app;

    /**
     * @var {string}
     */
    protected $table_name;

    /**
     * @param Application $app
     */
    public static function setApp(Application $app) {
        static::$app = $app;
    }

    public function setTableName($table_name) {
        $this->table_name = $table_name;
    }
}