<?php

namespace NietThijmen\LaravelTranslatetable;

use NietThijmen\LaravelTranslatetable\Commands\GenerateSpreadsheetCommand;
use NietThijmen\LaravelTranslatetable\Commands\GenerateTranslationsCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelTranslatetableServiceProvider extends PackageServiceProvider
{
    public static string $buymeacoffee = 'https://www.buymeacoffee.com/nietthijmen';

    public static function openLink(
        string $url
    ): bool {
        if (PHP_OS_FAMILY == 'Darwin') {
            exec("open {$url}");

            return true;
        }
        if (PHP_OS_FAMILY == 'Windows') {
            exec("start {$url}");

            return true;
        }
        if (PHP_OS_FAMILY == 'Linux') {
            exec("xdg-open {$url}");

            return true;
        }

        return false;
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-translatetable')
            ->hasCommands(
                GenerateSpreadsheetCommand::class,
                GenerateTranslationsCommand::class
            )
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->askToStarRepoOnGitHub('nietthijmen/laravel-translatetable');
            });
    }
}
