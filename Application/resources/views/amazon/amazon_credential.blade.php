@extends('layouts.frontend.app')

@section('title', 'Amazon Credential')

@section('css')

@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-head-line">Amazon Credential</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['url' =>  'amazon_credential', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('mws_seller_id', 'MWS Seller Id *', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('mws_seller_id', old('mws_seller_id', !empty($customer_amazon_detail) ? $customer_amazon_detail[0]->mws_seller_id: null), ['class' => 'form-control validate[required]', 'placeholder'=>'MWS Seller Id']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('mws_market_place_id', 'MWS Market Place Id *', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                              <select name="mws_market_place_id" class="form-control select2 validate[required]">
                                    <option value="">Marketplace</option>
                                    @foreach ($marketplace as $marketplace)
                                        @if(!empty($customer_amazon_detail))
                                        <option value="{{ $marketplace->id }}" @if($marketplace->id==$customer_amazon_detail[0]->mws_market_place_id) {{ "selected"  }} @endif>  {{ $marketplace->market_place_name }}</option>
                                        @else
                                            <option value="{{ $marketplace->id }}">  {{ $marketplace->market_place_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('mws_authtoken', 'MWS Authtoken *', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('mws_authtoken', old('mws_authtoken', !empty($customer_amazon_detail) ? $customer_amazon_detail[0]->mws_authtoken: null), ['class' => 'form-control validate[required]', 'placeholder'=>'MWS Authtoken']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            {!! Form::submit((!empty($customer_amazon_detail)?'Update': 'Add'). ' Amazon Credential', ['class'=>'btn btn-primary']) !!}
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
