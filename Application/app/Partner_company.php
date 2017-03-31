<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partner_company extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='partner_companies';
    protected $fillable = ['delivery_company', 'terminal', 'destination', 'user_id', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}