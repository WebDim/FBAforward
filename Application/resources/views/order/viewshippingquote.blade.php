@foreach($shipment as $key=>$shipments)
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
@endforeach