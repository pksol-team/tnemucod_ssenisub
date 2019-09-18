<?php 
  use Carbon\Carbon;
 ?>
@extends('voyager::master')

@section('page_title', __('voyager::generic.view').' '.' Notifications')

@section('page_header')
    @include('voyager::multilingual.language-selector')
@stop

@section('content')

    <div class="row" style="padding: 10px;">
      <div class="col-md-12">
        <div class="portlet light">
           <div class="portlet-title tabbable-line">
              <div class="caption caption-md">
                 <i class="icon-globe theme-font hide"></i>
                 <span class="caption-subject font-blue-madison bold uppercase">Your Notifications</span>
              </div>
           </div>
           <div class="portlet-body">
              <!--BEGIN TABS-->
              <div class="tab-content">
                 <div class="tab-pane active" id="notifications-tab">
                  <ul class="feeds">
                    <?php if (count($dataTypeContent) > 0): ?>
                      <?php foreach ($dataTypeContent as $key => $notification): ?>
                        <li class="clearfix">
                            <div class="label label-sm label-success">
                               <i class="voyager-bell"></i>
                            </div>
                            <div class="desc">
                              {!! $notification->body !!}
                            </div>
                            <div class="date">
                              <?php 
                              $date = Carbon::parse($notification->created_at); // now date is a carbon instance
                              echo $elapsed = $date->diffForHumans(Carbon::now());

                               ?>
                            </div>
                        </li>
                      <?php endforeach ?>
                    <?php else: ?>
                      <li class="clearfix">
                        <div class="label label-sm label-success" style="width: 100%;">
                          You don't have any notifications
                        </div>
                      </li>
                    <?php endif ?>
                  </ul>     
                 </div>     
              </div>
              <!--END TABS-->
           </div>
        </div>
      </div>
    </div>
@stop