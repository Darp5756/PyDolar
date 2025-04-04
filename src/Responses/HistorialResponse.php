<?php

namespace Darp5756\PyDolar\Responses;

use Darp5756\PyDolar\Responses\Base\DateTimeResponse;
use Darp5756\PyDolar\Responses\Data\History;

class HistorialResponse extends DateTimeResponse
{
    private array $histories;

    public function __construct($statusCode, array $data)
    {
        parent::__construct($statusCode, $data);
        foreach ($data['history'] as $history) {
            $this->histories[] = new History($history);
        }
    }

    public function getHistorys (): array {
        return $this->histories;
    }
}
