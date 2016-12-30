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
            <div class="Compose-Message">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        Contact Form Details
                    </div>
                    <div class="panel-body">
                        {!! Form::open(['url' =>  '/notify', 'method' => 'post', 'class' => 'form-horizontal', 'id'=>'validate']) !!}
                        <div class="form-group">
                            {!! Form::label('content', 'message', ['class' => 'control-label col-md-2']) !!}
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                    {!! Form::text('content', old('content'), ['class' => 'form-control validate[required]', 'placeholder'=>'Message']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12">
                                {!! Form::submit('Send', ['class'=>'btn btn-primary pull-right']) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}
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
