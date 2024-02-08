<?php

/**
 * Параметры конфигурации
 */

return [

    //Параметры подключения mysql
    'db_host'               => '127.0.0.1',
    'db_port'               => '3306',
    'db_user'               => 'user',
    'db_pass'               => 'pass',
    'db_name'               => 'base',

    //Параметры подключения redis
    'redis_host'            => '127.0.0.1',
    'redis_port'            => '6379',
    'redis_db'              => '0',
    'redis_pass'            => '',

    //Основной домен
    'service_host'          => 'localhost',

    //TTL пуш сообщения в минутах
    'push_ttl'              => 7200,

    //Блокировка /api/admin для ip адресов не из списка
    'admin_api_blocking'    => false,
    'admin_api_whitelist'   => [
        '127.0.0.1',
    ],

    //Дебаг режим (включается вывод ошибок, отключается кеширование и минификация js кода)
    'debug'                 => true,

];
