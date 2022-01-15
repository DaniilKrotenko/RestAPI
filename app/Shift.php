<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Shift extends Model
{
    protected $fillable = [
        'project_id',
        'timeStart',
        'timeEnd',
        'date',
    ];
}
