<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Amazon_destination extends Model
{
    //
    protected $primaryKey = 'amazon_destination_id';

    protected $table ='amazon_destinations';
     protected $fillable = ['destination_name', 'shipment_id','api_shipment_id','sellerSKU','fulfillment_network_SKU','qty','ship_to_address_name','ship_to_address_line1','ship_to_city','ship_to_state_code','ship_to_country_code','ship_to_postal_code','label_prep_type','total_units','fee_per_unit_currency_code','fee_per_unit_value','total_fee_value','prep_complete'];

    public function outbound_shipping_detail()
    {
        return $this->hasMany(Outbound_Shipping_detail::class,'amazon_destination_id');
    }
}
