<?php

namespace Darp5756\PyDolar\Responses;

use Darp5756\PyDolar\Responses\Base\Response;

class ErrorResponse extends Response {
    private $error;
    private $message;

    public function __construct ($statusCode, $error, $message) {
        parent::__construct($statusCode);
        $this->error = $error;
        $this->message = $message;
    }

    public function getError () {
        return $this->error;
    }

    public function getMessage () {
        return $this->message;
    }
}