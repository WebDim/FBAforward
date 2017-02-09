<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shipping_charge extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='shipping_charges';
    protected $fillable = ['shipping_id', 'charges_id'];

    public function shipping_quote()
    {
        return $this->belongsTo(Shipping_quote::class);
    }
    public function charges()
    {
        return $this->belongsTo(Charges::class);
    }
}