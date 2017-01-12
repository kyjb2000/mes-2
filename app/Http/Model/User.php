<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
    protected $table = 'user_option';
    protected $primaryKey = 'user_serial';
    public $timestamps = false;
}
