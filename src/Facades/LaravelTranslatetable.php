<?php

namespace NietThijmen\LaravelTranslatetable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \NietThijmen\LaravelTranslatetable\LaravelTranslatetable
 */
class LaravelTranslatetable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \NietThijmen\LaravelTranslatetable\LaravelTranslatetable::class;
    }
}
