<?php

namespace Darp5756\PyDolar\Responses\Base;

abstract class DateTimeResponse extends Response
{
    protected string $date;
    protected string $time;

    public function __construct (int $statusCode, array $data)
    {
        parent::__construct($statusCode);
        $this->date = $data['datetime']['date'];
        $this->time = $data['datetime']['time'];
    }

    public function getDate (): string
    {
        return $this->date;
    }

    public function getTime (): string
    {
        return $this->time;
    }
}
