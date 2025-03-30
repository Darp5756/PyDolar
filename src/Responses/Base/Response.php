<?php

namespace Darp5756\PyDolar\Responses\Base;

abstract class Response {
    protected $statusCode;

    public function __construct($statusCode) {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode () {
        return $this->statusCode;
    }
}