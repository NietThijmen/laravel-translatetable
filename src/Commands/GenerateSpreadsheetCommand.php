<?php

namespace NietThijmen\LaravelTranslatetable\Commands;

use Illuminate\Console\Command;

class GenerateSpreadsheetCommand extends Command
{
    protected $signature = 'translations:generate-spreadsheet';

    protected $description = 'Generate a spreadsheet of all translations in your application';

    public function handle(): int
    {
        $this->info('This command is not implemented yet.');
        return 1;
    }
}
