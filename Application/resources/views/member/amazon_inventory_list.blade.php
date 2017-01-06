@extends('layouts.frontend.app')

@section('title', 'Amazon Inventory List')

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
            <h2 class="page-head-line">Inventory List</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
               <div class="table-responsive no-padding">
                    <table class="table" id="list">
                        <thead>
                        <tr>
                            <td><span>Condition</span></td>
                            <td><span>Total Supply Quantity</span></td>
                            <td><span>FNSKU</span></td>
                            <td><span>In Stock Supply Quantity</span></td>
                            <td><span>ASIN</span></td>
                            <td><span>SellerSKU</span></td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($inventory_list as $list)
                        <tr>
                            <td><b class="text-info">{{ $list->condition }}</b></td>
                            <td><b class="text-info">{{ $list->total_supply_quantity  }}</b></td>
                            <td><b class="text-info">{{ $list->FNSKU }}</b></td>
                            <td><b class="text-info">{{ $list->instock_supply_quantity }}</b></td>
                            <td><b class="text-info">{{ $list->ASIN  }}</b></td>
                            <td><b class="text-info">{{ $list->sellerSKU  }}</b></td>
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
