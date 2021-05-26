Online-UniConvert-laravel
=======================

> This is the official Laravel package for the OnlineUniConvert _API v2_

[![Tests](https://github.com/Online-UniConverter/Online-UniConverter-laravel/actions/workflows/run-tests.yml/badge.svg)](https://github.com/Online-UniConverter/Online-UniConverter-laravel/actions/workflows/run-tests.yml)
[![Latest Stable Version](https://poser.pugx.org/onlineuniconvert/onlineuniconvert-laravel/v)](//packagist.org/packages/onlineuniconvert/onlineuniconvert-laravel) 
[![Total Downloads](https://poser.pugx.org/onlineuniconvert/onlineuniconvert-laravel/downloads)](//packagist.org/packages/onlineuniconvert/onlineuniconvert-laravel) 
[![Latest Unstable Version](https://poser.pugx.org/onlineuniconvert/onlineuniconvert-laravel/v/unstable)](//packagist.org/packages/onlineuniconvert/onlineuniconvert-laravel) 
[![License](https://poser.pugx.org/onlineuniconvert/onlineuniconvert-laravel/license)](//packagist.org/packages/onlineuniconvert/onlineuniconvert-laravel)

## Installation

You can install the package via composer:

    composer require onlineuniconvert/onlineuniconvert-laravel

This package requires a HTTP client. It works both with Guzzle 6 and 7. If you are using Guzzle 6, you need an adapter:

    composer require php-http/guzzle6-adapter

Guzzle 7 works out of the box.

Next you must publish the config file. 

    php artisan vendor:publish --provider="OnlineUniConvert\Laravel\Providers\OnlineUniConvertServiceProvider"

This is the content that will be published to `config/OnlineUniConvert.php`:

```php
<?php
return [
    /**
     * You can generate API keys here: https://OnlineUniConvert.com/dashboard/api/v2/keys.
     */

    'api_key' => env('OnlineUniConvert_API_KEY', ''),

    /**
     * Use the OnlineUniConvert Sanbox API (Defaults to false, which enables the Production API).
     */
    'sandbox' => env('OnlineUniConvert_SANDBOX', false),
];
```

Creating Import Tasks
-------------------
```php
use OnlineUniConvert\Models\Import;

// init
$import = (new Import('import/upload'));
$this->OnlineUniConvert->imports()->create($import);

// upload
$response = $this->OnlineUniConvert->imports()->upload($import, fopen(__DIR__ . '/files/单独.mov', 'r'), 'vid00084source.mov');
var_dump($response);

// info
$this->OnlineUniConvert->imports()->info($import);
var_dump($import);
```

Creating Convert Tasks
-------------------
```php
use OnlineUniConvert\Models\Task;

// init
$task = (new Task('convert'))->set('input', 'jnthak3k-amuk-bj8l-cj7h-nn1yno4jty8i')->set('output_format', 'mp4');
$this->OnlineUniConvert->tasks()->create($task);
var_dump($task);

// info
$this->OnlineUniConvert->tasks()->info($task);
var_dump($task);
```

Creating Export Tasks
-------------------
```php
use OnlineUniConvert\Models\Common;
use OnlineUniConvert\Models\Export;

// init
$export = (new Export('export/url'))->set('input', '2w2y610m-awgo-bt8q-cq2p-981fu1w1bmr0');
$this->OnlineUniConvert->exports()->create($export);
var_dump($export);

// info
$this->OnlineUniConvert->exports()->info($export);
var_dump($export);

// download
$source = $this->OnlineUniConvert->getHttpTransport()->download($export->getResult()->files[0]->url)->detach();

$dest = tmpfile();
$destPath = stream_get_meta_data($dest)['uri'];
stream_copy_to_stream($source, $dest);
```

You can use the [OnlineUniConvert](https://developer.media.io/api-introduction.html) to see the available options for the various task types.

Tests
-----------------

    vendor/bin/phpunit 

Resources
---------

* [PHP SDK](https://developer.media.io/)
* [API Documentation](https://developer.media.io/)
* [OnlineUniConvert Blog](https://developer.media.io/)
