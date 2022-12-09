<?php


namespace Brokecode\Footprint\Support;


use Illuminate\Contracts\Config\Repository;

class FootprintStatus
{
    protected $enabled = true;

    public function __construct(Repository $config)
    {
        $this->enabled = $config['footprint.enabled'];
    }

    public function enable(): bool
    {
        return $this->enabled = true;
    }

    public function disable(): bool
    {
        return $this->enabled = false;
    }

    public function disabled(): bool
    {
        return $this->enabled === false;
    }
}

