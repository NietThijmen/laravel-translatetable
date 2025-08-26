<?php

namespace NietThijmen\LaravelTranslatetable\Exceptions;

class LanguageSystemNotSupported extends \Exception
{
    // @phpstan-ignore-next-line in exception classes these types of properties are allowed
    protected $message = 'The selected language system is not supported.';

    // @phpstan-ignore-next-line in exception classes these types of properties are allowed
    protected $code = 500;
}
