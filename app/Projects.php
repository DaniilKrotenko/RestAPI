<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Projects extends Model
{
    protected $fillable = [
        'id',
        'name',
        'address',
        'projectNumber',
        'geoFence',
        'radius',
    ];
    protected $table = 'project';
}
