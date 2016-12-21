@extends('layouts.frontend.app')

@section('title', 'Contact Us')

@section('css')

@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h4 class="page-head-line">Contact Us</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div>
                <h3>Address</h3>
                {!!  getSetting('ADDRESS') !!}
            </div>
            <div id="googleMap" style="width:100%;height:380px;"></div>
        </div>
        <div class="col-md-6">
            <div class="Compose-Message">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        Contact Form Details
                    </div>
                    <div class="panel-body">
                        {!! Form::open(['url' =>  '/contact-us', 'method' => 'post', 'class' => 'form-horizontal', 'id'=>'validate']) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Name', ['class' => 'control-label col-md-2']) !!}
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    {!! Form::text('name', old('name'), ['class' => 'form-control validate[required]', 'placeholder'=>'Name']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('email', 'Email', ['class' => 'control-label col-md-2']) !!}
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                    {!! Form::email('email', old('email'), ['class' => 'form-control  validate[required,custom[email]]', 'placeholder'=>'Email']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('subject', 'Subject', ['class' => 'control-label col-md-2']) !!}
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                                    {!! Form::text('subject', old('subject'), ['class' => 'form-control  validate[required]', 'placeholder'=>'Subject']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('message', 'Message', ['class' => 'control-label col-md-2']) !!}
                            <div class="col-md-10">
                                {!! Form::textarea('message', old('message'), ['class' => 'form-control  validate[required]', 'rows'=> 5]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                {!! Form::submit('Send', ['class'=>'btn btn-primary pull-right']) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                    <div class="panel-footer text-muted">
                        <strong>Note : </strong>Please note that we track all messages so don't send any spams.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')

    {!! Html::script('http://maps.googleapis.com/maps/api/js') !!}
    {!! Html::script('assets/plugins/validationengine/languages/jquery.validationEngine-en.js') !!}
    {!! Html::script('assets/plugins/validationengine/jquery.validationEngine.js') !!}

    <script type="text/javascript">
        function initialize() {
            var mapProp = {
                center: new google.maps.LatLng( {{ getSetting('MAP_LATITUDE') }}, {{ getSetting('MAP_LONGITUDE') }}),
                zoom: 5,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
        }
        google.maps.event.addDomListener(window, 'load', initialize);

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
