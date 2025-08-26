<?php

namespace NietThijmen\LaravelTranslatetable\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use NietThijmen\LaravelTranslatetable\LaravelTranslatetableServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelTranslatetableServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
