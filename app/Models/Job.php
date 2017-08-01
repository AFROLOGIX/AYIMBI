<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    //
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
