<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trucking_company extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='trucking_companies';
    protected $fillable = ['company_name'];

    public function delivery_booking()
    {
        return $this->hasMany(Delivery_booking::class);
    }
}