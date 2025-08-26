<?php

namespace NietThijmen\LaravelTranslatetable\Translations\Retriever;

class ArrayTranslationReader extends FileSystemTranslationReader implements TranslationRetriever
{
    /**
     * Get the namespaces for a given language. (E.g. 'messages', 'validation', etc.)
     * {@inheritDoc}
     */
    public function getNamespaces(string $language): array
    {
        $namespaces = [];
        
        $path = $this->getBasePath();

        $langPath = $path.DIRECTORY_SEPARATOR.$language;
        if (is_dir($langPath)) {
            $files = scandir($langPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $namespaces[] = pathinfo($file, PATHINFO_FILENAME);
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
        $path = $this->getBasePath();

        $filePath = $path.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.$namespace.'.php';
        if (file_exists($filePath)) {
            $loadedTranslations = include $filePath;
            if (is_array($loadedTranslations)) {
                $translations = array_merge($translations, $loadedTranslations);
            }
        }

        return $translations;
    }
}
