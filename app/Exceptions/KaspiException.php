<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class KaspiException extends Exception
{
    /**
     * Конструктор исключения KaspiException
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message = '', int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        // Вывод информации об исключении в лог
        Log::error('KaspiException: ' . $message);
    }
}
