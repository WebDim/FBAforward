<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Inspection_report extends Model
{
    use Notifiable;
    //
    protected $primaryKey = 'id';

    protected $table ='inspection_reports';
    protected $fillable = ['order_id','uploaded_file','status'];

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
}
