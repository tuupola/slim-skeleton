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
    $connection = $config->addConnection("connection", [
        "dbname" => getenv("DB_NAME"),
        "user" => getenv("DB_USER"),
        "password" => getenv("DB_PASSWORD"),
        "host" => getenv("DB_HOST"),
        "path" => getenv("DB_PATH"),
        "driver" => "pdo_" . getenv("DB_DRIVER"),
        "charset" => "utf8"
    ]);

    $spot = new \Spot\Locator($config);

    $sqllogger = new Tuupola\DBAL\Logging\Psr3Logger($container["logger"]);
    $connection->getConfiguration()->setSQLLogger($sqllogger);

    return $spot;
};

$container["view"] = function ($c) {
    return new \League\Plates\Engine(__DIR__ . "/../views", "html");
};
