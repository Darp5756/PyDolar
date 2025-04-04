<?php

namespace Darp5756\PyDolar\Responses;

use Darp5756\PyDolar\Responses\Base\Response;

class ValorResponse extends Response
{
    private float $result;

    public function __construct($statusCode, float $result)
    {
        parent::__construct($statusCode);
        $this->result = $result;
    }

    public function getResult (): float {
        return $this->result;
    }
}
