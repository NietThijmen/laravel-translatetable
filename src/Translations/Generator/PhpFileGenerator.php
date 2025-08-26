<?php

namespace NietThijmen\LaravelTranslatetable\Translations\Generator;

use Illuminate\Support\Facades\Blade;

class PhpFileGenerator
{
    /**
     * Use a Blade stub to generate a PHP translation file from an array of translations.
     * I neeeed to make this more robust.
     *
     * @phpstan-ignore-next-line missingType.iterableValue TODO: Get this working with phpstan as the types are correct (ugh)
     */
    public function generate(array $translations): string
    {
        // get the stub file in ../../../stubs/translation-file.blade.stub
        $stub = file_get_contents(__DIR__.'/../../../stubs/translation-file.blade.stub');
        if ($stub === false) {
            throw new \RuntimeException('Could not load stub file');
        }

        $arrayString = var_export($translations, true);

        return Blade::render($stub, [
            'translations' => $arrayString,
        ]);
    }

    /**
     * Generate a PHP translation file and save it to the given file path.
     *
     * @param  array  $translations  the translations to generate the file from
     * @param  string  $filePath  the file path to save the generated file to
     *
     * @phpstan-ignore-next-line missingType.iterableValue TODO: Get this working with phpstan as the types are correct (ugh)
     */
    public function generateToFile(
        array $translations,
        string $filePath
    ): void {
        $content = $this->generate($translations);
        file_put_contents($filePath, $content);
    }
}
