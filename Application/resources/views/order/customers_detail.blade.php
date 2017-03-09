@extends('layouts.frontend.app')
@section('title', $title)
@section('css')
    <style type="text/css">
        .margin-bottom {
            margin-bottom: 5px;
        }
    </style>
    {!! Html::style('assets/dist/css/datatable/dataTables.bootstrap.min.css') !!}
    {!! Html::style('assets/dist/css/datatable/responsive.bootstrap.min.css') !!}
    {!! Html::style('assets/dist/css/datatable/dataTablesCustom.css') !!}
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
                                @if($user_role_id==4 || $user_role_id==9)
                                    @if($users->status=='1')
                                    <a href="switchuser/{{$users->user_id}}/0">Switch User</a>
                                    @endif
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
    {!! Html::script('assets/dist/js/datatable/jquery.dataTables.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/dataTables.bootstrap.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/dataTables.responsive.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/responsive.bootstrap.min.js') !!}
    <script type="text/javascript">
        $(document).ready(function() {
            $('#list').DataTable({});
        });
    </script>
@endsection