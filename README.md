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

```php
<?php
return [
    /**
     * You can generate API keys here: https://developer.media.io/.
     */

    'api_key' => env('OnlineUniConverter_API_KEY', ''),

    /**
     * Use the OnlineUniConverter Sanbox API (Defaults to false, which enables the Production API).
     */
    'sandbox' => env('OnlineUniConverter_SANDBOX', false),
];
```

Creating Import Tasks
-------------------
```php
use OnlineUniConverter\Models\Import;

// init
$import = (new Import('import/upload'));
$this->OnlineUniConverter->imports()->create($import);

// upload
$response = $this->OnlineUniConverter->imports()->upload($import, fopen(__DIR__ . '/files/单独.mov', 'r'), 'vid00084source.mov');
var_dump($response);

// info
$this->OnlineUniConverter->imports()->info($import);
var_dump($import);
```

Creating Convert Tasks
-------------------
```php
use OnlineUniConverter\Models\Task;

// init
$task = (new Task('convert'))->set('input', 'jnthak3k-amuk-bj8l-cj7h-nn1yno4jty8i')->set('output_format', 'mp4');
$this->OnlineUniConverter->tasks()->create($task);
var_dump($task);

// info
$this->OnlineUniConverter->tasks()->info($task);
var_dump($task);
```

Creating Export Tasks
-------------------
```php
use OnlineUniConverter\Models\Common;
use OnlineUniConverter\Models\Export;

// init
$export = (new Export('export/url'))->set('input', '2w2y610m-awgo-bt8q-cq2p-981fu1w1bmr0');
$this->OnlineUniConverter->exports()->create($export);
var_dump($export);

// info
$this->OnlineUniConverter->exports()->info($export);
var_dump($export);

// download
$source = $this->OnlineUniConverter->getHttpTransport()->download($export->getResult()->files[0]->url)->detach();

$dest = tmpfile();
$destPath = stream_get_meta_data($dest)['uri'];
stream_copy_to_stream($source, $dest);
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
