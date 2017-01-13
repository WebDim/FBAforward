<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_labels_detail extends Model
{
    //
    protected $primaryKey = 'product_label_detail_id';
    protected $table ='product_labels_details';
    protected $fillable = ['product_id','product_label_id', 'fbsku', 'qty'];

    public function product_labels()
    {
        return $this->belongsTo(Product_labels::class,'product_label_id');
    }

}
