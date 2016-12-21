@extends('layouts.frontend.app')

@section('title', 'Forgot Password')

@section('css')

@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h4> Forgot Your Password don't worry with <strong>{{ getSetting('SITE_TITLE') }} :</strong></h4>
            <br/>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                {!! csrf_field() !!}

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="col-md-3 control-label">E-Mail Address *</label>

                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                        </div>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-btn fa-envelope"></i> Send Password Reset Link
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <div class="alert alert-info">
                <p>Laraship Pro is the first User Membership software built on Laravel that Provides Complete
                    Management to any Subscription Site Including :


                <ul>
                    <li>User Management</li>
                    <li>Subscription Management</li>
                    <li>Role Management</li>
                    <li>Features Management</li>
                    <li>Package Management</li>
                    <li>Setting Management</li>
                    <li>Content Management</li>
                </ul>
                </p>
            </div>
            <div class="alert alert-success">
                <strong> Instructions To Install:</strong>
                <ul>
                    <li>
                        Please Navigation to Instructions Manual at root folder for detailed installations and usage
                        steps
                    </li>

                </ul>

            </div>
        </div>
    </div>
@endsection
