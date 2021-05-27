<?php

namespace OnlineUniConverter\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class OnlineUniConverter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \OnlineUniConverter\OnlineUniConverter::class;
    }
}
