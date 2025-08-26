<?php

namespace NietThijmen\LaravelTranslatetable\Commands;

use Illuminate\Console\Command;
use NietThijmen\LaravelTranslatetable\Exceptions\LanguageSystemNotSupported;
use NietThijmen\LaravelTranslatetable\Translations\Retriever\ArrayTranslationReader;
use NietThijmen\LaravelTranslatetable\Translations\Retriever\JsonTranslationReader;
use NietThijmen\LaravelTranslatetable\Translations\Retriever\TranslationRetriever;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenerateSpreadsheetCommand extends Command
{
    protected $signature = 'translations:generate-spreadsheet {--output=translations.xlsx : The output file path}';

    protected $description = 'Generate a spreadsheet of all translations in your application';

    /**
     * @param  array<string, string|array>  $data
     * @return void
     *              // @phpstan-ignore-next-line TODO: Get this working with phpstan as the types are correct (ugh)
     */
    protected function handleRecursiveSpreadsheetArray(
        Worksheet $sheet,
        array $data,
        int &$row,
        int $languageIndex,
        string $key
    ): void {
        foreach ($data as $subKey => $subValue) {
            if (is_array($subValue)) {
                $this->handleRecursiveSpreadsheetArray($sheet, $subValue, $row, $languageIndex, "{$key}.{$subKey}");

                continue;
            }

            $column = chr(66 + $languageIndex); // 66 is ASCII for 'B'
            // Set the key in column A
            $sheet->setCellValue("A{$row}", "{$key}.{$subKey}");
            // Set the translation in the appropriate language column;
            $sheet->setCellValue("{$column}{$row}", $subValue);
            $row++;
        }

    }

    public function handle(): int
    {
        $retrievers = [
            ArrayTranslationReader::class,
            JsonTranslationReader::class,
        ];

        $languages = [];

        // key: language, value: array with key: namespace, value: array with key: translation key, value: translation (I should really use a proper data structure here but whatever)
        $allTranslations = [];
        $allNamespaces = [];

        foreach ($retrievers as $retriever) {
            /** @var TranslationRetriever $instance */
            /** @phpstan-ignore-next-line  */
            $instance = new $retriever;
            try {
                $retrievedLanguages = $instance->getLanguages();
            } catch (LanguageSystemNotSupported $languageSystemNotSupported) {
                $this->warn($languageSystemNotSupported->getMessage());

                continue;
            }
            foreach ($retrievedLanguages as $language) {
                if (! in_array($language, $languages)) {
                    $languages[] = $language;
                }

                $namespaces = $instance->getNamespaces($language);
                foreach ($namespaces as $namespace) {
                    $translations = $instance->getTranslations($language, $namespace);
                    if (! isset($allTranslations[$language])) {
                        $allTranslations[$language] = [];
                    }
                    if (! isset($allTranslations[$language][$namespace])) {
                        $allTranslations[$language][$namespace] = [];
                    }
                    foreach ($translations as $key => $value) {
                        $allTranslations[$language][$namespace][$key] = $value;
                    }

                    $allNamespaces[$namespace] = $namespace;
                }
            }
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
        $spreadsheet->removeSheetByIndex(0); // remove default sheet
        $namespaceSheets = [];

        foreach ($allNamespaces as $namespace) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($namespace);
            $namespaceSheets[$namespace] = $sheet;

            // Set header row
            $sheet->setCellValue('A1', 'Key');
            foreach ($languages as $index => $language) {
                $column = chr(66 + $index); // 66 is ASCII for 'B'
                $sheet->setCellValue("{$column}1", $language);
            }
        }

        foreach ($allTranslations as $language => $namespacedTranslations) {
            foreach ($namespacedTranslations as $namespace => $translations) {
                if (! isset($namespaceSheets[$namespace])) {
                    continue;
                }
                $sheet = $namespaceSheets[$namespace];

                $row = 2; // Start from the second row
                foreach ($translations as $key => $value) {
                    // Find the column for the current language
                    $languageIndex = array_search($language, $languages);
                    if ($languageIndex === false) {
                        continue;
                    }

                    if (is_string($value)) {
                        $column = chr(66 + $languageIndex); // 66 is ASCII for 'B'
                        // Set the key in column A
                        $sheet->setCellValue("A{$row}", $key);
                        // Set the translation in the appropriate language column
                        $sheet->setCellValue("{$column}{$row}", $value);
                        $row++;
                    }

                    if (is_array($value)) {
                        $this->handleRecursiveSpreadsheetArray($sheet, $value, $row, $languageIndex, $key);
                    }
                }
            }
        }

        $output = $this->option('output') ?? 'translations.xlsx';

        if(!is_string($output)) {
            $this->error("Invalid output path");
            return 0;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($output);
        $this->info("Spreadsheet generated at {$output}");

        return 1;
    }
}
