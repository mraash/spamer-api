<?php

declare(strict_types=1);

namespace App\Http\ArgumentResolver\RequestBody\Exception;

use RuntimeException;

class RequestBodyConvertException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invalid request format.');
    }
}
