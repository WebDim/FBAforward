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
                </tr>
                </thead>
                <tbody>
                {{--*/$other_label=0 /*--}}
                @foreach($shipment_detail as $shipment_details)
                    @if($shipment_details->shipment_id==$shipments->shipment_id)
                        <tr>
                            <td>{{ $shipment_details->product_name }}</td>
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
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td></td>
                    <td id="print{{$shipments->shipment_id}}"><a href="{{url('order/printshippinglabel/'.$shipments->shipment_id)}}">Print Shipping Label</a></td>
                    <td id="label{{$shipments->shipment_id}}"><a onclick="verifyshipment('{{$shipments->shipment_id}}','2')">verify Label</a></td>
                    <td id="ship_load{{$shipments->shipment_id}}" colspan="2"><a onclick="verifyshipment('{{$shipments->shipment_id}}','3')">Verify Shipment Load On Truck</a></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endforeach
