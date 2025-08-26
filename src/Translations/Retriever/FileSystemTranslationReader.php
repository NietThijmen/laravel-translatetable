<?php

namespace NietThijmen\LaravelTranslatetable\Translations\Retriever;

use Illuminate\Support\Facades\Lang;
use NietThijmen\LaravelTranslatetable\Exceptions\LanguageSystemNotSupported;

abstract class FileSystemTranslationReader implements TranslationRetriever
{
    /**
     * {@inheritDoc}
     *
     * @throws LanguageSystemNotSupported if the language system is not supported
     */
    public function getLanguages(): array
    {
        $languages = [];

        try {
            // @phpstan-ignore-next-line this is fine as we throw an exception if the language system is not supported.
            $paths = Lang::getLoader()->paths();

            foreach ($paths as $path) {
                if (is_dir($path)) {
                    $dirs = scandir($path);
                    foreach ($dirs as $dir) {
                        if ($dir === '.' || $dir === '..') {
                            continue;
                        }
                        if (is_dir($path.DIRECTORY_SEPARATOR.$dir)) {
                            $languages[] = $dir;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            throw new LanguageSystemNotSupported(
                'The selected language system is not supported: '.$e->getMessage()
            );
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
