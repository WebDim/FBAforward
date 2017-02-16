<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CFS_terminal extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='cfs_terminals';
    protected $fillable = ['terminal_name'];

    public function delivery_booking()
    {
        return $this->hasMany(Delivery_booking::class);
    }
}