@extends('layouts.frontend.app')

@section('title', 'Register')

@section('css')

@endsection

@section('content')
    <div class="row">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
            {!! csrf_field() !!}
            <div class="col-md-12">
            <h4> Website Login Credentials</h4>
                <hr>
            <br/>
                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Name *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                            <input type="text" placeholder="Name" class="form-control" name="name" value="{{ old('name') }}">
                        </div>
                        @if ($errors->has('name'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">E-Mail Address *</label>
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            <input type="email" placeholder="E-Mail Address" class="form-control" name="email" value="{{ old('email') }}">
                        </div>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Password *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                            <input type="password" placeholder="Password" class="form-control" name="password">
                        </div>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Confirmation *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                            <input type="password" placeholder="Confirmation" class="form-control" name="password_confirmation">
                        </div>
                        @if ($errors->has('password_confirmation'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">User Role *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <select name="role" class="form-control">
                                <option value="">User Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">  {{ $role->name }}</option>
                                @endforeach
                            </select>


                        </div>
                        @if ($errors->has('role'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('role') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <h4> Company Information</h4>
                <hr>
                <br/>
                <div class="form-group{{ $errors->has('company_name') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Company Name *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Company Name" class="form-control" name="company_name" value="{{ old('company_name') }}">
                        </div>
                        @if ($errors->has('company_name'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('company_name') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('company_phone') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Phone *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Phone" class="form-control" name="company_phone" value="{{ old('company_phone') }}">
                        </div>
                        @if ($errors->has('company_phone'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('company_phone') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('company_address') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Address *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Street Address" class="form-control" name="company_address">
                        </div>
                        @if ($errors->has('company_address'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('company_address') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"></label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Address Line 2" class="form-control" name="company_address2">
                        </div>
                    </div>
                </div>
                <div class="form-group{{ $errors->has('company_country') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Country *</label>
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                             <select name="company_country" class="form-control" onchange="changeCountryWiseState(this.value)">
                                <option value="">Select Country</option>
                                @foreach ($country as $key=>$country)
                                    <option value="{{ $key }}">  {{ $country }}</option>
                                @endforeach
                            </select>

                        </div>
                        @if ($errors->has('company_country'))
                            <span class="help-block">
                                <strong>{{ $errors->first('company_country') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div id="select_state">
                    <div class="form-group{{ $errors->has('company_state') ? ' has-error' : '' }}">
                        <label class="col-md-2 control-label">State *</label>
                        <div class="col-md-7">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                <select name="company_state" class="form-control">
                                    <option value="">Select State</option>
                                    @foreach ($states as $key=>$state)
                                        <option value="{{ $key }}">  {{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($errors->has('company_state'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('company_state') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group{{ $errors->has('company_city') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">City *</label>
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="City" class="form-control" name="company_city">
                        </div>
                        @if ($errors->has('company_city'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('company_city') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('company_zipcode') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Zipcode *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Zipcode" class="form-control" name="company_zipcode">
                        </div>
                        @if ($errors->has('company_zipcode'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('company_zipcode') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('tax_id_number') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Tax ID Number *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Tax ID Number" class="form-control" name="tax_id_number">
                        </div>
                        @if ($errors->has('tax_id_number'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('tax_id_number') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('business_type') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Primary Amazon Business Type *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Primary Amazon Business Type" class="form-control" name="business_type">
                        </div>
                        @if ($errors->has('business_type'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('business_type') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('annual_amazon_revenue') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Estimated Annual Amazon Revenue *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Estimated Annual Amazon Revenue" class="form-control" name="annual_amazon_revenue">
                        </div>
                        @if ($errors->has('annual_amazon_revenue'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('annual_amazon_revenue') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('annual_order') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Estimated Annual FBAforward Orders *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Estimated Annual FBAforward Orders" class="form-control" name="annual_order">
                        </div>
                        @if ($errors->has('annual_order'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('annual_order') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('reference') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">How did you hear about us? *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="How did you hear about us?" class="form-control" name="reference">
                        </div>
                        @if ($errors->has('reference'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('reference') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <h4> Main Company Contact</h4>
                <hr>
                <br/>
                <div class="form-group{{ $errors->has('contact_fname') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Contact First Name *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Contact First Name" class="form-control" name="contact_fname" value="{{ old('contact_fname') }}">
                        </div>
                        @if ($errors->has('contact_fname'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('contact_fname') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('contact_lname') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Contact Last Name *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Contact Last Name" class="form-control" name="contact_lname" value="{{ old('contact_lname') }}">
                        </div>
                        @if ($errors->has('contact_lname'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('contact_lname') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('contact_email') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Email *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            <input type="email" placeholder="Email" class="form-control" name="contact_email" value="{{ old('contact_email') }}">
                        </div>
                        @if ($errors->has('contact_email'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('contact_email') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group{{ $errors->has('contact_phone') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Phone *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Phone" class="form-control" name="contact_phone" value="{{ old('contact_phone') }}">
                        </div>
                        @if ($errors->has('contact_phone'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('contact_phone') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Secondary Contact </label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Secondary Contact" class="form-control" name="secondary_contact_phone" value="{{ old('secondary_contact_phone') }}">
                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Secondary Contact Email</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            <input type="email" placeholder="Secondary Contact Email" class="form-control" name="secondary_contact_email" value="{{ old('secondary_contact_email') }}">
                        </div>

                    </div>
                </div>

            </div>
            <div class="col-md-12">
                <h4> Accounting Contact</h4>
                <hr>
                <br/>
                <div class="form-group{{ $errors->has('accounts_payable') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Accounts Payable Contact *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Accounts Payable Contact" class="form-control" name="accounts_payable" value="{{ old('accounts_payable') }}">
                        </div>
                        @if ($errors->has('accounts_payable'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('accounts_payable') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('accounts_email') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">E-Mail *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            <input type="email" placeholder="E-Mail" class="form-control" name="accounts_email" value="{{ old('accounts_email') }}">
                        </div>
                        @if ($errors->has('accounts_email'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('accounts_email') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('accounts_phone') ? ' has-error' : '' }}">
                    <label class="col-md-2 control-label">Phone *</label>

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-addon"></span>
                            <input type="text" placeholder="Phone" class="form-control" name="accounts_phone">
                        </div>
                        @if ($errors->has('accounts_phone'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('accounts_phone') }}</strong>
                                    </span>
                        @endif
                    </div>
                </div>


            </div>

                <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-btn fa-plus"></i> Register
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        function changeCountryWiseState(val){
            $.ajax({
                headers: {
                    'X-CSRF-Token': $('input[name="_token"]').val()
                },
                method: 'POST', // Type of response and matches what we said in the route
                url: '/selectState', // This is the url we gave in the route
                data: {
                    'country_code': val,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $('#select_state').html(response);
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
    </script>
@endsection
