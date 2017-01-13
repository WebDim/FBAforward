<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_labels extends Model
{
    //
    protected $primaryKey = 'product_label_id';

    protected $table ='product_labels';

    public function product_labels_detail()
    {
        return $this->hasMany(Product_labels_detail::class,'product_label_id');
    }
}
