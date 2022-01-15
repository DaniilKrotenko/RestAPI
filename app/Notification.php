<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Notification extends Model
{
    protected $fillable = [
      'from_user',
      'to_user',
      'text',
      'date',
      'type',
        'is_read',
        'group_id'
    ];

    protected $table = 'notification';
}
