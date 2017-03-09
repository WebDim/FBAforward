@extends('layouts.admin.app')

@section('title', 'Shipping Method')

@section('css')
    <!-- iCheck for checkboxes and radio inputs -->
    {!! Html::style('assets/plugins/iCheck/all.css') !!}
@endsection


@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-star"></i> {{ isset($shipping_method) ? 'Edit' : 'Add' }} Shipping Method
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ url('admin/shippingmethod') }}"><i class="fa fa-star"></i> Shipping Method</a></li>
            <li class="active"><i
                        class="fa {{ isset($shipping_method) ? 'fa-pencil' : 'fa-plus' }}"></i> {{ isset($shipping_method) ? 'Edit' : 'Add' }}
                Shipping Method
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Shipping Method Details Form</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i
                                class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open(['url' =>  isset($shipping_method) ? 'admin/shippingmethod/'.$shipping_method->shipping_method_id  :  'admin/shippingmethod', 'method' => isset($shipping_method) ? 'put' : 'post', 'class' => 'form-horizontal', 'id'=>'validate']) !!}
                {!! Form::hidden('method_id', isset($shipping_method) ? $shipping_method->shipping_method_id: null) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! htmlspecialchars_decode(Form::label('name', 'Name <span class="required">*</span>', ['class' => 'control-label col-md-3'])) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-star"></i></span>
                                    {!! Form::text('name', old('name', isset($shipping_method) ? $shipping_method->shipping_name: null), ['class' => 'form-control validate[required]', 'placeholder'=>'Name']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! htmlspecialchars_decode(Form::label('port_fee', 'Port Fee <span class="required">*</span>', ['class' => 'control-label col-md-3'])) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-star"></i></span>
                                    {!! Form::text('port_fee', old('port_fee', isset($shipping_method) ? $shipping_method->port_fee: null), ['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Port Fee']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! htmlspecialchars_decode(Form::label('custom_brokrage', 'Custom Brokrage <span class="required">*</span>', ['class' => 'control-label col-md-3'])) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-star"></i></span>
                                    {!! Form::text('custom_brokrage', old('custom_brokrage', isset($shipping_method) ? $shipping_method->custom_brokrage: null), ['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Custom Brokrage']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! htmlspecialchars_decode(Form::label('consulting_fee', 'Consulting Fee <span class="required">*</span>', ['class' => 'control-label col-md-3'])) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-star"></i></span>
                                    {!! Form::text('consulting_fee', old('consulting_fee', isset($shipping_method) ? $shipping_method->consulting_fee: null), ['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Consulting Fee']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                {!! Form::submit((isset($shipping_method)?'Update': 'Add'). ' Shipping Method', ['class'=>'btn btn-primary']) !!}
                                <a class="btn btn-default btn-close" href="{{ '../' }}">Cancel</a>
                            </div>
                        </div>
                    </div><!-- .col-md-6 -->
                </div><!-- .row -->
                {!! Form::close() !!}
            </div><!-- /.box-body -->
            <div class="box-footer">
            </div><!-- /.box-footer-->
        </div><!-- /.box -->
    </section><!-- /.content -->
@endsection


@section('js')
    <!-- iCheck 1.0.1 -->
    {!! Html::script('assets/plugins/iCheck/icheck.min.js') !!}

    {!! Html::script('assets/plugins/validationengine/languages/jquery.validationEngine-en.js') !!}

    {!! Html::script('assets/plugins/validationengine/jquery.validationEngine.js') !!}

    <script type="text/javascript">
        $(document).ready(function () {

            $('input[type="checkbox"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });

            //Initialize Select2 Elements
            $(".select2").select2({
                placeholder: "Please Select",
                allowClear: true
            });

            // Validation Engine init
            var prefix = 's2id_';
            $("form[id^='validate']").validationEngine('attach',
                {
                    promptPosition: "bottomRight", scroll: false,
                    prettySelect: true,
                    usePrefix: prefix
                });
        });
    </script>
@endsection
