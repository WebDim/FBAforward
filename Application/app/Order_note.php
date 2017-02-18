<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_note extends Model
{
    protected $primaryKey = 'id';
    protected $table ='order_notes';
    protected $fillable = ['order_id','shipping_notes','prep_notes'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
