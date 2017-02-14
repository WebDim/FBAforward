<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prealert_detail extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='prealert_details';
    protected $fillable = ['order_id','shipment_id','ISF', 'HBL','MBL','ETD_china','ETA_US','delivery_port','status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function Shipment()
    {
        return $this->belongsTo(Shipments::class);
    }
}