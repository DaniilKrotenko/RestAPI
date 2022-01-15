<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class UserShift extends Model
{
    protected $fillable = [
        'user_id',
        'shift_id',
        'shift_request',
    ];

    protected $table = 'usershifts';

}
