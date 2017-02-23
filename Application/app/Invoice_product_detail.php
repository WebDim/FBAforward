<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice_product_detail extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='invoice_product_details';
    protected $fillable=['invoice_product_detail_id','item_ref','item_ref_name','qty','amount'];

    public function invoice_detail()
    {
        return $this->belongsTo(Invoice_detail::class);
    }
}