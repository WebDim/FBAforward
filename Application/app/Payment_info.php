<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment_info extends Model
{
    //
    protected $guarded = ['payment_info_id'];
    protected $primaryKey = 'payment_info_id';
    protected $table ='payment_infos';


}