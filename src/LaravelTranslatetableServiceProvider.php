<?php

namespace NietThijmen\LaravelTranslatetable;

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
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->info('Thank you for downloading my package!');
                $command->askToStarRepoOnGitHub('nietthijmen/laravel-translatetable');

                $buyMeACoffeeQuestion = $command->confirm('If you like the project, please donate to my buymeacoffee');
                if ($buyMeACoffeeQuestion) {
                    $hasOpened = self::openLink(self::$buymeacoffee);
                    if (! $hasOpened) {
                        $command->info('Please visit '.self::$buymeacoffee);
                    }
                    $command->info('Thank you so much for donating!');
                }

                $command->info('The package is fully installed, please read the README over on github');
            });
    }
}
