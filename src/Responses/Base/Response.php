<?php

namespace Darp5756\PyDolar\Responses\Base;

abstract class Response
{
    protected int $statusCode;

    public function __construct (int $statusCode)
	{
        $this->statusCode = $statusCode;
    }

    public function getStatusCode (): int
	{
        return $this->statusCode;
    }
}
