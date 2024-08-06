<?php

namespace Model;

class Config
{
    public static function getRouterConfig(): array
    {
        return \yaml_parse_file(__DIR__ . '/../Config/routes.yaml');
    }

    public static function getDbConfig(): array
    {
        return \yaml_parse_file(__DIR__ . '/../Config/database.yaml');
    }
}