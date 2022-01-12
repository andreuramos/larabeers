<?php

namespace Larabeers\Exceptions;

use Throwable;

class TagNotFoundException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $exception_message = "Tag $message not found";
        parent::__construct($exception_message, $code, $previous);
    }
}
