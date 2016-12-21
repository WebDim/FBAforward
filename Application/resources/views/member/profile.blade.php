@extends('layouts.frontend.app')

@section('title', 'Profile')

@section('css')
    <style type="text/css">
        .margin-bottom {
            margin-bottom: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-head-line">Profile</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-3">
                <div class="margin-bottom"><a class="btn btn-primary" href="{{ url('member/profile/edit') }}"
                                              title="Edit" style="width: 100%;"><i
                                class="fa fa-2 fa-pencil"></i> Edit Your Profile</a></div>
                <img src="{{ asset($user->avatar) }}" class="img-responsive img-circle margin-bottom"
                     alt="{{ $user->name }}">
            </div>
            <div class="col-md-9">
                <div class="table-responsive no-padding">
                    <table class="table">
                        <tbody>
                        <tr>
                            @if($user->package_id != 0)
                                <td>
                                    <span>Your Package:</small>
                                </td>
                                <td>
                                    <b class="text-info">{{ $user->package->name }}</b>
                                </td>
                            @else
                                <td style="vertical-align:middle;">
                                    <strong id="join-us" class="text-danger">Join Us Now</strong>
                                </td>
                                <td><b><a href="{{ url('member/pricing') }}" class="btn btn-success"><i
                                                    class="fa fa-briefcase"></i> Select a Package</a></b></td>
                            @endif
                        </tr>
                        <tr>
                            <td style="width: 30%;">
                                <span>Name:</span>
                            </td>
                            <td><b class="text-info">{{ $user->name }}</b></td>
                        </tr>
                        <tr>
                            <td>
                                <span>Email:</span>
                            </td>
                            <td><b class="text-info">{{ $user->email }}</b></td>
                        </tr>
                        <tr>
                            <td>
                                <span>Job Title:</span>
                            </td>
                            <td>
                                <b class="text-info">{{ isset($user->job_title) && !empty($user->job_title) ? $user->job_title : '-' }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Mobile:</span>
                            </td>
                            <td>
                                <b class="text-info">{{ isset($user->mobile) && !empty($user->mobile) ? $user->mobile : '-' }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>Address:</span>
                            </td>
                            <td><b class="text-info">{{ $user->address }}</b></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">

                    @if($user->package_id != 0)
                        <div class="col-md-3 margin-bottom"><a href="{{ url('member/pricing') }}"
                                                               class="btn btn-success"
                                                               style="width: 100%;"><i
                                        class="fa fa-2 fa-briefcase"></i> Change
                                Package</a></div>
                        <div class="col-md-3 margin-bottom"><a href="{{ url('member/subscription/card') }}"
                                                               class="btn btn-success"
                                                               style="width: 100%;"><i
                                        class="fa fa-2 fa-credit-card"></i> Update
                                Card Details</a></div>
                        <div class="col-md-3 margin-bottom"><a href="{{ url('member/subscription/invoices') }}"
                                                               class="btn btn-success"
                                                               style="width: 100%;"><i
                                        class="fa fa-2 fa-list"></i> Invoices</a>
                        </div>
                        <div class="col-md-3 margin-bottom"><a href="{{ url('member/subscription/cancel') }}"
                                                               class="btn btn-warning"
                                                               style="width: 100%;"><i
                                        class="fa fa-2 fa-remove"></i> Cancel
                                Subscription</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">

    </script>
@endsection
