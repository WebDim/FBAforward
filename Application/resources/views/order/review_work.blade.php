@foreach($shipment as $key=>$shipments)
<div>
    <h4>Shipment # {{$key+1}} </h4>
    <div class="table-responsive no-padding">
        <table class="table" id="list">
            <thead>
            <tr>
                <th class="col-md-6"><span>Product</span></th>
                <th><span>Shipping Method Name</span></th>
                <th><span>Other Label</span></th>
                <th><span>Amazon Destination</span></th>
                <th><span>Prep Label</span></th>
                <th><span></span></th>
            </tr>
            </thead>
            <tbody>
            {{--*/$other_label=0 /*--}}
            @foreach($shipment_detail as $shipment_details)
            @if($shipment_details->shipment_id==$shipments->shipment_id)
            <tr>
                <td>@if($shipment_details->product_nick_name==''){{ $shipment_details->product_name}} @else {{$shipment_details->product_nick_name}} @endif</td>
                <td>{{ $shipment_details->shipping_name }}</td>
                <td>

                    @foreach($other_label_detail as $other_label_details)
                    @if($other_label_details->prep_detail_id==$shipment_details->prep_detail_id)
                    @if($other_label_details->label_id=='1')
                    Suffocation Warning
                    @elseif($other_label_details->label_id=='2')
                    This is a Set
                    {{--*/$other_label=1 /*--}}
                    @elseif($other_label_details->label_id=='3')
                    Blank
                    @elseif($other_label_details->label_id=='4')
                    Custom
                    @endif
                    @endif
                    @endforeach
                </td>
                <td>
                    @foreach($amazon_destination as $amazon_destinations)
                    @if($amazon_destinations->shipment_id==$shipments->shipment_id && $amazon_destinations->fulfillment_network_SKU==$shipment_details->fnsku)
                    {{ $amazon_destinations->destination_name }}
                    @endif
                    @endforeach
                </td>
                <td>
                    @foreach($amazon_destination as $amazon_destinations)
                    @if($amazon_destinations->shipment_id==$shipments->shipment_id && $amazon_destinations->fulfillment_network_SKU==$shipment_details->fnsku)
                    {{ $amazon_destinations->label_prep_type }}
                    @endif
                    @endforeach
                </td>
                <td>
                    @if($shipment_details->prep_complete=='0')
                    <a onclick="prepcomplete({{$shipment_details->shipment_detail_id}})">Prep Complete</a>
                    @endif
                </td>
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

            </tr>
            </thead>
            <tbody>
            <tr>
                <th><span>Prep Requirement</span></th>
                <th><span>Print Label</span></th>
                <th><span>Print Other Label</span></th>
            </tr>
            <tr>
                <td>Completed</td>
                <td>Completed</td>
                <td>@if($other_label==1) {{ "Completed" }} @endif</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
@endforeach
