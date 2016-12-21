@extends('layouts.admin.app')

@section('title', 'Packages')

@section('css')
        <!-- iCheck for checkboxes and radio inputs -->
{!! Html::style('assets/plugins/iCheck/all.css') !!}
@endsection


@section('content')
        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-star"></i> {{ isset($package) ? 'Edit' : 'Add' }} Feature
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{ url('admin/packages') }}"><i class="fa fa-briefcase"></i> Packages</a></li>
        <li class="active"><i
                    class="fa {{ isset($package) ? 'fa-pencil' : 'fa-plus' }}"></i> {{ isset($package) ? 'Edit' : 'Add' }}
            Package
        </li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Feature Details Form</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i
                            class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            {!! Form::open(['url' =>  isset($package) ? 'admin/packages/'.$package->id  :  'admin/packages', 'method' => isset($package) ? 'put' : 'post', 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            {!! Form::hidden('package_id', isset($package) ? $package->id: null) !!}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', 'Name *', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-briefcase"></i></span>
                                {!! Form::text('name', old('name', isset($package) ? $package->name: null), ['class' => 'form-control validate[required]', 'placeholder'=>'Package Name']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('cost', 'Cost *', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon"><i
                                            class="fa fa-{{ getSetting('DEFAULT_CURRENCY') }}"></i></span>
                                {!! Form::number('cost', old('cost',  isset($package) ? $package->cost : null), ['class' => 'form-control validate[required,custom[number],min[0]]', 'placeholder'=>'Cost', 'step' => "0.01"]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('cost_per', 'Cost Per *', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            {!! Form::text('cost_per', old('cost_per', isset($package) ? $package->cost_per : null ), ['class' => 'form-control validate[required]', 'placeholder'=>'Cost Per']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('plan', 'Plan *', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            {!! Form::text('plan', old('plan', isset($package) ? $package->plan : null ), ['class' => 'form-control validate[required]', 'placeholder'=>'Plan']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('status', 'Status', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-sm-8">
                            <label class="check">{!! Form::checkbox('status',1,  old('status' , (isset($package) && ($package->getOriginal('status') == 1) ) ? true : false ) ,['class'=>'minimal']) !!}
                                Active</label>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('featured', 'Featured', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-sm-8">
                            <label class="check">{!! Form::checkbox('featured',1,  old('featured' , (isset($package) && ($package->getOriginal('featured') == 1) ) ? true : false ) ,['class'=>'minimal']) !!}
                                Featured</label>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('pricing_order', 'Pricing Order *', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            {!! Form::number('pricing_order', old('pricing_order',   isset($package) ? $package->pricing_order : null ), ['class' => 'form-control validate[required,custom[integer],min[1]]', 'placeholder'=>'Pricing Order']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            {!! Form::submit((isset($package)?'Update': 'Add'). ' Package', ['class'=>'btn btn-primary']) !!}
                        </div>
                    </div>
                </div><!-- .col-md-6 -->
                <div class="col-md-6">
                    <h4>Package Features List</h4>
                    @foreach($features as $feature)
                        <div class="form-group">
                            <div class="col-md-1">
                                {!! Form::checkbox('features[]', $feature->id, (isset($package) && ($package->features->contains($feature->id)) ) ? true : false, ['class'=>'minimal']) !!}
                            </div>
                            {!! Form::label('features', $feature->name, ['class' => 'col-md-11', 'style'=>'text-align:left;']) !!}
                            <div class="col-md-6">
                                {!! Form::text('feature_'. $feature->id , old('feature_spec', (isset($package)&& $package->features->contains($feature->id)) ? $package->features()->find($feature->id)->pivot->spec : null ), ['class' => 'form-control', 'placeholder'=>'Spec']) !!}
                            </div>
                        </div>
                    @endforeach
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
