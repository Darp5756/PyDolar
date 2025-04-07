<?php

namespace Darp5756\PyDolar\Responses;

use Darp5756\PyDolar\Responses\Base\DateTimeResponse;
use GuzzleHttp\Exception\InvalidArgumentException;

class MonitorsResponse extends DateTimeResponse
{
    private array $monitors;

    public function __construct (int $statusCode, array $data)
	{
        parent::__construct($statusCode, $data);
        foreach ($data['monitors'] as $monitor => $dataMonitor) {
            $this->monitors[] = [$monitor => new MonitorResponse($statusCode, $dataMonitor)];
        }
    }

    public function getMonitors (): array
    {
        return $this->monitors;
    }

    public function getMonitor (string $monitor): MonitorResponse
    {
        if (!array_key_exists($monitor, $this->monitors)) {
            throw new InvalidArgumentException("Monitor '{$monitor}' is invalid.");
        }
        return $this->monitors[$monitor];
    }
}
