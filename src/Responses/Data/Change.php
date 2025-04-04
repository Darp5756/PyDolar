<?php

namespace Darp5756\PyDolar\Responses\Data;

class Change
{
	private string $lastUpdate;
    private float $price;

	public function __construct (array $data)
	{
        $this->lastUpdate = $data['last_update'];
        $this->price = $data['price'];
    }

	public function getLastUpdate (): string
    {
        return $this->lastUpdate;
    }

    public function getPrice (): float
    {
        return $this->price;
    }
}
