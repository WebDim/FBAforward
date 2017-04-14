@extends('layouts.frontend.app')
@section('title', $title)
@section('css')
    <style type="text/css">
        .margin-bottom {
            margin-bottom: 5px;
        }
    </style>
    <!-- DataTables -->
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
            <div class="box-body">
                <table class="table datatable dt-responsive" id="list">
                    <thead>
                    <tr>
                        <th><span>Company Name</span></th>
                        <th><span>Company Phone</span></th>
                        <th><span>Company Address</span></th>
                        <th><span>Primary Amazon Business Type</span></th>
                        <th><span>Contact Name</span></th>
                        <th><span>Contact Email Address</span></th>
                        <th><span>Contact Phone Number</span></th>
                        <th><span>Secondary Email Contact</span></th>
                        <th><span>Accounts Payable Contact Name</span></th>
                        <th><span>Accounts Payable Contact Email</span></th>
                        <th><span>Accounts Payable Contact Phone Number</span></th>
                        <th><span>Username</span></th>
                        <th><span>Tax ID Numebr</span></th>
                        <th><span>Amazon Revenue<br>(Estimate Annual)</span></th>
                        <th><span>FBA Order<br>(Estimate Annual)</span></th>
                        <th><span>Referred By</span></th>
                        <th><span>Action</span></th>
                       {{-- @if($user_role_id==4 || $user_role_id==9)
                        <th><span>Action</span></th>
                        @endif --}}
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <!-- DataTables -->
    {!! Html::script('assets/dist/js/datatable/jquery.dataTables.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/dataTables.bootstrap.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/dataTables.responsive.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/responsive.bootstrap.min.js') !!}
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $("#list").DataTable({
                processing: true,
                serverSide: true,
                ajax: ({
                    headers: {
                        'X-CSRF-Token': '{{ csrf_token() }}',
                    },
                    url:'/order/customers',
                    type:'post',
                    data:{
                    }
                }),
                columns: [
                    { data: "company_name" },
                    { data: "company_phone"},
                    { data: "company_address" },
                    { data: "primary_bussiness_type" },
                    { data: "contact_fname" },
                    { data: "contact_email" },
                    { data: "contact_phone" },
                    { data: "secondary_contact_email" },
                    { data: "account_payable" },
                    { data: "account_email" },
                    { data: "account_phone" },
                    { data: "email"},
                    { data: "tax_id_number"},
                    { data: "estimate_annual_amazon_revenue"},
                    { data: "estimate_annual_fba_order"},
                    { data: "reference_from"},
                    { data: "Action"}
                ],
            });
        });
        function storeuser(user_id) {
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/member/storeuser', // This is the url we gave in the route
                data: {
                    'user_id': user_id
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('.preloader').css("display", "none");
                    window.location.assign('{{url("/member/switchuser")}}');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('.preloader').css("display", "none");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
    </script>
@endsection