@foreach($shipment as $key=>$shipments)
    <div>
        <h4>Shipment # {{$key+1}} </h4>
        <div class="table-responsive no-padding">
            <table class="table" id="list">
                <thead>
                <tr>
                    <th class="col-md-6"><span>Product</span></th>
                    <th><span>Shipping Method Name</span></th>
                </tr>
                </thead>
                <tbody>

                @foreach($shipment_detail as $shipment_details)
                    @if($shipment_details->shipment_id==$shipments->shipment_id)
                        <tr>
                            <td>@if($shipment_details->product_nick_name==''){{ $shipment_details->product_name}} @else {{$shipment_details->product_nick_name}} @endif</td>
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
                    <th><span>Cartoon Length</span></th>
                    <th><span>Cartoon Width</span></th>
                    <th><span>Cartoon Weight</span></th>
                    <th><span>Cartoon Height</span></th>
                    <th><span># Of cartoon</span></th>
                    <th><span>Unit Per Cartoon</span></th>
                    <th><span>Cartoon Condition</span></th>
                    <th><span>Warehouse Location</span></th>
                    <th><span>Images</span></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{$shipments->cartoon_length}}</td>
                    <td>{{$shipments->cartoon_width}}</td>
                    <td>{{$shipments->cartoon_weight}}</td>
                    <td>{{$shipments->cartoon_height}}</td>
                    <td>{{$shipments->no_of_cartoon}}</td>
                    <td>{{$shipments->unit_per_cartoon}}</td>
                    <td>{{$shipments->cartoon_condition}}</td>
                    <td>{{$shipments->location}}</td>
                    <td>
                        @foreach($warehouse_images as $warehouse_image)
                            @if($warehouse_image->warehouse_checkin_id==$shipments->id)
                                <a href="{{ url('warehouse/downloadwarehouseimages/'.$warehouse_image->id) }}">{{  $warehouse_image->images}}</a><br>
                            @endif
                        @endforeach
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endforeach
