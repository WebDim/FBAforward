<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice_detail extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='invoice_details';
    protected $fillable=['invoice_id','created_time','updtaed_time','docnumber','txndate','customer_ref','customer_ref_name','line1','line2','city','country','postalcode','lat','due_date','total_amt','currancy_ref','currency_ref_name','total_taxe'];

    public function invoice_product_detail()
    {
        return $this->hasMany(Invoice_product_detail::class);
    }
}

