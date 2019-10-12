<?php 
  use App\User;
?>
@extends('voyager::master')
@section('content')
<div class="page-content">
   <div class="row" style="margin-top: 50px;">
      <div class="col-md-12 dashboard_document favourites_document">
         <div class="container">
            <div class="row dashboard">
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
               <div class="col-md-12">
                  <div data-help="dashboard-favorites-panel" class="panel panel-default favorites-panel">
                     <div class="panel-heading"> <img src="{{ Voyager::image(Auth::user()->avatar) }}" class="profile-img img-circle" style="width: 30px;display: inline-block;"> {{ Auth::user()->name }} - Favorite procedures</div>
                      <table id="item-listings" class="table table-hover">
                         <thead>
                            <tr>
                               <th width="1%"></th>
                               <th width="1%"></th>
                               <th width="1%"></th>
                               <th width="60%">Name</th>
                               <th width="10%">Owner</th>
                               <th width="25%">Last updated</th>
                            </tr>
                         </thead>
                         <tbody>
                               @forelse ($favourites as $favourite)
                                <tr>
                                   <td><i data-help="browse-procedure-icon" class="{{ $favourite->type == 'folder' ? 'voyager-folder' : 'voyager-file-text' }}"></i></td>
                                   <td>
                                      <a data-help="browse-procedure-favorite" class="toggle-favorite" style="text-decoration: none;margin-left: 2px;" href="/{{ $favourite->type }}/{{ $favourite->folder_and_procedureID }}/toggle-favorite"><i class="voyager-star-two"></i></a>
                                   </td>
                                   <td>
                                      <div class="dropdown" style="display:inline">
                                         <i data-help="procedure-right-click-gear-icon" style="cursor: pointer" type="button" id="procedure-dropdown-{{ $favourite->folder_and_procedureID }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="voyager-settings"></i>
                                         <ul class="dropdown-menu" aria-labelledby="procedure-dropdown-{{ $favourite->folder_and_procedureID }}">
                                            <?php if ($favourite->type != 'folder'): ?>
                                              <li><a href="/procedure/{{ $favourite->folder_and_procedureID }}/edit">Edit</a></li>
                                              <li><a href="/procedure/{{ $favourite->folder_and_procedureID }}/duplicate">Duplicate</a></li>
                                            <?php endif ?>
                                           {{--  <li><a class="moveButton" data-target="#move-procedure-modal-{{ $favourite->folder_and_procedureID }}" data-type="{{ $favourite->type }}" data-id="{{ $favourite->folder_and_procedureID }}">Move</a></li> --}}
                                            <li><a data-toggle="modal" data-target="#rename-{{ $favourite->type }}-modal-{{ $favourite->folder_and_procedureID }}">Rename</a></li>
                                            <li><a href="/{{ $favourite->type }}/{{ $favourite->folder_and_procedureID }}/destroy">Delete</a></li>
                                         </ul>
                                      </div>
                                      <!-- Various modals and addons required here -->
                                      <!-- Modal -->
                                      <div class="modal fade" id="move-procedure-modal-{{ $favourite->folder_and_procedureID }}" tabindex="-1" role="dialog" aria-labelledby="move-procedure-modal-label" aria-hidden="true">
                                         <div class="modal-dialog">
                                            <div class="modal-content">
                                               <form method="POST" action="/{{ $favourite->type }}/{{ $favourite->folder_and_procedureID }}/move" accept-charset="UTF-8">
                                                  @csrf
                                                  <div class="modal-header">
                                                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                     <h4 class="modal-title" id="move-procedure-modal-label">Move Name</h4>
                                                  </div>
                                                  <div class="modal-body">
                                                     <!-- <ul class="list-unstyled" id="destination-selector">
                                                        <li style="padding-left: 21px;" id="department-5" data-type="department" class="active">
                                                           <a href="/browse/department/5"><i style="font-size: 18px;" class="fa fa-building"></i> dept. Service &amp; Parts </a>
                                                        </li>
                                                        <li style="padding-left: 21px;" id="department-2" data-type="department">
                                                           <a href="/browse/department/2"><i style="font-size: 18px;" class="fa fa-building"></i> Facturare </a>
                                                        </li>
                                                        <li style="padding-left: 21px;" id="department-4" data-type="department">
                                                           <a href="/browse/department/4"><i style="font-size: 18px;" class="fa fa-building"></i> Marketing </a>
                                                        </li>
                                                        <li style="padding-left: 21px;" id="department-7" data-type="department">
                                                           <a href="/browse/department/7"><i style="font-size: 18px;" class="fa fa-building"></i> new deparment </a>
                                                        </li>
                                                     </ul> -->
                                                     <input type="hidden" id="destinationType-Procedure-4" name="destinationType">
                                                     <input type="hidden" id="destinationId-Procedure-4" name="destinationId">
                                                  </div>
                                                  <div class="modal-footer">
                                                     <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                     <button type="submit" class="btn btn-primary moveButton">Move Name</button>
                                                  </div>
                                               </form>
                                            </div>
                                         </div>
                                      </div>
                                      <!-- Modal -->
                                      <div class="modal fade" id="rename-{{ $favourite->type }}-modal-{{ $favourite->folder_and_procedureID }}" tabindex="-1" role="dialog" aria-labelledby="rename-{{ $favourite->type }}-modal-{{ $favourite->folder_and_procedureID }}-label" aria-hidden="true">
                                         <div class="modal-dialog">
                                            <div class="modal-content">
                                               <form method="POST" action="/proc_fold_rename" accept-charset="UTF-8">
                                                  @csrf
                                                  <input type="hidden" name="type_id" value="{{ $favourite->folder_and_procedureID }}" />
                                                  <div class="modal-header">
                                                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                                     <h4 class="modal-title" id="rename-{{ $favourite->type }}-modal-label"> Rename {{ ucfirst($favourite->type) }} </h4>
                                                  </div>
                                                  <div class="modal-body">
                                                     <div class="form-group">
                                                        <div class="form-group">
                                                           <label for="name">New {{ $favourite->type }} name</label>
                                                           <input class="form-control" name="name" type="text" value="{{ $favourite->folder_and_procedureName }}" id="name" required>
                                                        </div>
                                                        <?php if ($favourite->type == 'procedure'): ?>
                                                        <div class="form-group">
                                                           <label for="description">Procedure goal / description</label>
                                                           <textarea class="form-control" name="description" cols="50" rows="10" id="description" required>{{ $favourite->description }}</textarea>
                                                        </div>
                                                        <?php endif ?>
                                                     </div>
                                                  </div>
                                                  <div class="modal-footer">
                                                     <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                     <button type="submit" class="btn btn-primary"> Rename {{ ucfirst($favourite->type) }} </button>
                                                  </div>
                                               </form>
                                            </div>
                                         </div>
                                      </div>
                                      <!-- Modal -->
                                   </td>
                                   <td>
                                    <?php if ($favourite->type == 'folder'): ?>
                                      <a class="item-name" href="/browse/folder/{{ $favourite->folder_and_procedureID }}">{{ $favourite->folder_and_procedureName }}</a>
                                    <?php else: ?>
                                      <?php 
                                        $request_found = DB::table('approval_requests')->where('procedure_id', $favourite->folder_and_procedureID)->first();
                                      ?>
                                      <?php if ($request_found): ?>
                                        <?php if ($request_found->status != 'reject'): ?>
                                          <?php
                                            $link = '';
                                            if ($request_found->status == 'review') {
                                              $link = 'request_review';
                                            } elseif ($request_found->status == 'approval') {
                                              $link = 'review_approval';
                                            }
                                          ?>
                                            <a class="item-name" href="/{{ $favourite->type }}/{{ $favourite->folder_and_procedureID }}/{{ $link }}">{{ $favourite->folder_and_procedureName }}</a>
                                        <?php endif ?>

                                      <?php else: ?>
                                        
                                        <?php if ($favourite->status == NULL): ?>
                                          <?php if ($favourite->owner == Auth::user()->id): ?>
                                            <a class="item-name" href="/{{ $favourite->type }}/{{ $favourite->folder_and_procedureID }}/edit">{{ $favourite->folder_and_procedureName }}</a>
                                          <?php else: ?>
                                            <a class="item-name" href="/{{ $favourite->type }}/{{ $favourite->folder_and_procedureID }}/edit">{{ $favourite->folder_and_procedureName }}</a>
                                          <?php endif ?>
                                        <?php else: ?>
                                          <?php if ($favourite->edit != NULL): ?>
                                            <?php if ($favourite->owner == Auth::user()->id): ?>
                                              <a class="item-name" href="/{{ $favourite->type }}/{{ $favourite->folder_and_procedureID }}/edit">{{ $favourite->folder_and_procedureName }}</a>
                                            <?php else: ?>
                                              <a class="item-name" href="/{{ $favourite->type }}/{{ $favourite->folder_and_procedureID }}">{{ $favourite->folder_and_procedureName }}</a>
                                            <?php endif ?>
                                          <?php else: ?>
                                            <a class="item-name" href="/{{ $favourite->type }}/{{ $favourite->folder_and_procedureID }}">{{ $favourite->folder_and_procedureName }}</a>
                                          <?php endif ?>
                                        <?php endif ?>
                                      <?php endif ?>
                                    <?php endif ?>
                                   </td>
                                   <td>
                                    <?php $owner = User::find($favourite->owner); ?>
                                    {{ $owner->name }}
                                   </td>
                                   <td>
                                      <?php if ($favourite->updated_at == NULL): ?>
                                        {{ date('D, M d, Y g:i A', strtotime($favourite->folder_and_procedureCreateDate)) }}
                                      <?php else: ?>
                                        {{ date('D, M d, Y g:i A', strtotime($favourite->folder_and_procedureUpdateDate)) }}
                                      <?php endif ?>
                                   </td>
                                </tr>
                               @empty
                               @endforelse
                         </tbody>
                      </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@stop
@section('javascript')
<script>
   jQuery(document).ready(function($) {

      $("#quick-filter-favorites").on("keyup", function() {
         var $this = $(this);
         var keywords = $this.val().toLowerCase();
         if (keywords.trim().length == 0) {
            $this.val('');
         }
         $("#favorites-list > li").filter(function() {
            $(this).toggle($(this).find('.item-name').text().toLowerCase().indexOf(keywords) > -1)
         });
      });
      
   });
</script>
@stop
