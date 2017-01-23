<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Addresses extends Model
{
    //
    protected $primaryKey = 'address_id';

    protected $table ='addresses';
    protected $fillable = ['user_id', 'type','address_1', 'address_2', 'city', 'state', 'postal_code', 'country'];

}
