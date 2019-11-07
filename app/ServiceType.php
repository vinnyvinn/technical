<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $guarded = [];

    public function track()
    {
        return $this->belongsTo(TrackBy::class,'track_by_id');
    }
    public function machines()
    {
        return $this->belongsToMany(Machine::class, 'machine_service_type');
    }
}
