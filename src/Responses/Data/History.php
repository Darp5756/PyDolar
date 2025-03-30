<?php

namespace Darp5756\PyDolar\Responses;

class History
{
    private string $lastUpdate;
    private float $price;
    private float $priceHigh;
    private float $priceLow;

    public function __construct(array $data) {
        $this->lastUpdate = $data['last_update'];
        $this->price = $data['price'];
        $this->priceHigh = $data['price_high'];
        $this->priceLow = $data['price_low'];
    }

    public function getLastUpdate (): string
    {
        return $this->lastUpdate;
    }

    public function getPrice (): float
    {
        return $this->price;
    }

    public function getPriceHigh (): float
    {
        return $this->priceHigh;
    }

    public function getPriceLow (): float
    {
        return $this->priceLow;
    }
}