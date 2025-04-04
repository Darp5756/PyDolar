<?php

namespace Darp5756\PyDolar\Responses;

use Darp5756\PyDolar\Responses\Base\DateTimeResponse;
use Darp5756\PyDolar\Responses\Data\Change;

class CambiosResponse extends DateTimeResponse
{
    private array $changes;

    public function __construct (int $statusCode, array $data)
    {
        parent::__construct($statusCode, $data);
        foreach ($data['daily'] as $change) {
            $this->changes[] = new Change($change);
        }
    }

    public function getChanges (): array
	{
        return $this->changes;
    }
}
