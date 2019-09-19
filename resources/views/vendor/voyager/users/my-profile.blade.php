<?php  
    $user = Auth::user();
    $user_id = $user->id;
?>

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', 'Edit Profile')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-person"></i>
        Edit Profile
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form" class="form-edit-add" action="{{ URL::to('/users/'.$user_id) }}" method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if($user)
                            {{ method_field("PUT") }}
                        @endif

                        <!-- CSRF TOKEN -->
                        @csrf

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Adding / Editing -->
                            <div class="panel-body">
                               <!-- Adding / Editing -->
                               <!-- GET THE DISPLAY OPTIONS -->
                               <div class="form-group  col-md-12 ">
                                  <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                                  <label class="control-label" for="name">Name</label>
                                  <input required="" type="text" class="form-control" name="name" placeholder="Name" value="{{ $user->name }}">
                               </div>
                               <!-- GET THE DISPLAY OPTIONS -->
                               <div class="form-group  col-md-12 ">
                                  <label class="control-label" for="name">Email</label>
                                  <input required="" type="text" class="form-control" name="email" placeholder="Email" value="{{ $user->email }}">
                               </div>
                               <!-- GET THE DISPLAY OPTIONS -->
                               <div class="form-group  col-md-12 ">
                                  <label class="control-label" for="name">Password</label>
                                  <br>
                                  <small>Leave empty to keep the same</small>
                                  <input type="password" class="form-control" name="password" value="">
                               </div>
                               
                            </div>

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary save">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- End Delete File Modal -->
@stop

@section('javascript')
    <script>
       
    </script>
@stop
