<?php

namespace Model;

use mysqli;

class Database
{
    private mysqli $connectionHandler;
    private static array $instances = [];
    public function __construct()
    {
        $config = Config::getDbConfig()['database'];
        $this->connectionHandler = mysqli_connect($config['host'], $config['user'], $config['password'], $config['database']);
    }

    public static function getInstance(): Database
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    public function __call($name, $arguments)
    {
        $result = $this->connectionHandler->$name(...$arguments);
        return $result;
    }
}