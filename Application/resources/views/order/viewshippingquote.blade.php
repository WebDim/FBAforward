<h4>FBAForward</h4>
<span>1550 HOTEL CIRCLE NORTH</span><br>
<span>SUITE 360</span><br>
<span>SAN DIEGO, CA 92108</span><br>
<span>+1 866 526 3951</span><br>
<br><br>
@foreach($shipment as $key=>$shipments)
    @foreach($shipment_detail as $shipment_details)
        @if($shipment_details->shipment_id==$shipments->shipment_id)
        <table id="list" border="1" width="95%">
            <thead>
            <tr>
                <th width="20%"><span>QUOTE TYPE</span></th>
                <th width="50%"><span>ITEM DESCRIPTION</span></th>
                <th width="10%"><span># Of UNITS</span></th>
                <th width="20%"><span>QUOTE DATE</span></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td width="20%">{{$shipment_details->shipping_name}}</td>
                <td width="50%">@if($shipment_details->product_nick_name==''){{ $shipment_details->product_name}} @else {{$shipment_details->product_nick_name}} @endif</td>
                <td width="10%">{{$shipment_details->total}}</td>
                <td width="20%"></td>
            </tr>
            </tbody>
        </table>
            <br>
        @endif
    @endforeach
    <table  id="" border="1" width="95%">
        <thead>
        <tr>
            <th width="20%"><span>WEIGHT(KGS)</span></th>
            <th width="25%"><span>VOLUME(CBM)</span></th>
            <th width="25%"><span>SHIPPING PORT</span></th>
            <th width="10%"><span>TERMS</span></th>
            <th width="20%"><span>TRANSIT TIME</span></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td width="20%">{{ $shipments->shipment_weights }}</td>
            <td width="25%">{{ $shipments->cubic_meters}}</td>
            <td width="25%">{{ $shipments->shipment_port}}</td>
            <td width="10%">{{ $shipments->shipment_term }}</td>
            <td width="20%"></td>
        </tr>
        </tbody>
    </table>
    <br>
@endforeach
<table border="1" width="95%">
    <thead>
    <tr>
    <th width="80%"><span>CHARGE</span></th>
    <th width="20%"><span>AMOUNT</span></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td width="80%">SHIPPING,DOCUMENTATION,INSURANCE</td>
        <td width="20%"></td>
    </tr>
    <tr>
        <td width="80%">US.CUSTOMS BROKERAGE</td>
        <td width="20%"></td>
    </tr>
    <tr>
        <td width="80%">ESTIMATED PORT AND TERMINAL FEES</td>
        <td width="20%"></td>
    </tr>
    <tr>
        <td width="80%" align="right">TOTAL COST:</td>
        <td width="20%"></td>
    </tr>
    </tbody>
</table>
<br>
<h1 align="center">we look forward to helping you build, grow, and scale your e-commerce bussiness!</h1>
<span align="center">If you have any questions about this quote, please call or email our order support team:</span><br>
<span align="center">Phone: +1 866 526 3951     Email:orders@fbaforward.com</span><br>

{{--@foreach($shipment as $key=>$shipments)
    <div>
        <h4>Shipment # {{$key+1}} </h4>
        <div class="table-responsive no-padding">
            <table class="table" id="list">
                <thead>
                <tr>
                    <th class="col-md-2"><span>Product</span></th>
                    <th class="col-md-2"><span>Qty Per Case</span></th>
                    <th class="col-md-2"><span># Of Case</span></th>
                    <th><span>Total</span></th>
                    <th><span>Shipment Type</span></th>
                </tr>
                </thead>
                <tbody>

                @foreach($shipment_detail as $shipment_details)
                    @if($shipment_details->shipment_id==$shipments->shipment_id)
                        <tr>
                            <td>{{ $shipment_details->product_name }}</td>
                            <td>{{ $shipment_details->qty_per_box}}</td>
                            <td>{{ $shipment_details->no_boxs}}</td>
                            <td>{{ $shipment_details->total}}</td>
                            <td>{{ $shipment_details->shipping_name }}</td>
                        </tr>

                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table" id="">
                <thead>
                <tr>
                    <th><span>Shipment Port</span></th>
                    <th><span>Shipment Term</span></th>
                    <th><span>Weight</span></th>
                    <th><span>Chargable Weight</span></th>
                    <th><span>Cubic Meters</span></th>
                    <th><span>Charges</span></th>
                    <th><span>Total Shipping Cost</span></th>

                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $shipments->shipment_port }}</td>
                        <td>{{ $shipments->shipment_term}}</td>
                        <td>{{ $shipments->shipment_weights}}</td>
                        <td>{{ $shipments->chargable_weights }}</td>
                        <td>{{ $shipments->cubic_meters }}</td>
                        <td>
                            @foreach($charges as $charge)
                                @if($charge->shipment_id==$shipments->shipment_id)
                                    {{ $charge->name." $".$charge->price }}<br>
                                @endif
                            @endforeach
                        </td>
                        <td>${{ $shipments->total_shipping_cost }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endforeach--}}