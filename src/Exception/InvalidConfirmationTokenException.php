<?php
declare(strict_types=1);

namespace App\Exception;

class InvalidConfirmationTokenException extends \Exception
{
    public function __construct(
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    ) {

        $message = "Confirmation token is invalid!";

        parent::__construct(
            $message,
            $code,
            $previous
        );
    }
}

