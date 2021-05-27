<?php

namespace OnlineUniConverter\Laravel\Tests;

use OnlineUniConverter\Laravel\Facades\OnlineUniConverter;
use OnlineUniConverter\Laravel\Providers\OnlineUniConverterServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected $http;

    protected function getPackageProviders($app)
    {
        return [
            OnlineUniConverterServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'OnlineUniConverter' => OnlineUniConverter::class,
        ];
    }

}
