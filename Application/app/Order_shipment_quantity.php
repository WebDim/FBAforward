<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_shipment_quantity extends Model
{
    protected $primaryKey = 'id';
    protected $table ='order_shipment_quantities';
    protected $fillable = ['order_id','shipment_id','shipment_detail_id','quantity'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}