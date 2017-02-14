<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill_of_lading extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='bill_of_ladings';
    protected $fillable = ['order_id','shipment_id','sbnumber', 'bill','status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function Shipment()
    {
        return $this->belongsTo(Shipments::class);
    }
}