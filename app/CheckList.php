<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CheckList extends Model
{
    protected $guarded = [];

    public function expirytype()
    {
        return $this->belongsTo(ExpiryDate::class,'expiry_type_id');
    }
}
