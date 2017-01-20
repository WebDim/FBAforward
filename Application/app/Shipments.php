<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shipments extends Model
{
    protected $primaryKey = 'shipment_id';
    protected $fillable = ['shipping_method_id','order_id','user_id','split_shipment','goods_ready_date','is_activated'];
}
