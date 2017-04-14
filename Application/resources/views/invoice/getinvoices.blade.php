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
            <div class="row">
           <div class="form-group">
                {!! Form::label('start_date', 'Start Date ', ['class' => 'control-label col-md-3']) !!}
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" name="start_date" id="start_date" class="form-control datepicker" placeholder="Start Date ">
                    </div>
                </div>
           </div>
            <div class="form-group">
                {!! Form::label('end_date', 'End Date ', ['class' => 'control-label col-md-3']) !!}
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" name="end_date" id="end_date" class="form-control datepicker" placeholder="End Date ">
                    </div>
                </div>
           </div>
            </div>
            <div class="row">
            <div class="form-group">
               {!! Form::label('doc_number', 'DocNumber ', ['class' => 'control-label col-md-3']) !!}
               <div class="col-md-3">
                   <div class="input-group">
                       <input type="text" name="doc_number" id="doc_number" class="form-control" placeholder="DocNumber ">
                   </div>
               </div>
            </div>
            <div class="form-group">
               {!! Form::label('customer_name', 'Customer Reference Name', ['class' => 'control-label col-md-3']) !!}
               <div class="col-md-3">
                   <div class="input-group">
                       <input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Customer Reference Name ">
                   </div>
               </div>
            </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
               <div class="col-md-9 col-md-offset-6">
                   <input type="button" name="submit" id="submit" class="btn btn-primary" value="Submit " onclick="get_list()">
               </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
            <table class="table datatable dt-responsive" width="100%" id="list">
                <thead>
                <tr>
                    <th><span>Invoice Id</span></th>
                    <th><span>Order No</span></th>
                    <th><span>SyncToken</span></th>
                    <th><span>Create Time</span></th>
                    <th><span>Last Update Time</span></th>
                    <th><span>Doc Number</span></th>
                    <th><span>TXN Date</span></th>
                    <th><span>Customer Reference Name</span></th>
                    <th><span>Address</span></th>
                    <th><span>LAT</span></th>
                    <th><span>Due Date</span></th>
                    <th><span>Total Amount</span></th>
                    <th><span>Currency Reference Name</span></th>
                    <th><span>Total Taxe</span></th>
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
        $(document).ready(function () {
            start_date=$("#start_date").val();
            end_date=$("#end_date").val();
            doc_number=$("#doc_number").val();
            customer_name=$("#customer_name").val();
            var table = $("#list").DataTable({
                processing: true,
                serverSide: true,
                ajax: ({
                    headers: {
                        'X-CSRF-Token': '{{ csrf_token() }}',
                    },
                    url:'invoice',
                    type:'post',
                    data:{
                        'start_date': start_date,
                        'end_date':end_date,
                        'doc_number':doc_number,
                        'customer_name':customer_name
                    }
                }),
                columns: [
                    { data: "invoice_id" },
                    { data: "order_no"},
                    { data: "synctoken" },
                    { data: "created_time" },
                    { data: "updated_time" },
                    { data: "docnumber" },
                    { data: "txndate" },
                    { data: "customer_ref_name" },
                    { data: "line1" },
                    { data: "lat" },
                    { data: "due_date"},
                    { data: "total_amt"},
                    { data: "currancy_ref_name"},
                    { data: "total_taxe"}
                ],
            });

        });

        function get_list()
        {
            start_date=$("#start_date").val();
            end_date=$("#end_date").val();
            doc_number=$("#doc_number").val();
            customer_name=$("#customer_name").val();
            var table = $("#list").DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                ajax: ({
                    headers: {
                        'X-CSRF-Token': '{{ csrf_token() }}',
                    },
                    url:'invoice',
                    type:'post',
                    data:{
                        'start_date': start_date,
                        'end_date':end_date,
                        'doc_number':doc_number,
                        'customer_name':customer_name
                    }
                }),
                columns: [
                    { data: "invoice_id" },
                    { data: "order_no" },
                    { data: "synctoken" },
                    { data: "created_time" },
                    { data: "updated_time" },
                    { data: "docnumber" },
                    { data: "txndate" },
                    { data: "customer_ref_name" },
                    { data: "line1" },
                    { data: "lat" },
                    { data: "due_date"},
                    { data: "total_amt"},
                    { data: "currancy_ref_name"},
                    { data: "total_taxe"}
                ],
            });
        }
    </script>
@endsection