<?php 
   use App\User;
   use App\Department;
   ?>
@extends('voyager::master')
@section('page_title', __('voyager::generic.view').' '.' Procedure preview')
@section('page_header')
@include('voyager::multilingual.language-selector')
@stop
@section('content')
@if(session()->has('message'))
<div class="alert alert-success">
   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
   {!! session()->get('message') !!}
</div>
@endif
@if(session()->has('error'))
<div class="alert alert-danger">
   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
   {!! session()->get('error') !!}
</div>
@endif
<div class="panel-body" style="background: #fff;margin-left: 16px;">
   <div class="row">
      <div class="col-md-12">
        <div id="blocks-container" class="col-lg-9 col-sm-12">
            <div id="procedure-nestable-container" class="dd">
               <div class="panel-group" id="accordion">
                  <div class="row">
                     <div class="col-md-10">
                        <h2>{{ $dataTypeContent->name }} </h2>
                        <p>{{ $dataTypeContent->description }}</p>
                        <h4><?php 
                          $user = User::find($dataTypeContent->owner);
                          echo 'Owned by '.$user->name;
                        ?>
                        </h4>
                     </div>
                  </div>
                <?php if ($procedureContent): ?>
                  <?php $count = 0; ?>
                  <?php  foreach ($procedureContent as $key => $singleProcedureData): ?>
                    <div class="{{ $singleProcedureData->step == '1' ? 'panel panel-default' : NULL}} procedure_data_id_{{ $singleProcedureData->id }}">
                       <div id="preview_{{ $singleProcedureData->id }}" class="">
                        <?php if ($singleProcedureData->step == '1'): $count ++; ?>
                          <a style="margin-right: 20px; margin-left: 5px;" class="pull-left btn btn-circle btn-warning" href="#relationship-208">Step {{ $count }}</a>
                        <?php endif ?>
                         <div class="panel-body">
                          {!! $singleProcedureData->content !!}
                         </div>
                       </div>
                     </div>
                  <?php endforeach ?>
                <?php endif ?>
               </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-12 procedure_actions">
            <div data-spy="">
               <div class="panel panel-primary">
                  <div class="panel-heading">
                     Procedure status
                  </div>
                  <div class="panel-body">
                     <div class="btn-group btn-group-solid procedure-actions-buttons-container">
                        <span class="btn btn-danger" style="cursor: default;"> Preview </span>
                     </div>
                  </div>
               </div>
            </div>
        </div>
      </div>
    </div>
</div>
@stop
