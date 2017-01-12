<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;

class ProductList extends Model
{
    //
    protected $table = 'product_list';
    protected $primaryKey = 'product_list_serial';
    public $timestamps = false;
}
