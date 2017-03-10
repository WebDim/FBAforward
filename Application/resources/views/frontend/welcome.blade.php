@extends('layouts.frontend.single')

@section('title', 'Welcome to '.getSetting('SITE_TITLE'))

@section('css')
    {!! Html::style('assets/plugins/pricingTable/pricingTable.min.css') !!}
    <style type="text/css">
        ul.vpt_plan > li {
            font-family: Sans-Serif;
        }
    </style>
@endsection

@section('content')
    <!-- start home -->
    <section id="home">
        <div class="overlay">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1 wow fadeIn" data-wow-delay="0.3s">
                        <h1 class="text-upper">Welcome to {{ getSetting('SITE_TITLE') }}</h1>
                        <p class="tm-white"></p>
                    <!-- <img src="{{ asset('assets/dist/img/software-img-admin.png') }}" class="img-responsive"
                             alt="home img"> -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end home -->
    <!-- start divider -->
    <!-- <section id="divider">
         <div class="container">
             <div class="row">
                 <div class="col-md-4 wow fadeInUp templatemo-box" data-wow-delay="0.3s">
                     <i class="fa fa-laptop"></i>
                     <h3 class="text-uppercase"></h3>
                     <p></p>
                 </div>
                 <div class="col-md-4 wow fadeInUp templatemo-box" data-wow-delay="0.3s">
                     <i class="fa fa-twitter"></i>
                     <h3 class="text-uppercase"></h3>
                     <p></p>
                 </div>
                 <div class="col-md-4 wow fadeInUp templatemo-box" data-wow-delay="0.3s">
                     <i class="fa fa-desktop"></i>
                     <h3 class="text-uppercase"></h3>
                     <p></p>
                 </div>
             </div>
         </div>
     </section> -->
    <!-- end divider -->
    <!-- start feature -->
    <!-- <section id="feature">
        <div class="container">
            <div class="row">
                <div class="col-md-6 wow fadeInLeft" data-wow-delay="0.6s">
                    <h2 class="text-uppercase"></h2>
                    <p>
                    <ul>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                    </p>
                    <i class="fa fa-code"></i>
                </div>
                <div class="col-md-6 wow fadeInRight" data-wow-delay="0.6s">
                    <img src="{{ asset('assets/dist/img/software-img-admin.png') }}" class="img-responsive"
                         alt="feature img">
                </div>
            </div>
        </div>
    </section> -->
    <!-- end feature -->
    <!-- start feature1 -->
    <!-- <section id="feature1">
        <div class="container">
            <div class="row">
                <div class="col-md-6 wow fadeInUp" data-wow-delay="0.6s">
                    <img src="{{ asset('assets/dist/img/software-img.png') }}" class="img-responsive" alt="feature img">
                </div>
                <div class="col-md-6 wow fadeInUp" data-wow-delay="0.6s">
                    <h2 class="text-uppercase"></h2>
                    <p></p>
                    <a href="http://codecanyon.net/item/laraship-laravel-membership-administration-/15650201"
                       class="btn btn-primary text-uppercase"><i class="fa fa-download"></i> </a>
                </div>
            </div>
        </div>
    </section> -->
    <!-- end feature1 -->
    @if(Auth::guest())
        <!-- start pricing -->
        <!-- <section id="pricing">
        <div class="container">
            <div class="row">
                <div class="col-md-12 wow bounceIn">
                    <h2 class="text-uppercase">Our Pricing</h2>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        @foreach($packages as $package)
            <div class="vpt_plan-container col-md-3 no-margin {{ $package->featured }}">
                                <ul class="vpt_plan drop-shadow {{ $package->featured=='featured'?'bootstrap-vtp-orange':'bootstrap-vpt-green' }} hover-animate-position {{ $package->featured }}">
                                    <li class="vpt_plan-name"><strong>{{ $package->name }}</strong></li>
                                    <li class="vpt_plan-price"><span class="vpt_year"><i
                                                    class="fa fa-{{ getSetting('DEFAULT_CURRENCY') }}"></i></span>{{ $package->cost }}
                    <span
                            class="vpt_year"
                                style="vertical-align:bottom">{{ $package->cost_per }}</span></li>
                                    <li class="vpt_plan-footer"><a href="{{ url('/register') }}" class="pricing-select">Subscribe
                                            Now</a></li>
                                    <?php $i = 1; ?>
            @foreach($package->features as $feature)
                @if($feature->isActive())
                    @if($i%2)
                        <li>{{ $feature->pivot->spec }}</li>
                                            @else
                        <li class="vptbg">{{ $feature->pivot->spec  }}</li>
                                            @endif
                    <?php $i++; ?>
                @endif
            @endforeach
                    </ul>
                </div>
            @endforeach
                </div>
            </div>
        </div>
    </div>
</section> -->
        <!-- end pricing -->
    @endif
    <!-- start contact -->
    <section id="contact">
        <div class="overlay">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 wow fadeInUp" data-wow-delay="0.6s">
                        <h2 class="text-uppercase">Contact Us</h2>
                        <h4>Address</h4>
                        {!!  getSetting('ADDRESS') !!}
                    </div>
                    <div class="col-md-6 wow fadeInUp" data-wow-delay="0.6s">
                        <div class="contact-form">
                            {!! Form::open(['url' =>  '/contact-us', 'method' => 'post', 'class' => 'form-horizontal', 'id'=>'validate']) !!}
                            <div class="col-md-6">
                                {!! Form::text('name', old('name'), ['class' => 'form-control validate[required]', 'placeholder'=>'Name*', 'id'=>'name']) !!}
                            </div>
                            <div class="col-md-6">
                                {!! Form::email('email', old('email'), ['class' => 'form-control  validate[required,custom[email]]', 'placeholder'=>'Email*','id'=>'email']) !!}
                            </div>
                            <div class="col-md-12">
                                {!! Form::text('subject', old('subject'), ['class' => 'form-control  validate[required]', 'placeholder'=>'Subject','id'=>'subject']) !!}
                            </div>
                            <div class="col-md-12">
                                {!! Form::textarea('message', old('message'), ['class' => 'form-control  validate[required]', 'rows'=> 4, 'id'=>'message','placeholder'=>'Your Query*']) !!}
                            </div>
                            <div class="col-md-8">
                                <input type="button" onclick="contactus()" class="form-control text-uppercase"
                                       value="Send">
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- end contact -->

@endsection

@section('js')

    {!! Html::script('http://maps.googleapis.com/maps/api/js') !!}
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

            $(function () {
                new WOW().init();
                $('.laraship-nav').singlePageNav({
                    offset: 70,
                    updateHash: true,
                    filter: ':not(.external)',
                });

                /* Hide mobile menu after clicking on a link
                 -----------------------------------------------*/
                $('.navbar-collapse a').click(function () {
                    $(".navbar-collapse").collapse('hide');
                });
            })
        })
        function contactus() {
            name = $("#name").val();
            email = $("#email").val();
            subject = $("#subject").val();
            message = $("#message").val();
            var valid = 'false'
            if(name!= '' && email!='' && message!='')
                valid = 'true'
            else
                swal('Please provide required fields to contact us.');
            if (valid == 'true') {
                $.ajax({
                    headers: {
                        'X-CSRF-Token': "{{ csrf_token() }}"
                    },
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/contact-us', // This is the url we gave in the route
                    data: {
                        'name': name,
                        'email': email,
                        'subject': subject,
                        'message': message
                    }, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        swal(response);
                        $("#name").val('');
                        $("#email").val('');
                        $("#subject").val('');
                        $("#message").val('');
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }
        }
    </script>
@endsection
