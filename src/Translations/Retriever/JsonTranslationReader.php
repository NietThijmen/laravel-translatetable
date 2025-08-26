<?php

namespace NietThijmen\LaravelTranslatetable\Translations\Retriever;

use Illuminate\Support\Facades\Lang;

class JsonTranslationReader extends FileSystemTranslationReader implements TranslationRetriever
{
    /**
     * JSON does not use namespaces, so we return a wildcard.
     * {@inheritDoc}
     */
    public function getNamespaces(string $language): array
    {
        return ['json'];
    }

    /**
     * Get the translations for a given language and namespace.
     * namespace is ignored for JSON translations. so you can pass anything you want.
     * {@inheritDoc}
     */
    public function getTranslations(string $language, string $namespace = 'json'): array
    {
        $translations = [];
        // @phpstan-ignore-next-line the jsonPaths method exists in laravel just not in the array loader which is fine as an exception is thrown earlier if the language system is not supported.
        $paths = Lang::getLoader()->jsonPaths();
        foreach ($paths as $path) {
            $file = $path.DIRECTORY_SEPARATOR.$language.'.json';
            if (is_file($file)) {
                $fileContent = file_get_contents($file);
                if( $fileContent === false ) {
                    continue;
                }



                // @phpstan-ignore-next-line argument.type it will match as we check is_file above.
                $content = json_decode(file_get_contents($file), true);
                if (is_array($content)) {
                    $translations = array_merge($translations, $content);
                }
            }

        }

        return $translations;
    }
}
