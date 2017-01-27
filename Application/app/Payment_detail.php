<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment_detail extends Model
{

    protected $primaryKey = 'payment_detail_id';
    protected $table ='payment_details';
    protected $guarded = ['payment_detail_id'];

    public function addresses()
    {
        return $this->belongsTo(Addresses::class,'address_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function user_credit_card_info()
    {
        return $this->belongsTo(User_credit_cardinfo::class);
    }
}