<?php


namespace Brokecode\Footprint\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FootprintOptions
{
    public array $trackAttributes = [];

    public array $renderAttributes = [];


    /**
     * Start configuring model with the default options.
     */
    public static function defaults(): self
    {
        return new static();
    }

    /**
     * Track all attributes on the model.
     */
    public function trackAll(): self
    {
        return $this->trackOnly(['*']);
    }

    /**
     * Track changes only if these attributes changed.
     */
    public function trackOnly(array $attributes): self
    {
        $this->trackAttributes = $attributes;

        return $this;
    }

    /**
     * Hide these attributes.
     */
    public function displayOnly(array $attributes): self
    {
        $this->renderAttributes = $attributes;

        return $this;
    }
}
