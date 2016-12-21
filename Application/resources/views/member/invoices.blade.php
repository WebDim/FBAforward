@extends('layouts.frontend.app')

@section('title', 'Invoices')

@section('css')

@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-head-line">Invoices</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @if(count($invoices) > 0)
                <table class="table table-striped">
                    <thead>
                    <th>Invoice Date</th>
                    <th>Invoice Amount</th>
                    <th>Download</th>
                    </thead>

                    @foreach ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->date()->toFormattedDateString() }}</td>
                            <td>{{ $invoice->total() }}</td>
                            <td>
                                <a href="{{ URL::to('member/subscription/download-invoice/'.$invoice->id ) }}">Download</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <h5>You don't have invoices.</h5>
            @endif
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">

    </script>
@endsection
