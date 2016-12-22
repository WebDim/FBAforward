@extends('layouts.frontend.app')

@section('title', 'Edit Profile')

@section('css')

@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-head-line">Update Profile</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' =>  'member/profile/edit', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
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
                        {!! Form::label('email', 'Email', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                {!! Form::email('', $user->email, ['class' => 'form-control', 'disabled']) !!}
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
                        <div class="col-md-9 col-md-offset-3" style="margin-top: 10px;">
							<span class="btn  btn-file  btn-primary">Upload Avatar
                                {!! Form::file('avatar') !!}
							</span>
                        </div>
                    </div>
                </div><!-- .col-md-6 -->
            </div><!-- .row -->
            <div class="row">
                <div class="col-md-6">
                   {{-- <div class="form-group">
                        {!! Form::label('mobile', 'Mobile', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                                {!! Form::text('mobile', old('mobile', $user->mobile), ['class' => 'form-control', 'placeholder'=>'Mobile']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('job_title', 'Job Title', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            {!! Form::select('job_title', $job_titles, old('job_title', $user->job_title), ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('address', 'Address *', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            {!! Form::text('address', old('address', $user->address), ['class' => 'form-control validate[required]', 'placeholder'=>'Address']) !!}
                        </div>
                    </div>--}}
                    @foreach($user_info as $user_info)
                    <div class="form-group">
                        {!! Form::label('company_name', 'Company Name *', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('company_name', old('company_name', $user_info->company_name), ['class' => 'form-control', 'placeholder'=>'Company Name']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('contact_fname', 'Contact First Name *', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                               <span class="input-group-addon"></span>
                               {!! Form::text('contact_fname', old('contact_fname', $user_info->contact_fname), ['class' => 'form-control', 'placeholder'=>'Contact First Name']) !!}
                            </div>
                        </div>
                    </div>
                        <div class="form-group">
                            {!! Form::label('contact_lname', 'Contact Last Name *', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('contact_lname', old('contact_lname', $user_info->contact_lname), ['class' => 'form-control', 'placeholder'=>'Contact Last Name']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_address', 'Street Address *', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('company_address', old('company_address', $user_info->company_address), ['class' => 'form-control', 'placeholder'=>'Street Address']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_address2', 'Address Line 2', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('company_address2', old('company_address2', $user_info->company_address2), ['class' => 'form-control', 'placeholder'=>'Address Line 2']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('company_phone', 'Phone *', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('company_phone', old('company_phone', $user_info->company_phone), ['class' => 'form-control', 'placeholder'=>'Phone']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('business_type', 'Primary Business Type *', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"></span>
                                    {!! Form::text('business_type', old('business_type', $user_info->primary_bussiness_type), ['class' => 'form-control', 'placeholder'=>'Primary Business Type']) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            {!! Form::submit('Update Profile', ['class'=>'btn btn-primary']) !!}
                        </div>
                    </div>
                </div><!-- .col-md-6 -->
            </div><!-- .row -->
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('js')
    {!! Html::script('assets/plugins/validationengine/languages/jquery.validationEngine-en.js') !!}

    {!! Html::script('assets/plugins/validationengine/jquery.validationEngine.js') !!}
    <script type="text/javascript">
        $(document).ready(function () {
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
