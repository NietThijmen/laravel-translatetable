<?php

namespace NietThijmen\LaravelTranslatetable\Translations\Retriever;

abstract class FileSystemTranslationReader implements TranslationRetriever
{
    public function getBasePath(): string
    {
        return app()->basePath('lang');
    }

    /**
     * {@inheritDoc}
     */
    public function getLanguages(): array
    {
        $languages = [];

        $path = $this->getBasePath();

        if (is_dir($path)) {
            $dirs = scandir($path);
            foreach ($dirs as $dir) {
                if ($dir === '.' || $dir === '..') {
                    continue;
                }
                if (is_dir($path.DIRECTORY_SEPARATOR.$dir)) {
                    $languages[] = $dir;
                }

                if (is_file($path.DIRECTORY_SEPARATOR.$dir)) {
                    $extension = pathinfo($dir, PATHINFO_EXTENSION);
                    if ($extension === 'json') {
                        $languages[] = pathinfo($dir, PATHINFO_FILENAME);
                    }
                }
            }
        }

        return array_unique($languages);
    }

    /**
     * Get the namespaces for a given language. (E.g. 'messages', 'validation', etc.)
     * {@inheritDoc}
     */
    abstract public function getNamespaces(string $language): array;

    /**
     * {@inheritDoc}
     */
    abstract public function getTranslations(string $language, string $namespace): array;
}
