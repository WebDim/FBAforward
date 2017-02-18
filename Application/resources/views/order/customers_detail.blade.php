@extends('layouts.frontend.app')
@section('title', $title)
@section('css')
    <style type="text/css">
        .margin-bottom {
            margin-bottom: 5px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-head-line">{{$title}}</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive no-padding">
                <table class="table" id="list">
                    <thead>
                    <tr>
                        <th><span>Company Name</span></th>
                        <th><span>Company Phone</span></th>
                        <th><span>Company Address</span></th>
                        <th><span>Company Primary Business Type</span></th>
                        <th><span>Amazon Revenue<br>(Estimate Annual)</span></th>
                        <th><span>FBA Order<br>(Estimate Annual)</span></th>
                        <th><span>Reference From</span></th>
                        <th><span>Action</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($user as $users)
                        <tr>
                            <td><b class="text-info">{{ $users->company_name }}</b></td>
                            <td><b class="text-info">{{ $users->company_phone }}</b></td>
                            <td><b class="text-info">{{ $users->company_address." ".$users->company_address2." ".$users->company_city." ".$users->state." ".$users->country }}</b></td>
                            <td><b class="text-info">{{ $users->primary_bussiness_type  }}</b></td>
                            <td><b class="text-info">{{ $users->estimate_annual_amazon_revenue }}</b></td>
                            <td><b class="text-info">{{ $users->estimate_annual_fba_order }}</b></td>
                            <td><b class="text-info">{{ $users->reference_from  }}</b></td>
                            <td>
                                @if($users->status=='1')
                                <a href="switchuser/{{$users->user_id}}/0">Switch User</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <link href="//cdn.datatables.net/1.10.11/css/jquery.dataTables.css" rel="stylesheet">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.11/js/jquery.dataTables.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#list').DataTable({});
        });
    </script>
@endsection