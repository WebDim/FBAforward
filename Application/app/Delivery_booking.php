<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery_booking extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='delivery_bookings';
    protected $fillable = ['order_id','shipment_id','CFS_terminal', 'trucking_company','warehouse_fee','fee_paid','ETA_warehouse','status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function Shipment()
    {
        return $this->belongsTo(Shipments::class);
    }
}