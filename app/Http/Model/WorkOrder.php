<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    //
    protected $table = 'work_order';
    protected $primaryKey = 'work_order_serial';
    public $timestamps = false;
}
