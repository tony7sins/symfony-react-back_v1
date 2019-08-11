<?php

namespace App\Exception;

use \Throwable;

final class EmptyBodyException extends \Exception
{
    public function __construct(
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    ) {

        $message = "The body of the POST/PUT method cannot be empty!";
        $code = 400;

        parent::__construct(
            $message,
            $code,
            $previous
        );
    }
}
