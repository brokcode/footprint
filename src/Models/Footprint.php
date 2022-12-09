<?php


namespace Brokecode\Footprint\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Footprint extends Model
{
    use HasFactory,SoftDeletes;

    const CREATED = 'CREATED';
    const UPDATED = 'UPDATED';
    const DELETED = 'DELETED';
    const VIEWED  = 'VIEWED';

    /**
     * @var string
     */
    protected $table = 'foot_prints';


    /**
     * @var string[]
     */
    protected $fillable = [
        'causer_type',
        'causer_id',
        'model_type',
        'model_id',
        'module_name',
        'old_value',
        'new_value',
        'action',
        'guard',
        'ip_address'
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function causer()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();

    }
}
{

}
