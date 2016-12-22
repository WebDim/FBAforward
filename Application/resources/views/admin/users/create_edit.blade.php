@extends('layouts.admin.app')

@section('title', 'Users')

@section('css')

@endsection


@section('content')
        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-user"></i> {{ isset($user) ? 'Edit' : 'Add' }} User
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{ url('admin/users') }}"><i class="fa fa-users"></i> Users</a></li>
        <li class="active"><i
                    class="fa {{ isset($user) ? 'fa-pencil' : 'fa-plus' }}"></i> {{ isset($user) ? 'Edit' : 'Add' }}
            User
        </li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">User Details Form</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i
                            class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            {!! Form::open(['url' =>  isset($user) ? 'admin/users/'.$user->id  :  'admin/users', 'method' => isset($user) ? 'put' : 'post', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            {!! Form::hidden('user_id', isset($user) ? $user->id: null) !!}
            <fieldset>
                <legend>Website Login Credentials</legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('name', 'Name *', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    {!! Form::text('name', old('name', isset($user) ? $user->name: null), ['class' => 'form-control validate[required]', 'placeholder'=>'Name']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('email', 'Email *', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                    {!! Form::email('email', old('email', isset($user) ? $user->email: null), ['class' => 'form-control validate[required,custom[email]]', 'placeholder'=>'Email']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('password', 'Password', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    {!! Form::password('password', ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('password_confirmation', 'Confirmation', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    {!! Form::password('password_confirmation', ['class' => isset($user) ? 'form-control validate[equals[password]]': 'form-control validate[required,equals[password]]' ]) !!}
                                </div>
                            </div>
                        </div>
                    </div><!-- .col-md-6 -->

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('role', 'Role *', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                {!! Form::select('role', array_add($roles, '','Please Select'), old('role', isset($user) ? $user->role_id: null), ['class' => 'form-control select2 validate[required]']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('avatar', 'Avatar', ['class' => 'control-label col-md-3']) !!}
                            @if(isset($user) && $user->avatar !="")
                                <div class="col-md-9">
                                    <img src="{{ asset($user->avatar) }}" width="30%" class="img-circle"
                                         alt="User Avatar"/>
                                </div>
                            @else
                                <div class="col-md-9">
                                    <img src="{{ asset('uploads/avatars/avatar.png') }}" width="30%"
                                         class="img-circle" alt="User Avatar"/>
                                </div>
                            @endif
                            <div class="col-md-7 col-md-offset-5" style="margin-top: 10px;">
								<span class="btn  btn-file  btn-primary">Upload Avatar
                                    {!! Form::file('avatar') !!}
								</span>
                            </div>
                        </div>
                    </div><!-- .col-md-6 -->
                </div><!-- .row -->
            </fieldset>
            @foreach($user_info as $user_info)
            <fieldset>
                <legend>Company Information</legend>
                <div class="row">
                    <div class="col-md-6">
                        {{--<div class="form-group">
                            {!! Form::label('mobile', 'Mobile', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                                    {!! Form::text('mobile', old('mobile', isset($user) ? $user->mobile: null), ['class' => 'form-control', 'placeholder'=>'Mobile']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('job_title', 'Job Title', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                {!! Form::select('job_title', array_add($job_titles, '','Please Select'), old('job_title', isset($user) ? $user->job_title: null), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('package_id', 'Package', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                {!! Form::select('package_id', array_add($packages, '','Please Select'), old('package_id', isset($user) && $user->package_id != 0 ? $user->package_id: null), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('address', 'Address *', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                {!! Form::text('address', old('address', isset($user) ? $user->address: null), ['class' => 'form-control validate[required]', 'placeholder'=>'Address']) !!}
                            </div>
                        </div>--}}
                        <div class="form-group">
                            {!! Form::label('company_name', 'Company Name', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('company_name', old('company_name', isset($user_info) ? $user_info->company_name: null), ['class' => 'form-control', 'placeholder'=>'Company Name']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_phone', 'Phone', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                                    {!! Form::text('company_phone', old('company_phone', isset($user_info) ? $user_info->company_phone: null), ['class' => 'form-control', 'placeholder'=>'Phone']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_address', 'Street Address', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('company_address', old('company_address', isset($user_info) ? $user_info->company_address: null), ['class' => 'form-control', 'placeholder'=>'Street Address']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_address2', 'Address Line 2', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('company_address2', old('company_address2', isset($user_info) ? $user_info->company_address2: null), ['class' => 'form-control', 'placeholder'=>'Address Line 2']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_city', 'City', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('company_city', old('company_city', isset($user_info) ? $user_info->company_city: null), ['class' => 'form-control', 'placeholder'=>'City']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_state', 'State', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('company_state', old('company_state', isset($user_info) ? $user_info->company_state: null), ['class' => 'form-control', 'placeholder'=>'State']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_zipcode', 'Zipcode', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('company_zipcode', old('company_zipcode', isset($user_info) ? $user_info->company_zipcode: null), ['class' => 'form-control', 'placeholder'=>'Zipcode']) !!}
                                </div>
                            </div>
                        </div>


                    </div><!-- .col-md-6 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('company_country', 'Country', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('company_country', old('company_country', isset($user_info) ? $user_info->company_country: null), ['class' => 'form-control', 'placeholder'=>'Country']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('tax_id_number', 'Tax ID Number', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('tax_id_number', old('tax_id_number', isset($user_info) ? $user_info->tax_id_number: null), ['class' => 'form-control', 'placeholder'=>'Tax ID Number']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('business_type', 'Primary Business Type', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('business_type', old('business_type', isset($user_info) ? $user_info->primary_bussiness_type: null), ['class' => 'form-control', 'placeholder'=>'Primary Business Type']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('annual_amazon_revenue', 'Estimated Annual Amazon Revenue', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('annual_amazon_revenue', old('annual_amazon_revenue', isset($user_info) ? $user_info->estimate_annual_amazon_revenue: null), ['class' => 'form-control', 'placeholder'=>'Estimated Annual Amazon Revenue']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('annual_fba_order', 'Estimated Annual FBAforward Order', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('annual_fba_order', old('annual_fba_order', isset($user_info) ? $user_info->estimate_annual_fba_order: null), ['class' => 'form-control', 'placeholder'=>'Estimated Annual FBAforward Order']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('reference_from', 'How did you hear about us?', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('reference_from', old('reference_from', isset($user_info) ? $user_info->reference_from: null), ['class' => 'form-control', 'placeholder'=>'How did you hear about us?']) !!}
                                </div>
                            </div>
                        </div>
                    </div><!-- .col-md-6 -->
                </div><!-- .row -->
            </fieldset>
            <fieldset>
                <legend>Main Company Contact</legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('contact_fname', 'Contact First Name', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('contact_fname', old('contact_fname', isset($user_info) ? $user_info->contact_fname: null), ['class' => 'form-control', 'placeholder'=>'Contact First Name']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('contact_lname', 'Contact Last Name', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('contact_lname', old('contact_lname', isset($user_info) ? $user_info->contact_lname: null), ['class' => 'form-control', 'placeholder'=>'Contact Last Name']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('contact_email', 'E-Mail', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('contact_email', old('contact_email', isset($user_info) ? $user_info->contact_email: null), ['class' => 'form-control', 'placeholder'=>'E-mail']) !!}
                                </div>
                            </div>
                        </div>


                    </div><!-- .col-md-6 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('contact_phone', 'Phone', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('contact_phone', old('contact_phone', isset($user_info) ? $user_info->contact_phone: null), ['class' => 'form-control', 'placeholder'=>'Phone']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('secondary_contact_phone', 'Secondary Contact', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('secondary_contact_phone', old('secondary_contact_phone', isset($user_info) ? $user_info->secondary_contact_phone : null), ['class' => 'form-control', 'placeholder'=>'Secondary Contact']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('secondary_contact_email', 'Secondary Conatct E-Mail', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('secondary_contact_email', old('secondary_contact_email', isset($user_info) ? $user_info->secondary_contact_email : null), ['class' => 'form-control', 'placeholder'=>'Secondary Conatct E-Mail']) !!}
                                </div>
                            </div>
                        </div>
                    </div><!-- .col-md-6 -->
                </div><!-- .row -->
            </fieldset>
            <fieldset>
                <legend>Accounting Contact</legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('account_payable', 'Accounts Payable Contact ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('account_payable', old('account_payable', isset($user_info) ? $user_info->account_payable : null), ['class' => 'form-control', 'placeholder'=>'Accounts Payable Contact ']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('account_email', 'E-Mail', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('account_email', old('account_email', isset($user_info) ? $user_info->account_email : null), ['class' => 'form-control', 'placeholder'=>'E-Mail']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-9 col-md-offset-3">
                                {!! Form::submit((isset($user)?'Update': 'Add'). ' User', ['class'=>'btn btn-primary']) !!}
                            </div>
                        </div>
                    </div><!-- .col-md-6 -->
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('account_phone', 'Phone', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('account_phone', old('account_phone', isset($user_info) ? $user_info->account_phone : null), ['class' => 'form-control', 'placeholder'=>'Phone']) !!}
                                </div>
                            </div>
                        </div>

                    </div><!-- .col-md-6 -->
                </div><!-- .row -->
            </fieldset>
            @endforeach
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
