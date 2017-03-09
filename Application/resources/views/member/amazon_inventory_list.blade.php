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
                            <th><span>&nbsp;</span></th>
                            <th><span>Product Name</span></th>
                            <th><span>Nick Name</span></th>
                            {{--<th><span>Condition</span></th>--}}
                            <th><span>Total Supply Quantity</span></th>
                            {{--<th><span>FNSKU</span></th>--}}
                            <th><span>In Stock Supply Quantity</span></th>
                            <th><span>ASIN</span></th>
                            {{--<th><span>SellerSKU</span></th>--}}
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($inventory_list as $list)
                        <tr>
                            <td><img src="{{ ($list->image_path ? $list->image_path : url('/').'/uploads/avatars/avatar.png' ) }}" style="width:500px;height:100px" ></td>
                            <td><b class="text-info">{{ $list->product_name }}</b></td>
                            <td><b class="text-info">{{ $list->product_nick_name }}</b></td>
                            {{--<td><b class="text-info">{{ $list->condition }}</b></td>--}}
                            <td><b class="text-info">{{ $list->total_supply_quantity  }}</b></td>
                            {{--<td><b class="text-info">{{ $list->FNSKU }}</b></td>--}}
                            <td><b class="text-info">{{ $list->instock_supply_quantity }}</b></td>
                            <td><b class="text-info"><a href="https://www.amazon.com/dp/{{ $list->ASIN  }}" target="_blank">{{ $list->ASIN  }}</a></b></td>
                            {{--<td><b class="text-info">{{ $list->sellerSKU  }}</b></td>--}}
                            <td><a onclick="getnickname('{{$list->id}}','{{$list->product_nick_name}}')" class="btn btn-info">@if($list->product_nick_name == '')Create @else Update @endif nick name</a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <div class="modal fade" id="product_nickname" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModalLabel">Product Nick Name</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="nickname_div">
                            {!! Form::open(['url' =>  'member/addnickname', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="form-group" id="">
                                        {!! Form::hidden('id',old('id'), ['id'=>'id']) !!}
                                        {!! Form::label('nickname', 'Product Nick Name ',['class' => 'control-label col-md-5']) !!}
                                        <div class="col-md-7">
                                            <div class="input-group">
                                                 {!! Form::text('nickname', old('nickname'), ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('', '',['class' => 'control-label col-md-5']) !!}
                                        <div class="col-md-7">
                                            <div class="input-group">
                                                {!! Form::submit('  Submit  ', ['class'=>'btn btn-primary',  'id'=>'add']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
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
        function getnickname(id, name) {
            jQuery.noConflict();
            $("#id").val(id);
            $("#nickname").val(name);
            $("#product_nickname").modal('show');
        }
    </script>
@endsection
