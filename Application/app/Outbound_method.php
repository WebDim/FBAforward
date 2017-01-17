<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outbound_method extends Model
{
    protected $primaryKey = 'outbound_method_id';
    protected $table ='outbound_methods';
}
