<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Custom_clearance extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='custom_clearances';
    protected $fillable = ['order_id','shipment_id','form_3461', 'form_7501','delivery_order','custom_duty','terminal_fee','status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function Shipment()
    {
        return $this->belongsTo(Shipments::class);
    }
}