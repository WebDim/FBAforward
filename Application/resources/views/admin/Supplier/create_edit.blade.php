@extends('layouts.admin.app')

@section('title', 'Suppliers')

@section('css')
    <!-- iCheck for checkboxes and radio inputs -->
    {!! Html::style('assets/plugins/iCheck/all.css') !!}
@endsection


@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-star"></i> {{ isset($shipping_method) ? 'Edit' : 'Add' }} Suppliers
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ url('admin/suppliers') }}"><i class="fa fa-star"></i> Suppliers</a></li>
            <li class="active"><i
                        class="fa {{ isset($supplier) ? 'fa-pencil' : 'fa-plus' }}"></i> {{ isset($supplier) ? 'Edit' : 'Add' }}
                Suppliers
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Suppliers Details Form</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i
                                class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open(['url' =>  isset($supplier) ? 'admin/suppliers/'.$supplier->supplier_id  :  'admin/suppliers', 'method' => isset($supplier) ? 'put' : 'post', 'class' => 'form-horizontal', 'id'=>'validate']) !!}
                {!! Form::hidden('supplier_id', isset($supplier) ? $supplier->supplier_id: null) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('company_name', 'Company Name *', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-star"></i></span>
                                    {!! Form::text('company_name', old('company_name', isset($supplier) ? $supplier->company_name: null), ['class' => 'form-control validate[required]', 'placeholder'=>'Company Name']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('contact_name', 'Contact Name *', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-star"></i></span>
                                    {!! Form::text('contact_name', old('contact_name', isset($supplier) ? $supplier->contact_name: null), ['class' => 'form-control validate[required]', 'placeholder'=>'Contact Name']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('email', 'Email *', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-star"></i></span>
                                    {!! Form::text('email', old('email', isset($supplier) ? $supplier->email: null), ['class' => 'form-control validate[required, custom[email]]', 'placeholder'=>'Email']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('phone_number', 'Phone Number *', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-star"></i></span>
                                    {!! Form::text('phone_number', old('phone_number', isset($supplier) ? $supplier->phone_number: null), ['class' => 'form-control validate[required, custom[integer, maxSize[10]]]', 'placeholder'=>'Phone Number']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                {!! Form::submit((isset($supplier)?'Update': 'Add'). ' Supplier', ['class'=>'btn btn-primary']) !!}
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
