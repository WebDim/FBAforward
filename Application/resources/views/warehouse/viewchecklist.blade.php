<div>
    <h4>Notes </h4>
    <div class="table-responsive no-padding">
        <table class="table" id="">
            <thead>
            <tr>
                <th><span>Shipping Notes</span></th>
                <th><span>Prep Notes</span></th>
            </tr>
            </thead>
            <tbody>
            @foreach($order_note as $order_notes)
                <tr>
                    <td>{{$order_notes->shipping_notes}}</td>
                    <td>{{$order_notes->prep_notes}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@foreach($shipment as $key=>$shipments)
    <div>

        <div class="table-responsive no-padding">
            <table class="table" id="list">
                <thead>
                <tr>
                    <th class="col-md-6"><span>Product</span></th>
                    <th><span></span></th>
                    <th><span>Shipping Method Name</span></th>
                    <th><span>Other Label</span></th>
                    <th><span>Quantity</span></th>
                    <th><span>Amazon Destination</span></th>
                    <th><span>Prep Label</span></th>
                    <th><span></span></th>
                </tr>
                </thead>
                <tbody>

                @foreach($shipment_detail as $shipment_details)
                    @if($shipment_details->shipment_id==$shipments->shipment_id)
                        <tr>
                            <td>@if($shipment_details->product_nick_name==''){{ $shipment_details->product_name}} @else {{$shipment_details->product_nick_name}} @endif </td>
                            <td>@if($user_role==10) <a href="javascript:void(0)" onclick="getlabel('{{$shipment_details->fnsku}}')">Print Label</a>@endif</td>
                            <td>{{ $shipment_details->shipping_name }}</td>
                            <td>
                                @foreach($other_label_detail as $other_label_details)
                                    @if($other_label_details->prep_detail_id==$shipment_details->prep_detail_id)
                                        @if($other_label_details->label_id=='1')
                                            Suffocation Warning
                                        @elseif($other_label_details->label_id=='2')
                                            This is a Set @if($user_role==10)<a href="javascript:void(0)" onclick="getotherlabel()">Print
                                                Label</a>@endif
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
                                        {{ $amazon_destinations->qty }}<br>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @foreach($amazon_destination as $amazon_destinations)
                                    @if($amazon_destinations->shipment_id==$shipments->shipment_id && $amazon_destinations->fulfillment_network_SKU==$shipment_details->fnsku)
                                        {{ $amazon_destinations->destination_name }}<br>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @foreach($amazon_destination as $amazon_destinations)
                                    @if($amazon_destinations->shipment_id==$shipments->shipment_id && $amazon_destinations->fulfillment_network_SKU==$shipment_details->fnsku)
                                        {{ $amazon_destinations->label_prep_type }}<br>
                                    @endif
                                @endforeach
                            </td>
                            <td id="prep{{$shipment_details->shipment_detail_id}}">
                                @foreach($amazon_destination as $amazon_destinations)
                                    @if($amazon_destinations->shipment_id==$shipments->shipment_id && $amazon_destinations->fulfillment_network_SKU==$shipment_details->fnsku && $amazon_destinations->prep_complete==0)
                                        <span id="prep{{$amazon_destinations->amazon_destination_id}}"><a href="javascript:void(0)" onclick="prepcomplete('{{$shipment_details->shipment_detail_id}}','{{$amazon_destinations->amazon_destination_id}}')">Prep
                                            Complete</a></span><br>
                                    @endif
                                @endforeach
                               {{-- @if($shipment_details->prep_complete=='0')
                                    <a href="javascript:void(0)" onclick="prepcomplete({{$shipment_details->shipment_detail_id}})">Prep
                                        Complete</a>
                                @endif --}}
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

                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endforeach
<script>
    function prepcomplete(shipment_detail_id, amazon_destination_id) {
        $('.preloader').css("display", "block");

        $.ajax({
            headers: {
                'X-CSRF-Token': "{{ csrf_token() }}"
            },
            method: 'POST', // Type of response and matches what we said in the route
            url: '/warehouse/prepcomplete', // This is the url we gave in the route
            data: {
                'shipment_detail_id': shipment_detail_id,
                'amazon_destination_id' : amazon_destination_id,
            }, // a JSON object to send back
            success: function (response) { // What to do if we succeed
                $('.preloader').css("display", "none");
                $("#prep" + amazon_destination_id).hide();
            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                $('.preloader').css("display", "none");
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    }
</script>
