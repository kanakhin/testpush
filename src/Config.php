<?php

declare(strict_types=1);

/**
 * Объект для получения параметров конфигурации из файла конфигурации
 */

namespace Kanakhin\Push;

class Config
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function get($name, $default=null)
    {
        return $this->config[$name] ?? $default;
    }
}
