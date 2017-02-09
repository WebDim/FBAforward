<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shipping_quote extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='shipping_quotes';
    protected $fillable = ['order_id','shipment_id','shipment_port', 'shipment_term', 'shipment_weights','chargable_weights','cubic_meters','total_shipping_cost','status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function Shipment()
    {
        return $this->belongsTo(Shipments::class);
    }
    public function shipping_charge()
    {
        return $this->hasMany(Shipping_charge::class);
    }
}