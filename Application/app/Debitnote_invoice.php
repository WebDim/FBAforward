<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Debitnote_invoice extends Model
{
    use Notifiable;
    //
    protected $primaryKey = 'id';

    protected $table ='debitnote_invoices';
    protected $fillable = ['order_id','shipment_id','uploaded_file','status'];

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }
}
