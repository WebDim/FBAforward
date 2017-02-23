<table class="display" width="100%" cellspacing="0" id="list">
    <thead>
    <tr>
        <th></th>
        <th><span>Invoice Id</span></th>
        <th><span>SyncToken</span></th>
        <th><span>Create Time</span></th>
        <th><span>Last Update Time</span></th>
        <th><span>Doc Number</span></th>
        <th><span>TXN Date</span></th>
        <th><span>Customer Reference Name</span></th>
        <th><span>Address</span></th>
        <th><span>LAT</span></th>
        <th><span>Due Date</span></th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoice_detail as $invoice_details)
        <tr>
            <td></td>
            <td>{{$invoice_details->invoice_id}}</td>
            <td>{{$invoice_details->synctoken}}</td>
            <td>{{$invoice_details->created_time}}</td>
            <td>{{$invoice_details->updated_time}}</td>
            <td>{{$invoice_details->docnumber}}</td>
            <td>{{$invoice_details->txndate}}</td>
            <td>{{$invoice_details->customer_ref_name}}</td>
            <td>{{$invoice_details->line1." ".$invoice_details->line2." ".$invoice_details->city." ".$invoice_details->country." ".$invoice_details->postalcode}}</td>
            <td>{{$invoice_details->lat}}</td>
            <td>{{$invoice_details->due_date}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
