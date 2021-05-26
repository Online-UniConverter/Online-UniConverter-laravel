<?php

namespace OnlineUniConvert\Laravel\Tests;

use OnlineUniConvert\Laravel\Facades\OnlineUniConvert;
use OnlineUniConvert\Laravel\Providers\OnlineUniConvertServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected $http;

    protected function getPackageProviders($app)
    {
        return [
            OnlineUniConvertServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'OnlineUniConvert' => OnlineUniConvert::class,
        ];
    }

}
