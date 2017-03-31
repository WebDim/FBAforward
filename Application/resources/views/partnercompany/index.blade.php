@extends('layouts.frontend.app')
@section('title', $title)
@section('css')
    <style type="text/css">
        .margin-bottom {
            margin-bottom: 5px;
        }
        .modal-dialog {
            width: 80%;
            height: 80%;
            margin: 3;
            padding: 0;
        }
        .modal-content {
            height: auto;
            min-height: 80%;
            border-radius: 0;
        }
    </style>
    {!! Html::style('assets/dist/css/datatable/dataTables.bootstrap.min.css') !!}
    {!! Html::style('assets/dist/css/datatable/responsive.bootstrap.min.css') !!}
    {!! Html::style('assets/dist/css/datatable/dataTablesCustom.css') !!}
@endsection
@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <h3 class="page-head-line col-md-10">{{$title}}</h3>
                <div class="col-md-2">
                    <a href="{{ url('/partnercompany/create') }}" class="btn btn-primary">Create Partner </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h4 style="text-align: center;text-transform: uppercase"></h4>
                <hr>
                <div class="table-responsive no-padding">
                    <table id="data" class="table" >
                        <thead>
                        <tr>
                            <th>Delivery Company</th>
                            <th>Terminal</th>
                            <th>Delivery Destination</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($company as $newcompany)
                            <tr id="tr_{{$newcompany->id}}">
                                <td><b class="text-info">{{ $newcompany->delivery_company }}</b></td>
                                <td><b class="text-info">{{ $newcompany->terminal }}</b></td>
                                <td><b class="text-info">{{ $newcompany->destination }}</b></td>
                                <td><b class="text-info">{{ $newcompany->created_at }}</b> </td>
                                <td>
                                    <a href="{{ url('/partnercompany/'.$newcompany->id) .'/edit' }}" class="btn btn-info">Edit</a>
                                    <a href="javascript:void(0)" onclick="remove_company({{$newcompany->id}})" class="btn btn-danger">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection
@section('js')
    {!! Html::script('assets/dist/js/datatable/jquery.dataTables.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/dataTables.bootstrap.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/dataTables.responsive.min.js') !!}
    {!! Html::script('assets/dist/js/datatable/responsive.bootstrap.min.js') !!}
    <script type="text/javascript">
        $(document).ready(function() {
            $('#data').DataTable({
            });
        });
        function remove_company(id){
            if(confirm('Are you sure you want to delete this company!')){
                $('.preloader').css("display", "block");
                $.ajax({
                    headers: {
                        'X-CSRF-Token':  "{{ csrf_token() }}"
                    },
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/partnercompany/delete', // This is the url we gave in the route
                    data: {
                        'id': id,
                    }, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        $('.preloader').css("display", "none");
                        console.log(response);
                        if(response == 0){
                            swal('Sorry! Somthing went wrong please delete leter');
                        }else {
                            //$('#tr_'+id).remove();
                            location.reload();
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        $('.preloader').css("display", "none");
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }
        }

    </script>
@endsection