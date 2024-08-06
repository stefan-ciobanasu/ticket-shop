<?php

namespace Model;
class Router
{
    private array $config;

    public function __construct()
    {
        $this->setConfig(Config::getRouterConfig());
    }

    public function getController($query): array
    {
        $return['controller'] = 'IndexController';
        $return['action'] = 'indexAction';
        $config = $this->getConfig();
        $path = explode('?', $query)[0];
        foreach ($config['routes'] as $route) {
            if ($route['request'] == $path) {
                $return['controller'] = $route['controller'];
                $return['action'] = $route['method'];
            }
        }
        return $return;
    }

    private function getConfig(): array
    {
        return $this->config;
    }

    private function setConfig(array $config): void
    {
        $this->config = $config;
    }
}