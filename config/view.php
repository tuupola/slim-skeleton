<?php

$container["view"] = function ($c) {
    return new \League\Plates\Engine(__DIR__ . "/../views", "html");
};