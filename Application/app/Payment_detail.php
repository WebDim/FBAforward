<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment_detail extends Model
{

    protected $primaryKey = 'payment_detail_id';
    protected $table ='payment_details';
    protected $guarded = ['payment_detail_id'];
}