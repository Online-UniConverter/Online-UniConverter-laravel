<?php

namespace OnlineUniConvert\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class OnlineUniConvert extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \OnlineUniConvert\OnlineUniConvert::class;
    }
}
