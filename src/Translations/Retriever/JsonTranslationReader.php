<?php

namespace NietThijmen\LaravelTranslatetable\Translations\Retriever;

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

        $path = $this->getBasePath();

        $file = $path.DIRECTORY_SEPARATOR.$language.'.json';
        if (is_file($file)) {
            $fileContent = file_get_contents($file);
            if ($fileContent === false) {
                return $translations;
            }

            // @phpstan-ignore-next-line argument.type it will match as we check is_file above.
            $content = json_decode(file_get_contents($file), true);
            if (is_array($content)) {
                $translations = array_merge($translations, $content);
            }
        }

        return $translations;
    }
}
