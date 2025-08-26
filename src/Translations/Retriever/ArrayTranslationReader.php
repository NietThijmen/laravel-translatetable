<?php

namespace NietThijmen\LaravelTranslatetable\Translations\Retriever;

use Illuminate\Support\Facades\Lang;

class ArrayTranslationReader extends FileSystemTranslationReader implements TranslationRetriever
{
    /**
     * Get the namespaces for a given language. (E.g. 'messages', 'validation', etc.)
     * {@inheritDoc}
     */
    public function getNamespaces(string $language): array
    {
        $namespaces = [];

        // @phpstan-ignore-next-line this is fine as we throw an exception if the language system is not supported.
        $paths = Lang::getLoader()->paths();

        foreach ($paths as $path) {
            $langPath = $path.DIRECTORY_SEPARATOR.$language;
            if (is_dir($langPath)) {
                $files = scandir($langPath);
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                        $namespaces[] = pathinfo($file, PATHINFO_FILENAME);
                    }
                }
            }
        }

        return array_unique($namespaces);
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslations(string $language, string $namespace): array
    {
        // get all unique translations for a language and namespace
        $translations = [];
        // @phpstan-ignore-next-line this is fine as we throw an exception if the language system is not supported.
        $paths = Lang::getLoader()->paths();
        foreach ($paths as $path) {
            $filePath = $path.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.$namespace.'.php';
            if (file_exists($filePath)) {
                $loadedTranslations = include $filePath;
                if (is_array($loadedTranslations)) {
                    $translations = array_merge($translations, $loadedTranslations);
                }
            }
        }

        return $translations;
    }
}
