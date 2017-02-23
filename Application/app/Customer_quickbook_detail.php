<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Customer_quickbook_detail extends Eloquent
{

    protected $guarded = ['id'];
    protected $table = 'customer_quickbook_details';
    protected $fillable=['user_id','customer_id','balance'];

}
