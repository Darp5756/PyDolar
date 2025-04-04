<?php

namespace Darp5756\PyDolar\Responses;

use Darp5756\PyDolar\Responses\Base\Response;

class ErrorResponse extends Response
{
    private string $error;
    private string $message;

    public function __construct (int $statusCode, string $error, string $message) {
        parent::__construct($statusCode);
        $this->error = $error;
        $this->message = $message;
    }

    public function getError (): string
	{
        return $this->error;
    }

    public function getMessage (): string
	{
        return $this->message;
    }
}
