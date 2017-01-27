<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'order_id';
    protected $fillable = ['order_no','user_id','is_activated'];

    public function supplier_detail()
    {
        return $this->hasMany(Supplier_detail::class);
    }
    public function payment_detail()
    {
        return $this->hasMany(Payment_detail::class);
    }
}
