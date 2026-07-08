<?php

namespace App\Core\Popup\Exceptions;

use Exception;

class PopupException extends Exception
{
    public function __construct(string $message = 'Popup error', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}

class PopupNotFoundException extends PopupException
{
    public function __construct(string $id = '')
    {
        parent::__construct("Popup not found: {$id}", 404);
    }
}

class PopupValidationException extends PopupException
{
    public function __construct(string $message = 'Validation failed')
    {
        parent::__construct($message, 422);
    }
}
