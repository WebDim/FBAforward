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
                            <td>{{ $shipment_details->product_name }}</td>
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
                    <th ><span>Shipment Refrence Number(SB Number)</span></th>
                    <th><span><a href="{{ url('order/downloadladingbill/'.$order_id.'/'.$shipments->shipment_id) }}">Download Lading Bill</a></span></th>

                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $shipments->sbnumber}}</td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endforeach
