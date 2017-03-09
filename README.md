# Slim 3 skeleton

[![Latest Version](https://img.shields.io/packagist/v/tuupola/slim-skeleton.svg?style=flat-square)](https://packagist.org/packages/tuupola/slim-skeleton)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

This is Slim 3 skeleton project form Composer. Project includes [Spot2](http://phpdatamapper.com/), [Monolog](https://github.com/Seldaek/monolog), [Plates](http://platesphp.com/) libraries and [Vagrant](https://www.vagrantup.com/) virtualmachine config.

## Install

Install the latest version using [composer](https://getcomposer.org/).

``` bash
$ composer create-project --no-interaction --stability=dev tuupola/slim-skeleton app
```

## Usage

If you have [Vagrant](https://www.vagrantup.com/) installed start the virtual machine.

``` bash
$ cd app
$ vagrant up
```

Now you can access the app at [https://192.168.50.50/](https://192.168.50.50/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.