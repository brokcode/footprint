<?php


namespace Brokecode\Footprint\Traits;

use Brokecode\Footprint\Models\Footprint as FPModel;
use Brokecode\Footprint\Support\FootprintTrackOptions;
use Brokecode\Footprint\FootPrintServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\AssignOp\Mod;

trait Footprint
{
    protected $enableFootprint = true;

    protected array $hiddenPrints = [];

    protected $model;

    protected $steppedBy;

    protected $tookStep = 'VIEW';

    protected ?FootprintTrackOptions $footprintOptions;

    protected $module = null;

    protected $oldPrints = null;

    protected $newPrints = null;

    abstract public function getFootprintOptions(): FootprintTrackOptions;

    public function bootFootprints()
    {
        static::saved(function ($model){
            static::steppedOn($model);

            if ($model->wasRecentlyCreated) {
                static::tookStep('CREATED');
                static::storeFootprint();
            } else {
                if (!$model->getChanges()) {
                    return;
                }
                static::tookStep('UPDATED');
                static::storeFootprint();
            }
        });

        static::deleted(function (Model $model){
            static::steppedOn($model);

            static::tookStep('DELETED');
            static::storeFootprint();
        });
    }

    public function disableTracking(): self
    {
        $this->enableFootprint = false;

        return $this;
    }

    public function enableTracking(): self
    {
        $this->enableFootprint = true;

        return $this;
    }

    public function footprints(): MorphMany
    {
        return $this->morphMany(FootPrintServiceProvider::determineFootprintModel(), 'model');
    }

    public function module($module = null): self //Move to FootprintCauser Trait
    {
        $this->module = $module;

        return $this;
    }

    public function steppedOn($model): self //Move to FootprintCauser Trait
    {
        $this->model = $model;
        $this->moduleName =  !empty($model->tagname) ? $model->tagname : Str::title(Str::snake(class_basename($model), ' '));

        return $this;

    }

    public function tookStep($action): self //Move to FootprintCauser Trait
    {
        $this->tookStep = $action;

        return $this;
    }

    public function oldPrints(json $data = null): self
    {
        $this->oldPrints = empty($data) ? $this->model->getOriginals() : $data;

        return $this;
    }

    public function newPrints(json $data = null): self
    {
        if ($this->tookStep === 'CREATED') {
            $this->newPrints = empty($data) ? $this->model->getAttributes() : $data;
        } elseif ($this->tookStep === 'UPDATED') {
            $this->newPrints = empty($data) ? $this->model->getChanges() : $data;
        }

        return $this;
    }

    public function trackPrints()
    {
        static::getStepper();

        //dd($this->model, isset($this->model->id),$this->moduleName,$this->steppedBy,$this->tookStep,$this->oldPrints,$this->newPrints);

        $this->storeFootprint();

    }

    public function steppedBy($causer) // Move to be Causer trait
    {
        $this->steppedBy = $causer;

        return $this;
    }

    protected function storeFootprint(): self
    {
        $footprint                     = new FPModel();
        $footprint->model_id           = isset($this->model->id) ? $this->model->id : null;
        $footprint->model_type         = !empty($this->model) ? get_class($this->model) : null;
        $footprint->module_name        = $this->module;
        $footprint->causer_id          = $this->getStepper()->id;
        $footprint->causer_type        = !empty($this->getStepper()) ? get_class($this->getStepper()) : null;
        $footprint->action             = $this->tookStep;
        $footprint->old_value          = $this->oldPrints;
        $footprint->new_value          = $this->newPrints;
        $footprint->guard              = $this->activeStepperGuard();
        $footprint->ip_address         = request()->ip();
        $footprint->save();
        return $this->model;
    }

    private function getStepper()
    {
        $this->steppedBy = empty($this->steppedBy) ? Auth::guard(static::activeStepperGuard())->user() : $this->steppedBy;

        return $this;
    }

    private static function activeStepperGuard()
    {
        $guardName = 'web';
        foreach (array_keys(config('auth.guards')) as $guard) {

            if ($guard !='api') {
                if (auth()->guard($guard)->check()) {
                    $guardName = $guard;
                }
            }

        }

        return $guardName;
    }

}
