{!! Form::open(['url' =>  'customer', 'method' => 'post', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate', 'onsubmit'=>'return check()']) !!}
{{--*/ $cnt=1 /*--}}
<input type="hidden" id="order" name="order" value="{{$order_id}}">
@foreach($shipment as $key=>$shipments)
    <div>

        <input type="hidden" name="shipment_id{{$cnt}}" id="shipment_id{{$cnt}}" value="{{ $shipments->shipment_id }}">
        <div class="table-responsive no-padding">
            <table class="table" id="list">
                <thead>
                <tr>
                    <th class="col-md-2"><span>Product</span></th>
                    <th><span>Total</span></th>
                    <th><span>Shipment Type</span></th>
                    <th><span>Amazon Ship Quantity</span></th>
                    <th><span>Pending Ship Quantity</span></th>
                    <th><span>Ship Quantity</span></th>
                </tr>
                </thead>
                <tbody>
                {{--*/ $sub_cnt=1 /*--}}
                @foreach($shipment_detail as $shipment_details)
                    @if($shipment_details->shipment_id==$shipments->shipment_id)

                        <tr>
                            <td>{{ $shipment_details->product_name }}<input type="hidden" name="shipment_detail_id{{$cnt}}_{{$sub_cnt}}" id="shipment_detail_id{{$cnt}}_{{$sub_cnt}}" value="{{$shipment_details->shipment_detail_id}}"></td>
                            <td>{{ $shipment_details->total}}</td>
                            <td>{{ $shipment_details->shipping_name }}</td>
                            <td>{{ $shipment_details->quantity }}</td>
                            <td>{{ $total =$shipment_details->total - $shipment_details->quantity }}</td>
                            <td>@if($total>0)<input type="text" name="quantity{{$cnt}}_{{$sub_cnt}}" id="quantity{{$cnt}}_{{$sub_cnt}}" onkeyup="checkquantity('{{$cnt}}','{{$sub_cnt}}','{{$total}}')">@endif</td>
                        </tr>
                       {{--*/ $sub_cnt++ /*--}}
                    @endif
                @endforeach
                <input type="hidden" name="sub_count{{$cnt}}" id="sub_count{{$cnt}}" value="{{$sub_cnt}}">
                </tbody>
            </table>
        </div>
    </div>
    {{--*/ $cnt++ /*--}}
@endforeach
<input type="hidden" name="count" id="count" value="{{$cnt}}">
<div class="form-group">
    {!! Form::label('', '',['class' => 'control-label col-md-5']) !!}
    <div class="col-md-7">
        <div class="input-group">
            {!! Form::submit('  Submit  ', ['class'=>'btn btn-primary',  'id'=>'add']) !!}
        </div>
    </div>
</div>
{!! Form::close()  !!}
