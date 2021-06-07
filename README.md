Online-UniConverter-laravel
=======================

> This is the official Laravel package for the OnlineUniConverter _API v2_

[![Tests](https://github.com/Online-UniConverter/Online-UniConverter-laravel/actions/workflows/run-tests.yml/badge.svg)](https://github.com/Online-UniConverter/Online-UniConverter-laravel/actions/workflows/run-tests.yml)
[![Latest Stable Version](https://poser.pugx.org/onlineuniconverter/onlineuniconverter-laravel/v)](//packagist.org/packages/onlineuniconverter/onlineuniconverter-laravel) 
[![Total Downloads](https://poser.pugx.org/onlineuniconverter/onlineuniconverter-laravel/downloads)](//packagist.org/packages/onlineuniconverter/onlineuniconverter-laravel) 
[![Latest Unstable Version](https://poser.pugx.org/onlineuniconverter/onlineuniconverter-laravel/v/unstable)](//packagist.org/packages/onlineuniconverter/onlineuniconverter-laravel) 
[![License](https://poser.pugx.org/onlineuniconverter/onlineuniconverter-laravel/license)](//packagist.org/packages/onlineuniconverter/onlineuniconverter-laravel)

## Installation

You can install the package via composer:

    composer require onlineuniconverter/onlineuniconverter-laravel

This package requires a HTTP client. It works both with Guzzle 6 and 7. If you are using Guzzle 6, you need an adapter:

    composer require php-http/guzzle6-adapter

Guzzle 7 works out of the box.

Next you must publish the config file. 

    php artisan vendor:publish --provider="OnlineUniConverter\Laravel\Providers\OnlineUniConverterServiceProvider"

This is the content that will be published to `config/onlineuniconverter.php`:

## Usage

### File conversion
```php
# Init Convert Class

$onlineUniConverter = new \OnlineUniConverter\Laravel\OnlineUniConverter(config('onlineuniconverter'));
```

```php
# Convert the file to /a/path/to/file.mp4

$onlineUniConverter->from('/a/path/to/file.mov')->to('mp4')->convert();
```

```php
# Convert the file and save it in a different location /a/new/path/to/new.mp4

$onlineUniConverter->from('/a/path/to/biggles.webm')->to('/a/new/path/to/new.mp4')->convert();
```

```php
# It also works with Laravel's file upload

if (Input::hasFile('photo'))
{
    $onlineUniConverter->from( Input::file('photo') )->to('/a/local/path/profile_image.jpg')->convert();
}
```

```php
# Compress the image to kitty.jpg with ratio of 70%

$onlineUniConverter->from('kitty.png')->ratio(0.7)->to('jpg')->compress();

```

#### Remote files
It will also work with converting remote files (just make sure you provide a path to save it to)
```php
# Convert Google's SVG logo hosted on Wikipedia to a png on your server

$onlineUniConverter->from('http://upload.wikimedia.org/wikipedia/commons/a/aa/Logo_Google_2013_Official.svg')->to('images/google.png')->convert();
```

You can use the [OnlineUniConverter](https://developer.media.io/api-introduction.html) to see the available options for the various task types.

Tests
-----------------

    vendor/bin/phpunit 

Resources
---------

* [PHP SDK](https://developer.media.io/)
* [API Documentation](https://developer.media.io/)
* [OnlineUniConverter Blog](https://developer.media.io/)