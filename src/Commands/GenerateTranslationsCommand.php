<?php

namespace NietThijmen\LaravelTranslatetable\Commands;

use Illuminate\Console\Command;

class GenerateTranslationsCommand extends Command
{
    protected $signature = 'translations:from-spreadsheet {file : The path to the spreadsheet file}';

    protected $description = 'Generate translations from a spreadsheet';

    public function handle(): int
    {
        $file = $this->argument('file');
        if (empty($file)) {
            $file = $this->ask('Please enter the path to the spreadsheet file:');
        }

        if (! file_exists($file)) {
            $this->error("File not found: $file");

            return 1;
        }

        $this->info('This command is not implemented yet.');
        return 0;
    }
}
