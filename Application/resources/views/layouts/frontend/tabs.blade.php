<div class="row">
    <div class="col-md-10">
        <h2 class="page-head-line">CREATE NEW ORDER</h2>
    </div>
    <div class="col-md-2 ">
        <a href="{{ url('order/index') }}" class="btn btn-primary">Order Management</a>
    </div>
</div>
<ul class="nav nav-tabs" role="tablist">
    <li class="{{ ($data == 'shipment') ?  'active' : '' }} ">
        <a href="javascript:void(0)">Shipment Info </a>
    </li>
    <li class="{{ ($data == 'supplier') ?  'active' : '' }} ">
        <a href="javascript:void(0)">Supplier Info</a>
    </li>
    <li class="{{ ($data == 'pre_inspection') ?  'active' : '' }} ">
        <a href="javascript:void(0)">Pre Inspection</a>
    </li>
    <li class="{{ ($data == 'label') ?  'active' : '' }} ">
        <a href="javascript:void(0)">Labels</a>
    </li>
    <li class="{{ ($data == 'prep_service') ?  'active' : '' }} ">
        <a href="javascript:void(0)">Prep Services</a>
    </li>
    <li class="{{ ($data == 'list_service') ?  'active' : '' }} ">
        <a href="javascript:void(0)">Listing Services</a>
    </li>
    <li class="{{ ($data == 'outbound_shipping') ?  'active' : '' }} ">
        <a href="javascript:void(0)">Outbound Shipping</a>
    </li>
    <li class="{{ ($data == 'review_shipment') ?  'active' : '' }} ">
        <a href="javascript:void(0)">Review Shipment</a>
    </li>
    <li class="{{ ($data == 'payment') ?  'active' : '' }} ">
        <a href="javascript:void(0)">Payment</a>
    </li>
</ul>