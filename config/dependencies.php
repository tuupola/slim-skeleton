<?php

/*
 * This file is part of the Slim skeleton package
 *
 * Copyright (c) 2016-2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-skeleton
 *
 */

$container = $app->getContainer();

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NullHandler;
use Monolog\Formatter\LineFormatter;

$container["logger"] = function ($containerc) {
    $logger = new Logger("slim");

    $formatter = new LineFormatter(
        "[%datetime%] [%level_name%]: %message% %context%\n",
        null,
        true,
        true
    );

    /* Log to timestamped files */
    $rotating = new RotatingFileHandler(__DIR__ . "/../logs/slim.log", 0, Logger::DEBUG);
    $rotating->setFormatter($formatter);
    $logger->pushHandler($rotating);

    return $logger;
};

$container["spot"] = function ($container) {

    $config = new \Spot\Config();
    $mysql = $config->addConnection("mysql", [
        "dbname" => getenv("DB_NAME"),
        "user" => getenv("DB_USER"),
        "password" => getenv("DB_PASSWORD"),
        "host" => getenv("DB_HOST"),
        "driver" => "pdo_mysql",
        "charset" => "utf8"
    ]);

    $spot = new \Spot\Locator($config);

    return $spot;
};

$container["view"] = function ($c) {
    return new \League\Plates\Engine(__DIR__ . "/../views", "html");
};

/* Setup Spot */
/* Log SQL queries. Make sure logger is configured. */
/*
$logger = new Doctrine\DBAL\Logging\MonologSQLLogger(Monolog\Registry::sql());
$mysql->getConfiguration()->setSQLLogger($logger);
*/