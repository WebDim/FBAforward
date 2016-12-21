@extends('layouts.frontend.app')

@section('title', 'Login')

@section('css')

@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h4> Login with <strong>{{ getSetting('SITE_TITLE') }} Account :</strong></h4>
            <br/>
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
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

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label class="col-md-3 control-label">Password *</label>

                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                            <input type="password" class="form-control" name="password">
                        </div>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-btn fa-sign-in"></i> Login
                        </button>

                        <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <div class="alert alert-info">
                <p>Laraship Pro is the first user membership software built on Laravel that provides complete
                    management to any subscription site Including :


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
                        Please Navigation to Instructions Manual at root folder for delailed installations and usage
                        steps
                    </li>

                </ul>

            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">

    </script>
@endsection
