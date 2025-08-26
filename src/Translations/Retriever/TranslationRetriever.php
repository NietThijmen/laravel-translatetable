<?php

namespace NietThijmen\LaravelTranslatetable\Translations\Retriever;

/**
 * The main purpose of this interface is to allow different implementations for retrieving translations.
 * As I might add support for POT files or other formats in the future. (for now it's just the stuff laravel supports out of the box)
 */
interface TranslationRetriever
{
    /**
     * @return string[]
     */
    public function getLanguages(): array;

    /**
     * @param  string  $language  the language to get namespaces for
     * @return string[] the namespaces
     */
    public function getNamespaces(string $language): array;

    /**
     * @return array<string, string> the translations for a language.
     */
    public function getTranslations(string $language, string $namespace): array;
}
