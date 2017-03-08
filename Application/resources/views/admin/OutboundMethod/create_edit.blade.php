@extends('layouts.admin.app')

@section('title', 'Outbound Method')

@section('css')
    <!-- iCheck for checkboxes and radio inputs -->
    {!! Html::style('assets/plugins/iCheck/all.css') !!}
@endsection


@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-star"></i> {{ isset($outbound_method) ? 'Edit' : 'Add' }} Outbound Method
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ url('admin/outboundmethod') }}"><i class="fa fa-star"></i> Outbound Method</a></li>
            <li class="active"><i
                        class="fa {{ isset($outbound_method) ? 'fa-pencil' : 'fa-plus' }}"></i> {{ isset($outbound_method) ? 'Edit' : 'Add' }}
                Outbound Method
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Outbound Method Details Form</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i
                                class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open(['url' =>  isset($outbound_method) ? 'admin/outboundmethod/'.$outbound_method->outbound_method_id  :  'admin/outboundmethod', 'method' => isset($outbound_method) ? 'put' : 'post', 'class' => 'form-horizontal', 'id'=>'validate']) !!}
                {!! Form::hidden('method_id', isset($outbound_method) ? $outbound_method->outbound_method_id: null) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! htmlspecialchars_decode(Form::label('outbound_name', 'Outbound Name <span class="required">*</span>', ['class' => 'control-label col-md-3'])) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-star"></i></span>
                                    {!! Form::text('outbound_name', old('outbound_name', isset($outbound_method) ? $outbound_method->outbound_name: null), ['class' => 'form-control validate[required]', 'placeholder'=>'Outbound Name']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                {!! Form::submit((isset($outbound_method) ? 'Update' : 'Add'). ' Outbound Method', ['class'=>'btn btn-primary']) !!}
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
