<?php

namespace Darp5756\PyDolar\Responses;

use Darp5756\PyDolar\Responses\Base\Response;

class MonitorResponse extends Response
{
    private float $change;
    private string $color;
    private string $image;
    private string $lastUpdate;
    private float $percent;
    private float $price;
    private float $priceOld;
    private string $symbol;
    private string $title;

    public function __construct($statusCode, array $data)
    {
        parent::__construct($statusCode);
        $this->change = $data['change'];
        $this->color = $data['color'];
        $this->image = $data['image'];
        $this->lastUpdate = $data['last_update'];
        $this->percent = $data['percent'];
        $this->price = $data['price'];
        $this->priceOld = $data['price_old'];
        $this->symbol = $data['symbol'];
        $this->title = $data['title'];
    }

    public function getChange(): float
    {
        return $this->change;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getLastUpdate(): string
    {
        return $this->lastUpdate;
    }

    public function getPercent(): float
    {
        return $this->percent;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getPriceOld(): float
    {
        return $this->priceOld;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}