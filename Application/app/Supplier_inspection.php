<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier_inspection extends Model
{
    //
    protected $primaryKey = 'supplier_inspection_id';
    protected $table ='supplier_inspections';
    protected $fillable = ['supplier_detail_id','supplier_id', 'user_id', 'is_inspection', 'inspection_decription'];

    public function supplier_detail()
    {
        return $this->belongsTo(Supplier_detail::class,'supplier_detail_id');
    }
}
