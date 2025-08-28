<?php

namespace NietThijmen\LaravelTranslatetable\Commands;

use Illuminate\Console\Command;
use NietThijmen\LaravelTranslatetable\Translations\Generator\PhpFileGenerator;

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

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $confirmation = $this->ask('This will overwrite existing translations. Type "yes" to confirm:');
        if (strtolower($confirmation) !== 'yes') {
            $this->info('Operation cancelled.');

            return 0;
        }

        $sheets = $spreadsheet->getAllSheets();

        $namespaces = [];
        $fileGenerator = new PhpFileGenerator;
        foreach ($sheets as $sheet) {
            $namespaces[$sheet->getTitle()] = $sheet;
            $sheetData = $sheet->toArray();

            $header = array_shift($sheetData);
            $languages = array_slice($header, 1); // Skip the first column (keys)

            $translations = [];
            foreach ($sheetData as $row) {
                $key = array_shift($row); // First column is the key
                foreach ($languages as $index => $language) {
                    if (! isset($translations[$language])) {
                        $translations[$language] = [];
                    }

                    if (empty($key)) {
                        continue;
                    }

                    $translations[$language][$key] = $row[$index] ?? '';
                }
            }

            foreach ($translations as $language => $translation) {

                if (empty($language)) {
                    continue;
                }

                if (count($translations) === 0) {
                    $this->info("No translations found for namespace '{$sheet->getTitle()}'");

                    continue;
                }

                $langPath = lang_path($language);
                if (! is_dir($langPath) && ! mkdir($langPath, 0755, true) && ! is_dir($langPath)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $langPath));
                }

                $filePath = "$langPath/{$sheet->getTitle()}.php";
                if (file_exists($filePath)) {
                    $this->warn("Overwriting existing file: $filePath");
                    unlink($filePath);
                }

                $fileGenerator->generateToFile(
                    $translation,
                    $filePath
                );

                $this->info("Generated translations for language '$language' in namespace '{$sheet->getTitle()}'");
            }
        }

        return 0;
    }
}
