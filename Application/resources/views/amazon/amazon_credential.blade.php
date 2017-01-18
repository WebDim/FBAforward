@extends('layouts.frontend.app')

@section('title', 'Amazon Credential')

@section('css')

@endsection

@section('content')
     <div class="row">
         <h2 class="page-head-line">Amazon AWS Credentials</h2>
        <div class="col-md-12">
            <div>
                We need your Amazon MWS credentials to get info from Amazon. To provide them you can check
                <a href="https://developer.amazonservices.com/gp/mws/faq.html/189-9202934-4447748#accessToDeveloper">Amazon Documentation</a>
                or
                <a onclick="amazon_instruction()">follow instructions</a>
            </div>
            {!! Form::open(['url' =>  'amazon_credential', 'method' => 'put', 'files' => true, 'class' => 'form-horizontal', 'id'=>'validate']) !!}
                   <div class="form-group">
                        {!! Form::label('mws_seller_id', 'Seller ID *') !!}
                          <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('mws_seller_id', old('mws_seller_id', !empty($customer_amazon_detail) ? decrypt($customer_amazon_detail[0]->mws_seller_id): null), ['class' => 'form-control validate[required]', 'placeholder'=>'MWS Seller Id']) !!}
                            </div>

                    </div>
                    <div class="form-group">
                        {!! Form::label('mws_authtoken', 'MWS Authtoken *') !!}

                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                {!! Form::text('mws_authtoken', old('mws_authtoken', !empty($customer_amazon_detail) ? decrypt($customer_amazon_detail[0]->mws_authtoken): null), ['class' => 'form-control validate[required]', 'placeholder'=>'MWS Authtoken']) !!}
                            </div>

                    </div>
                    <div class="form-group">
                        {!! Form::label('mws_market_place_id', 'Marketplace ID *') !!}

                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                <select name="mws_market_place_id" class="form-control select2 validate[required]">
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
                    <div class="form-group">
                        <div >
                            {!! Form::submit((!empty($customer_amazon_detail)?'Update': 'Add'). ' Amazon Credential', ['class'=>'btn btn-primary']) !!}
                            {{ Form::reset('Cancel', ['class'=>'btn btn-warning']) }}
                        </div>
                    </div>

            {!! Form::close() !!}
        </div>
    </div>
     <div class="modal fade" id="amazoninstruction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                     <h4 class="modal-title" id="myModalLabel">Amazon account integration instructions</h4>
                 </div>
                 <div class="modal-body">
                     <p class="view-result"></p><div class="alert alert-success">
                         <ol>
                             <li>Go to <a href="http://developer.amazonservices.com" target="_blank">http://developer.amazonservices.com</a></li>
                             <li>Click the <strong>Sign up for MWS</strong> button.</li>
                             <li>Log in to your Amazon seller account.</li>
                             <li>On the MWS registration page, click on the button labeled <strong>I want to give a developer access to my Amazon seller account with MWS</strong>.</li>
                             <li>In the <strong>Developer's Name</strong> text box, type in <em>ChannelReply</em>.</li>
                             <li>In the Developer Account Number text box, enter our MWS developer account identifier: <strong>1333-3786-5186</strong></li>
                             <li>Click the <strong>Next</strong> button.</li>
                             <li>Accept the Amazon MWS License Agreements and click the <strong>Next</strong> button.</li>
                             <li>Copy your account identifiers (Seller ID, MWS Authorization Token, and Marketplace ID) as we need them in order to programmatically access your Amazon seller account.</li>
                             <li>Paste your account identifiers into the Amazon Seller ID, Amazon Auth Token and Amazon Marketplace ID fields at the top of this page and click Update.</li>
                         </ol>
                     </div><p></p>
                 </div>
             </div>
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
        function amazon_instruction() {
            $('#amazoninstruction').modal('show');
        }
    </script>
@endsection
