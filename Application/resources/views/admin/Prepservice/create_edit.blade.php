@extends('layouts.admin.app')

@section('title', 'Prep Services')

@section('css')
    <!-- iCheck for checkboxes and radio inputs -->
    {!! Html::style('assets/plugins/iCheck/all.css') !!}
@endsection


@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-star"></i> {{ isset($prep_service) ? 'Edit' : 'Add' }} Prep Services
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ url('admin/prepservices') }}"><i class="fa fa-star"></i> Prep Services</a></li>
            <li class="active"><i
                        class="fa {{ isset($prep_service) ? 'fa-pencil' : 'fa-plus' }}"></i> {{ isset($prep_service) ? 'Edit' : 'Add' }}
                Prep Service
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Prep Service Details Form</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i
                                class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open(['url' =>  isset($prep_service) ? 'admin/prepservices/'.$prep_service->prep_service_id  :  'admin/prepservices', 'method' => isset($prep_service) ? 'put' : 'post', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
                {!! Form::hidden('prep_id', isset($prep_service) ? $prep_service->prep_service_id: null) !!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! htmlspecialchars_decode(Form::label('service_name', 'Service Name <span class="required">*</span>', ['class' => 'control-label col-md-3'])) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-star"></i></span>
                                    {!! Form::text('service_name', old('service_name', isset($prep_service) ? $prep_service->service_name: null), ['class' => 'form-control validate[required]', 'placeholder'=>'Service Name']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! htmlspecialchars_decode(Form::label('price', 'Price <span class="required">*</span>', ['class' => 'control-label col-md-3'])) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-star"></i></span>
                                    {!! Form::text('price', old('price', isset($prep_service) ? $prep_service->price: null), ['class' => 'form-control validate[required, custom[number]]', 'placeholder'=>'Price']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('service_image', 'Image ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
								<span class="btn  btn-file  btn-primary">Upload Image
                                    {!! Form::file('service_image') !!}
								</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! htmlspecialchars_decode(Form::label('description', 'Description <span class="required">*</span>', ['class' => 'control-label col-md-3'])) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-star"></i></span>
                                    {!! Form::textarea('description',isset($prep_service) ? $prep_service->description: null,['class'=>'form-control validate[required]', 'rows' => 2, 'cols' => 40]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('important_info', 'Important Information ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-star"></i></span>
                                    {!! Form::textarea('important_info',isset($prep_service) ? $prep_service->important_information: null,['class'=>'form-control', 'rows' => 2, 'cols' => 40]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                {!! Form::submit((isset($prep_service)?'Update': 'Add'). ' Prep Services', ['class'=>'btn btn-primary']) !!}
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
