<?php 
   use App\User;
   use App\Department;
   ?>
@extends('voyager::master')
@section('page_title', __('voyager::generic.view').' '.' Procedure view')
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

<?php $approval_requests = DB::table('approval_requests')->where([['procedure_id', $dataTypeContent->id], ['status', 'pending']])->first(); ?>
<?php if ($approval_requests): ?>
    <?php 
      $approvalUsers = explode(',', $approval_requests->users);
      $checkUserHaveRequest = in_array((string)Auth::user()->id, $approvalUsers);
    ?>
    <?php if ($checkUserHaveRequest): ?>
      <div class="alert submitApproval">
        <div>
          <div>
            <?php
              $requestBy = User::find($approval_requests->user_id);
              echo $requestBy->name .' has send approval request';
            ?>
          </div>
            <a href="/procedure/{{ $dataTypeContent->id }}/publish_directly"><button type="submit" class="btn btn-success">Accpet</button></a>
            <a href="/procedure/{{ $dataTypeContent->id }}/reject"><button type="button reject_Approval" class="btn btn-dark">Reject</button></a>
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        </div>
      </div>
    <?php endif ?>
<?php endif ?>
<div class="row" style="padding: 10px;">
   <div class="col-md-12">
      <div class="page-bar">
         <ol class="breadcrumb hidden-xs">
            <li class="active">
               <a href="{{ route('voyager.dashboard')}}"><i class="voyager-home"></i> {{ __('voyager::generic.dashboard') }}</a>
            </li>
            <li class="active department_li_breadcrumb">
               <?php $department = Department::find($dataTypeContent->department_id); ?>
               <a href="/departments/{{ $department->id }}"><i class="voyager-company"></i> {{ $department->name }} </a>
            </li>
            <?php 
               $getParents = DB::table('folder_and_procedure')->where([['department_id', '=', $dataTypeContent->department_id], ['type', '=', 'folder'], ['id', '!=', $dataTypeContent->id], ['delete', NULL]])->orderBy('id', 'DESC')->get();
               ?>
            <?php if ($getParents): ?>
                <?php $parentID = $dataTypeContent->parent_id; ?>
                <?php foreach ($getParents as $key => $parent): ?>
                  <?php if ($parent->id == $parentID): ?>
                    <li class="active breadcrumb_li">
                       <a href="/browse/folder/{{ $parent->id }}"><i class="voyager-folder" style="margin-right: 3px;"></i>{{ $parent->name }}</a>
                    </li>
                    <?php $parentID = $parent->parent_id ?>
                  <?php endif ?>
                <?php endforeach ?>
            <?php endif ?>
            <li>{{ $dataTypeContent->name }}</li>
         </ol>
      </div>
   </div>
</div>
<?php $owner = User::find($dataTypeContent->owner); ?>
<?php if ($dataTypeContent->owner != Auth::user()->id): ?>
  <?php $userAccess =  DB::table('user_department')->where([['user_id', Auth::user()->id],['department_id', $dataTypeContent->department_id],['role', 'publisher']])->first(); ?>
  <?php if (!$userAccess || Auth::user()->role_id != '1'): ?>
      <input type="hidden" class="userType" value="0" />
  <?php endif ?>
<?php else: ?>
    <input type="hidden" class="userType" value="1" />
<?php endif ?>
<div class="panel-body procedure_view_page" style="background: #fff;margin-left: 16px;">
   <div class="row">
      <div class="col-md-12">
        <div id="blocks-container" class="col-lg-9 col-sm-12">
            <div id="procedure-nestable-container" class="dd">
               <div class="panel-group" id="accordion_proc_view">
                  <div class="row">
                     <div class="col-md-10">
                        <h2>{{ $dataTypeContent->name }} </h2>
                        <p>{{ $dataTypeContent->description }}</p>
                        <h4><?php 
                          $user = User::find($dataTypeContent->owner);
                          echo 'Created by <b style="font-weight:600;">'.$user->name. '</b> on <b style="font-weight:600;">'. date('F d, Y', strtotime($dataTypeContent->created_at)).'</b>';
                        ?>
                        </h4>
                     </div>
                  </div>
                <?php if ($procedureContent): ?>
                  <?php $count = 0; ?>
                  <?php  foreach ($procedureContent as $key => $singleProcedureData): ?>
                    <?php 
                      $comments = DB::table('procedure_data_comment')->where('procedure_data_id', $singleProcedureData->id)->get();
                    ?>
                    <div class="{{ $singleProcedureData->step == '1' ? 'panel panel-default' : NULL}} procedure_data_id_{{ $singleProcedureData->id }}">
                       <div id="preview_{{ $singleProcedureData->id }}">
                        <?php if ($singleProcedureData->step == '1'): $count ++; ?>
                          <a style="margin-right: 20px; margin-left: 5px;" class="pull-left btn btn-circle btn-warning" href="#relationship-208">Step {{ $count }}</a>
                        <?php endif ?>
                         <div class="panel-body">
                          <div class="hidden-print comm-edit-div" id="block-tools-container-{{ $singleProcedureData->id }}">
                             <div id="block-tools-{{ $singleProcedureData->id }}" class="pull-right block-tools">
                                <?php 
                                $propose_count = DB::table('procedure_data')->where([['parent_id','=', $singleProcedureData->id]])->whereRaw("(`status` LIKE '%pending%' OR `status` LIKE '%accept%' OR `status` LIKE '%update%')")->count();
                                  $checkAccepted = DB::table('procedure_data')->where([['parent_id','=', $singleProcedureData->id], ['status', "accept"]])->first();

                                  $acceptedFound = 'warning';
                                  if ($checkAccepted) {
                                    $acceptedFound = 'success';
                                  }
                                ?>
                                <a href="#" data-blockinfo="{{ $singleProcedureData->type }}" data-procedure="{{ $dataTypeContent->id }}" data-relationship_id="{{ $singleProcedureData->id }}" class="btn btn-{{ $acceptedFound }} btn-xs btn-proposals Image-proposals-button propose-block-button-standard">
                                  <i class="fas fa-edit"></i>
                                  <span class="badge badge-danger ">
                                    {{ $propose_count }}
                                  </span>
                                </a>
                             </div>
                             <div class="block-comments-container pull-right">
                                <a data-relationship_id="{{ $singleProcedureData->id }}" class="comments-link btn btn-xs btn-warning" href="#block-comments-modal-{{ $singleProcedureData->id }}" id="comments-display-button-{{ $singleProcedureData->id }}" data-toggle="modal" data-target="#block-comments-modal-{{ $singleProcedureData->id }}">
                                  <i class="far fa-comments"></i>
                                  <span class="badge badge-danger ">{{ count($comments) }}</span>
                                </a>
                                <!-- Modal -->
                                <div class="modal fade" id="block-comments-modal-{{ $singleProcedureData->id }}" tabindex="-1" role="dialog" aria-labelledby="block-comments-modal-{{ $singleProcedureData->id }}-label" aria-hidden="true">
                                   <div class="modal-dialog">
                                      <div class="modal-content">
                                         <div class="modal-body">
                                            <button aria-label="Close" data-dismiss="modal" class="close pull-right" type="button"><span aria-hidden="true">×</span></button>
                                            <div class="portlet light comment-portlet">
                                               <div class="portlet-title">
                                                  <div class="caption">
                                                     <i class="far fa-comments"></i>
                                                     <span class="caption-subject font-red-sunglo bold uppercase">Comments</span>
                                                  </div>
                                               </div>
                                               <div class="portlet-body comments-container" id="comments-container-{{ $singleProcedureData->id }}">
                                                  <ul class="chats comments-list" id="comments-list-{{ $singleProcedureData->id }}">
                                                    <?php if ($comments): ?>
                                                      <?php foreach ($comments as $key => $comment): ?>
                                                        <li class="{{ $comment->user_id == Auth::user()->id ? 'out' : 'in' }}">
                                                           <div class="comment-content" id="comment-{{ $comment->id }}">
                                                              <img src="{{ voyager_asset('images/captain-avatar.png') }}" class="avatar">
                                                              <div class="message"><span class="arrow"></span>
                                                                 <a class="name">
                                                                  <?php
                                                                   $user = User::find($comment->user_id);
                                                                   echo $user->name;
                                                                  ?>
                                                                 </a><br>
                                                                 <span class="comment-datestamp">
                                                                  {{ date('F d, Y g:i', strtotime($comment->created_at)) }}
                                                                 </span>
                                                                 <a data-procedure_data_id="{{ $singleProcedureData->id }}" class="comment-delete" data-commentid="{{ $comment->id }}" href="/comments/{{ $comment->id }}/destroy">
                                                                  <i class="fas fa-times"></i>
                                                                  </a>
                                                                 <span class="body">{{ $comment->comment }}</span>
                                                              </div>
                                                           </div>
                                                        </li>
                                                      <?php endforeach ?>
                                                    <?php endif ?>
                                                  </ul>
                                                  <div class="chat-form">
                                                     <div class="input-cont">
                                                        <input data-commentableid="{{ $singleProcedureData->id }}" data-commentabletype="App\ProcedureBlockRelationship" data-blockinfo="Image_31" data-relationship_id="{{ $singleProcedureData->id }}" id="add-comment-input-{{ $singleProcedureData->id }}" class="form-control add-comment-input" type="text" placeholder="Type a message here and press enter...">
                                                     </div>
                                                     <div class="btn-cont">
                                                        <span class="arrow"></span>
                                                        <a data-commentableid="{{ $singleProcedureData->id }}" data-blockinfo="Image_31" data-relationship_id="{{ $singleProcedureData->id }}" id="add-comment-input-{{ $singleProcedureData->id }}" href="" class="btn blue icn-only submit-comment">
                                                        <i class="fas fa-check"></i>
                                                        </a>
                                                     </div>
                                                  </div>
                                               </div>
                                            </div>
                                         </div>
                                         <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                         </div>
                                      </div>
                                   </div>
                                </div>
                                <!-- Modal -->
                                <div class="modal fade" id="add-comment-to-block-modal-{{ $singleProcedureData->id }}" tabindex="-1" role="dialog" aria-labelledby="add-comment-to-block-modal-{{ $singleProcedureData->id }}-label" aria-hidden="true">
                                   <div class="modal-dialog">
                                      <div class="modal-content">
                                         <form method="POST" action="comments/block-store" accept-charset="UTF-8">
                                            @csrf
                                            <input type="hidden" name="commentableType" value="App\ProcedureBlockRelationship">
                                            <input type="hidden" name="commentableId" value="{{ $singleProcedureData->id }}">
                                            <div class="modal-header">
                                               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                               <h4 class="modal-title" id="create-procedure-modal-label"> Add a comment </h4>
                                            </div>
                                            <div class="modal-body">
                                               <div class="form-group">
                                                  <label for="content">Your comment</label>
                                                  <textarea id="create-comment-textarea-modal-{{ $singleProcedureData->id }}" class="form-control" name="content" cols="50" rows="10"></textarea>
                                               </div>
                                            </div>
                                            <div class="modal-footer">
                                               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                               <button type="submit" class="btn btn-primary"> Add Comment </button>
                                            </div>
                                         </form>
                                      </div>
                                   </div>
                                </div>
                             </div>
                          </div>
                          {!! $singleProcedureData->content !!}
                         </div>
                       </div>
                     </div>
                  <?php endforeach ?>
                <?php endif ?>
               </div>
            </div>
        </div>
        <div class="hidden-print col-lg-3 col-sm-12 procedure_actions">
            <div data-spy="">
              <?php 
                $foundRequest = DB::table('approval_requests')->where([['user_id', Auth::user()->id], ['procedure_id', $dataTypeContent->id]])->first();
              ?>
              <?php if ($foundRequest): ?>
                <?php if ($foundRequest->status == 'approval' || $foundRequest->status == 'review'): ?>
                  <div class="dashboard-stat blue">
                     <div class="visual">
                        <i class="fas fa-cogs"></i>
                     </div>
                     <div class="details">
                        <div class="number">
                        </div>
                        <div class="desc">
                           Waiting for {{ $foundRequest->status }}
                        </div>
                     </div>
                     <a class="more" href="/procedure/{{ $dataTypeContent->id }}/{{ $foundRequest->status == 'approval' ? 'review_approval' : 'request_review' }}">Review procedure<i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                <?php endif ?>
              <?php endif ?>
               <div class="panel panel-primary">
                  <div class="panel-heading">
                     Procedure status
                  </div>
                  <div class="panel-body">
                     <div class="btn-group btn-group-solid procedure-actions-buttons-container">
                        <span class="btn btn-danger" style="cursor: default;">
                          <?php if ($foundRequest): ?>
                            <?php if ($foundRequest->status == 'approval'): ?>
                                 Waiting for approval
                            <?php elseif($foundRequest->status == 'review'): ?>
                                 Waiting for review
                            <?php else: ?>
                                 This procedure has been rejected
                            <?php endif ?>
                          <?php else: ?>
                            <?php if ($dataTypeContent->edit == NULL || $dataTypeContent->edit == '1' || $dataTypeContent->edit == '2' || $dataTypeContent->edit == '3'): ?>
                                 Editing
                            <?php else: ?>
                                 Live
                            <?php endif ?>
                          <?php endif ?>
                        </span>
                     </div>
                     <div class="btn-group btn-group-solid procedure-actions-buttons-container">
                      <?php $user = User::find($dataTypeContent->owner); ?>
                        <p>Published by <b>{{ $user->name }}</b> on <b>{{ date('F d, Y', strtotime($dataTypeContent->created_at)) }}</b></p>
                     </div>
                  </div>
               </div>
               <div class="panel panel-success editing_procedure">
                  <div class="panel-heading"> Procedure actions </div>
                  <div class="panel-body">
                    <div class="btn-group">
                      <?php $foundFavourite = DB::table('favourites')->where([['fold_proc_id', $dataTypeContent->id], ['user_id', Auth::user()->id], ['status', '1']])->first();
                      ?>
                      <a data-help="procedure-viewing-favorite-mark-button" href="/procedure/{{ $dataTypeContent->id }}/toggle-favorite" class="btn btn-primary toggle-favorite"><i class="voyager-star{{ $foundFavourite ? '-two' : ''}}"></i> {{ $foundFavourite ? 'Unmark as Favorite' : 'Mark as Favorite' }} </a>

                      <a id="procedure-print-button" data-help="procedure-viewing-print-button" href="javascript:window.print();" class="btn btn-default"><i class="fa fa-print"></i> Print</a>
                      <?php 
                        $isOwnerHref = '/procedure/'.$dataTypeContent->id.'/edit';
                        $target = '';
                        if ($dataTypeContent->owner != Auth::user()->id) {
                          $isOwnerHref = '#take-ownership-procedure-'.$dataTypeContent->id;
                          $target = '#take-ownership-procedure-'.$dataTypeContent->id;
                        }
                      ?>
                      <a data-help="procedure-viewing-edit-directly-button" href="{{ $isOwnerHref }}" id="procedure-edit-button" data-toggle="modal" data-target="{{ $target }}" class="btn btn-warning btn-procedure-edit-button"><i class="fa fa-edit"></i> Edit  </a>
                    </div>
                  </div>
               </div>

               <!-- Take Ownership Modal -->
               <div class="modal fade" id="take-ownership-procedure-{{ $dataTypeContent->id }}" tabindex="-1" role="dialog" aria-labelledby="take-ownership-procedure-{{ $dataTypeContent->id }}">
                  <div class="modal-dialog" role="document">
                     <div class="modal-content">
                        <div class="modal-header">
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                           <h4 class="modal-title" id="myModalLabel">Take Ownership Before Editing</h4>
                        </div>
                        <?php $owner = User::find($dataTypeContent->owner); ?>
                        <?php if ($dataTypeContent->owner != Auth::user()->id): ?>
                          <?php $userAccess =  DB::table('user_department')->where([['user_id', Auth::user()->id],['department_id', $dataTypeContent->department_id],['role', 'contributor']])->first(); ?>
                          <?php if ($userAccess || Auth::user()->role_id == '1'): ?>
                            <div class="modal-body">
                              <h2 style="margin-top: 0;">Warning</h2>
                                <p> You are not a department publisher for&nbsp;<strong>{{ $department->name }}</strong>, you will need to wait approval from a department publisher of <strong>{{ $department->name }}</strong> before being able to edit the procedure. You will receive a notification in case of approval or denial. </p>
                                <span class="warning">This procedure is already being edited by {{ $owner->name }}.</span>
                            </div>
                            <div class="modal-footer">
                              <a class="btn btn-default" data-dismiss="modal">Cancel</a>
                              <a href="/procedure/{{ $dataTypeContent->id }}/take_ownership" id="askOwnershipButton" class="btn btn-primary ask-ownership">Ask for ownership</a>
                            </div>
                          <?php endif ?>
                        <?php else: ?>
                          <div class="modal-body">
                             <h2 style="margin-top: 0;">Warning</h2>
                             <p> You are a publisher taking over directly, you know what you're doing. </p>
                             <span class="warning">This procedure is already being edited by {{ $owner->name }}.</span>
                          </div>
                          <div class="modal-footer">
                             <a class="btn btn-default" data-dismiss="modal">Cancel</a>
                             <a href="/procedure/{{ $dataTypeContent->id }}/take_ownership" id="takeOwnershipButton" class="btn btn-primary take-ownership">Take ownership and edit</a>
                          </div>
                        <?php endif ?>
                     </div>
                  </div>
               </div>
               <!-- Modal -->

              <!-- Edit Modals -->
              <!-- paragraph Modal -->
              <div class="modal fade" id="block-editor-modal-container" tabindex="-1" role="dialog" aria-labelledby="block-editor-modal-container-label" aria-hidden="true" style="display: none;">
                 <div class="modal-dialog ui-draggable">
                    <div class="modal-content">
                       <div class="modal-header ui-draggable-handle">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                          <span style="display: none;" id="modal-loader1" class="pull-right modal_loader"><i class="fa fa-cog fa-spin"></i> Loading...</span>
                          <h4 class="modal-title" id="block-editor-modal-container-label">Edit Paragraph</h4>
                       </div>
                       <div class="modal-body">
                          <ul class="nav nav-tabs" style="background-color: #fff !important;" role="tablist">
                             <li role="presentation" class="active"><a class="tab_link_button_top" href="#text_prop" id="embed-tab-link" role="tab" data-toggle="tab" aria-expanded="true">Your proposal</a></li>
                          </ul>

                        <div class="tab-content">
                           <div role="tabpanel" class="tab-pane active" id="text_prop">
                              <form method="POST" class="block-editor-form" accept-charset="UTF-8">
                               <input type="hidden" class="procedure_data_id" />
                               <textarea name="text" id="tinymceEditor" class="richTextBox" cols="10" rows="5"></textarea>
                               <div id="modal-footer-Image" class="modal-footer modal-tweak-footer">
                                   <div id="buttons-container-Image_48" class="btn-group-tweaks pull-right">
                                   <button  data-dismiss="modal" type="button" class="btn btn-default cancel-proposal-button">Cancel</button><button type="submit" class="btn btn-danger save-button-label propose-proposal-button">Propose Change</button>
                                   <?php $owner = User::find($dataTypeContent->owner); ?>
                                   <?php if ($dataTypeContent->owner != Auth::user()->id): ?>
                                     <?php $userAccess =  DB::table('user_department')->where([['user_id', Auth::user()->id],['department_id', $dataTypeContent->department_id],['role', 'publisher']])->first(); ?>
                                     <?php if ($userAccess || Auth::user()->role_id == '1'): ?>
                                       <button type="button" class="btn btn-success mark-as-accepted-proposal-button">Mark as Accepted</button>
                                       <button type="button" class="btn btn-primary publish-proposal-button">Publish Change</button>
                                     <?php endif ?>
                                   <?php endif ?>
                                   </div>
                               </div>
                              </form>
                           </div>
                        </div>
                       </div>
                    </div>
                 </div>
              </div>
              <!-- End paragraph Modal -->
              <!-- Image Modal -->
              <div class="modal fade" id="block-image-modal-container" tabindex="-1" role="dialog" aria-labelledby="block-image-modal-container-label" aria-hidden="true">
                 <div class="modal-dialog ui-draggable">
                    <div class="modal-content">
                       <div class="modal-header ui-draggable-handle">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                          <span style="display: none;" id="modal-loader2" class="pull-right modal_loader"><i class="fa fa-cog fa-spin"></i> Loading...</span>
                          <h4 class="modal-title" id="block-image-modal-container-label">Upload Image</h4>
                       </div>
                       <div class="modal-body">
                        <ul class="nav nav-tabs" style="background-color: #fff !important;" role="tablist">
                           <li role="presentation" class="active"><a class="tab_link_button" href="#imag_prop" id="embed-tab-link" role="tab" data-toggle="tab" aria-expanded="true">Your proposal</a></li>
                        </ul>
                        <div class="tab-content">
                           <div role="tabpanel" class="tab-pane active" id="imag_prop">
                             <input type="hidden" class="parent_id" />
                             <div id="my-dropzone" class="my-dropzone">
                               <div class="dropzone needsclick dz-clickable dz-message">
                                   Drop image here or click to upload.
                               </div>
                             </div>
                             <div class="progress">
                                <div class="progress-bar progress-bar-primary" role="progressbar" data-dz-uploadprogress_1>
                                </div>
                             </div>
                            <br><br>
                            **(only JPG, PNG are allowed) <br>
                            <div class="image_uploaded"></div>
                            <div id="modal-footer-Image" class="modal-footer modal-tweak-footer">
                                <div id="buttons-container-Image_48" class="btn-group-tweaks pull-right">
                                <button type="button" data-dismiss="modal" class="btn btn-default cancel-proposal-button">Cancel</button><button type="button" data-modal="#block-image-modal-container" class="btn btn-danger propose-proposal-button save-button-label proposedChange">Propose Change</button>
                                <?php $owner = User::find($dataTypeContent->owner); ?>
                                <?php if ($dataTypeContent->owner != Auth::user()->id): ?>
                                  <?php $userAccess =  DB::table('user_department')->where([['user_id', Auth::user()->id],['department_id', $dataTypeContent->department_id],['role', 'publisher']])->first(); ?>
                                  <?php if ($userAccess || Auth::user()->role_id == '1'): ?>
                                    <button type="button" class="btn btn-success mark-as-accepted-proposal-button">Mark as Accepted</button>
                                    <button type="button" class="btn btn-primary publish-proposal-button">Publish Change</button>
                                  <?php endif ?>
                                <?php endif ?>
                                </div>
                            </div>
                          </div>
                        </div>
                       </div>
                    </div>
                 </div>
              </div>
              <!-- End Image Modal -->

              <!-- Attachment Modal -->
              <div class="modal fade" id="block-attachment-modal-container" tabindex="-1" role="dialog" aria-labelledby="block-attachment-modal-container-label" aria-hidden="true">
                 <div class="modal-dialog ui-draggable">
                    <div class="modal-content">
                       <div class="modal-header ui-draggable-handle">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                          <span style="display: none;" id="modal-loader3" class="pull-right modal_loader"><i class="fa fa-cog fa-spin"></i> Loading...</span>
                          <h4 class="modal-title" id="block-image-modal-container-label">Upload Attachment</h4>
                       </div>
                       <div class="modal-body">
                        <ul class="nav nav-tabs" style="background-color: #fff !important;" role="tablist">
                           <li role="presentation" class="active">
                            <a class="tab_link_button" href="#attach_prop" id="embed-tab-link" role="tab" data-toggle="tab" aria-expanded="true">Your proposal</a>
                          </li>
                        </ul>
                        <div class="tab-content">
                           <div role="tabpanel" class="tab-pane active" id="attach_prop">
                               <input type="hidden" class="parent_id" />
                              <div id="my-dropzone-attachment" class="my-dropzone">
                                <div class="dropzone needsclick dz-clickable dz-message">
                                    Drop a file here or click to upload.
                                </div>
                              </div>
                              <div class="progress">
                                 <div class="progress-bar progress-bar-primary" role="progressbar" data-dz-uploadprogress_2>
                                 </div>
                              </div>
                              <div class="attach_uploaded" style="margin-top: 20px;"></div>
                              <div id="modal-footer-attachment" class="modal-footer modal-tweak-footer">
                                  <div id="buttons-container-Image_48" class="btn-group-tweaks pull-right">
                                  <button type="button" data-dismiss="modal" class="btn btn-default cancel-proposal-button">Cancel</button><button type="button" data-modal="#block-attachment-modal-container" class="btn btn-danger propose-proposal-button save-button-label proposedChange">Propose Change</button>
                                  <?php $owner = User::find($dataTypeContent->owner); ?>
                                  <?php if ($dataTypeContent->owner != Auth::user()->id): ?>
                                    <?php $userAccess =  DB::table('user_department')->where([['user_id', Auth::user()->id],['department_id', $dataTypeContent->department_id],['role', 'publisher']])->first(); ?>
                                    <?php if ($userAccess || Auth::user()->role_id == '1'): ?>
                                      <button type="button" class="btn btn-success mark-as-accepted-proposal-button">Mark as Accepted</button>
                                      <button type="button" class="btn btn-primary publish-proposal-button">Publish Change</button>
                                    <?php endif ?>
                                  <?php endif ?>
                                </div>
                              </div>
                            </div>
                          </div>
                       </div>
                    </div>
                 </div>
              </div>
              <!-- End Attachment Modal -->

              <!-- Procedure Modal -->
              <div class="modal fade" id="block-procedure-modal-container" tabindex="-1" role="dialog" aria-labelledby="block-procedure-modal-container-label" aria-hidden="true">
                 <div class="modal-dialog ui-draggable">
                   <form id="procedure_embed_form" class="procedure_embed_form">
                      <div class="modal-content">
                         <div class="modal-header ui-draggable-handle">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <span style="display: none;" id="modal-loader4" class="pull-right modal_loader"><i class="fa fa-cog fa-spin"></i> Loading...</span>
                            <h4 class="modal-title" id="block-procedure-modal-container-label">Select a Procedure to Embed</h4>
                         </div>
                         <div class="modal-body">
                          <ul class="nav nav-tabs" style="background-color: #fff !important;" role="tablist">
                             <li role="presentation" class="active"><a class="tab_link_button" href="#proce_prop" id="embed-tab-link" role="tab" data-toggle="tab" aria-expanded="true">Your proposal</a></li>
                          </ul>
                          <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="proce_prop">
                             <input type="hidden" class="procedure_data_id" />
                              <div>
                                  <span class="ms-helper " style="display: block;float: right; width: 100%;font-size: 12px; text-align: right;">You cannot choose more than 1 item</span>
                              </div>
                              <div>
                                <select name="procedure" id="procedure_model_select" class="procedure_model_select selectProcedure" style="width: 100%">
                                  <option></option>
                                  <?php 
                                   $procedures = DB::table('folder_and_procedure')->where([['owner', '=', Auth::user()->id], ['type', '=', 'procedure'], ['id', '!=', $dataTypeContent->id]])->get(); ?>
                                   <?php if ($procedures): ?>
                                     <?php foreach ($procedures as $key => $procedure): ?>
                                        <option value="{{ $procedure->id }}">{{ $procedure->name }}</option>
                                     <?php endforeach ?>
                                   <?php endif ?>
                                </select>
                              </div>
                              <div class="attach_procedure" style="margin-top: 20px;"></div>
                              <div id="modal-footer-attachment" class="modal-footer modal-tweak-footer">
                                  <div id="buttons-container-Image_48" class="btn-group-tweaks pull-right">
                                  <button type="button" data-dismiss="modal" class="btn btn-default cancel-proposal-button">Cancel</button><button type="submit" data-modal="#block-procedure-modal-container" class="btn btn-danger propose-proposal-button save-button-label">Propose Change</button>
                                  <?php $owner = User::find($dataTypeContent->owner); ?>
                                  <?php if ($dataTypeContent->owner != Auth::user()->id): ?>
                                    <?php $userAccess =  DB::table('user_department')->where([['user_id', Auth::user()->id],['department_id', $dataTypeContent->department_id],['role', 'publisher']])->first(); ?>
                                    <?php if ($userAccess || Auth::user()->role_id == '1'): ?>
                                      <button type="button" class="btn btn-success mark-as-accepted-proposal-button">Mark as Accepted</button>
                                      <button type="button" class="btn btn-primary publish-proposal-button">Publish Change</button>
                                    <?php endif ?>
                                  <?php endif ?>
                                </div>
                              </div>
                            </div>
                          </div>
                         </div>
                      </div>
                    </form>
                 </div>
              </div>
              <!-- End Procedure Modal -->
              <!-- Video Modal -->
              <div class="modal fade" id="block-video-modal-container" tabindex="-1" role="dialog" aria-labelledby="block-video-modal-container-label" aria-hidden="true">
                 <div class="modal-dialog ui-draggable">
                    <div class="modal-content">
                       <div class="modal-header ui-draggable-handle">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                          <span style="display: none;" id="modal-loader5" class="pull-right modal_loader"><i class="fa fa-cog fa-spin"></i> Loading...</span>
                          <h4 class="modal-title" id="block-video-modal-container-label">Embed Video</h4>
                       </div>
                       <div class="modal-body">
                        <ul class="nav nav-tabs" style="background-color: #fff !important;" role="tablist">
                           <li role="presentation" class="active"><a class="tab_link_button" href="#video_prop" id="embed-tab-link" role="tab" data-toggle="tab" aria-expanded="false">Your proposal</a></li>
                        </ul>
                        <div class="tab-content">
                          <div role="tabpanel" class="tab-pane active" id="video_prop">
                              <div>
                                 <!-- Nav tabs -->
                                 <ul class="nav nav-tabs" style="background-color: #fff !important;" role="tablist">
                                    <li role="presentation" class="active"><a class="tab_link_button" href="#embed" data-button="Embed" data-modal_title="Embed Video" id="embed-tab-link" role="tab" data-toggle="tab" aria-expanded="true">Embed video</a></li>
                                    <li role="presentation"><a class="tab_link_button" href="#upload" data-button="Save" data-modal_title="Upload Video" id="upload-tab-link" role="tab" data-toggle="tab" aria-expanded="false">Upload video</a></li>
                                 </ul>
                                 <!-- Tab panes -->
                                 <form class="video_embed_form" id="video_embed_form">
                                 <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="embed">
                                       <div class="form-group">
                                          <label for="embedUrl">Please paste in the URL of the video you want to embed</label>
                                          <input id="embedUrl" class="form-control" name="url" type="text">
                                       </div>
                                       <span class="text-success"> Supported: Youtube, Vimeo. </span>
                                       <br clear="all"><br>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="upload">
                                       <input type="hidden" class="parent_id"/>
                                       <div id="my-dropzone-video" class="my-dropzone dropzone_upload_button btn btn-dark">
                                           Upload video
                                       </div>
                                       <div class="progress">
                                          <div class="progress-bar progress-bar-primary" role="progressbar" data-dz-uploadprogress_3>
                                          </div>
                                       </div>
                                       <br><br><br>
                                       **(only MP4, AVI and OGG video files are allowed) <br><br>
                                    </div>
                                 </div>
                                 <div class="attach_video"></div>
                                 <div id="modal-footer-Image" class="modal-footer modal-tweak-footer">
                                     <div id="buttons-container-Image_48" class="btn-group-tweaks pull-right">
                                     <button type="button" data-dismiss="modal" class="btn btn-default cancel-proposal-button">Cancel</button><button type="submit" class="btn btn-danger propose-proposal-button save-button-label">Propose Change</button>
                                     <?php $owner = User::find($dataTypeContent->owner); ?>
                                     <?php if ($dataTypeContent->owner != Auth::user()->id): ?>
                                       <?php $userAccess =  DB::table('user_department')->where([['user_id', Auth::user()->id],['department_id', $dataTypeContent->department_id],['role', 'publisher']])->first(); ?>
                                       <?php if ($userAccess || Auth::user()->role_id == '1'): ?>
                                         <button type="button" class="btn btn-success mark-as-accepted-proposal-button">Mark as Accepted</button>
                                         <button type="button" class="btn btn-primary publish-proposal-button">Publish Change</button>
                                       <?php endif ?>
                                     <?php endif ?>
                                   </div>
                                 </div>
                                 </form>
                              </div>
                            </div>
                          </div>

                       </div>
                    </div>
                 </div>
              </div>
              <!-- End Video Modal -->
              <!-- End Edit Modals -->

            </div>
        </div>
      </div>
    </div>
</div>
@stop
@section('javascript')
  <script>
    jQuery(document).ready(function($) {
      $(document).on('click', '.submit-comment', function(e) {
        e.preventDefault();
        var $this = $(this);
        var procedure_data_id = $this.attr('data-commentableid');
        var comment = $('#add-comment-input-'+procedure_data_id).val();
        var commentCount = $('#preview_'+procedure_data_id+ ' .block-comments-container .badge').html();
        if ($.trim(comment).length < 1) {
          alert('Please type a comment first, thank you.');
        } else {

          $this.find('i').addClass('fa-cog fa-spin');
          $('#add-comment-input-'+procedure_data_id).val('');
          $.ajax({
            url: '/procedure/add_comment',
            type: 'POST',
            data: {
              "_token": "{{ csrf_token() }}",
              'procedure_data_id': procedure_data_id,
              'comment': comment,
            },
          })
          .done(function(response) {
            var newComment = `
            <li class="out">
               <div class="comment-content" id="comment-`+response.comment_id+`">
                  <img src="{{ voyager_asset('images/captain-avatar.png') }}" class="avatar">
                  <div class="message"><span class="arrow"></span>
                     <a class="name">`+response.user_name+`</a><br>
                     <span class="comment-datestamp">
                      `+response.date+`
                     </span>
                     <a data-procedure_data_id="`+procedure_data_id+`" class="comment-delete" data-commentid="`+response.comment_id+`" href="/comments/`+response.comment_id+`/destroy">
                      <i class="fas fa-times"></i>
                      </a>
                     <span class="body">`+comment+`</span>
                  </div>
               </div>
            </li>`;

            $('#comments-list-'+procedure_data_id).append(newComment);
            $('#preview_'+procedure_data_id+ ' .block-comments-container .badge').html(parseInt(commentCount) + 1);
            $this.find('i').removeClass('fa-cog fa-spin');
          });
        }

      });

      // delete Comment
      $(document).on('click', '.comment-delete', function(e) {
        e.preventDefault();
        var $this = $(this);
        var comment_id = $this.attr('data-commentid');
        var procedure_data_id = $this.attr('data-procedure_data_id');
        var confirming = confirm("Are you sure you want to delete this comment?");
        var commentCount = $('#preview_'+procedure_data_id+ ' .block-comments-container .badge').html();

        if(confirming == true) {
          $.ajax({
            url: '/comment/comment_destroy',
            type: 'POST',
            data: {
              "_token": "{{ csrf_token() }}",
              'comment_id': comment_id,
            },
          })
          .done(function(response) {
            $('#preview_'+procedure_data_id+ ' .block-comments-container .badge').html(parseInt(commentCount) - 1);
            $this.parents().eq(2).fadeOut('fast', function() {
              $(this).remove();
            });;
          })
          .fail(function() {
            alert("Oops! Something went wrong");
          });
        }
      });

      // select 2 
      $('#procedure_model_select').select2({
        placeholder: "Type or click here",
      });


      // delete permenently
      $(document).on('click', '.permanently-delete-block-button', function(e) {
        e.preventDefault();
        var $this = $(this);
        $this.closest('.modal').find('.modal_loader').attr('style', 'display:block');
        var id = $this.attr('data-id');
        var parent_id = $this.attr('data-data_id');
        var type = $this.attr('data-type');
        
        $.ajax({
          url: '/procedure/permenentDelete',
          type: 'POST',
          data: {
                "_token": "{{ csrf_token() }}",
                'id': id,
              }
        }).done(function(response) {
          $this.closest('.modal').find('.modal_loader').attr('style', 'display:none');
          var proposeCount = $('#block-tools-'+parent_id+' .badge').html();
          $('#block-tools-'+parent_id+' .badge').html((parseInt(proposeCount) - 1));
          
          if (type == 'BlockText') {
            $('#block-editor-modal-container').modal('hide');
            var tinymce_editor_id = 'tinymceEditor';
            tinymce.get(tinymce_editor_id).setContent('');
            
          } else if (type == 'BlockImage') {

            $('#block-image-modal-container').modal('hide');

          } else if (type == 'BlockAttachment') {

            $('#block-attachment-modal-container').modal('hide');

          } else if (type == 'BlockProcedure') {

            $('#block-procedure-modal-container').modal('hide');

          } else if (type == 'BlockVideo') {

            $('#block-video-modal-container').modal('hide');

          }
        });
        
      });
      
      // add text block paragraph
      $('.propose-block-button-standard').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var userType = $('.userType').val();
        var type = $this.attr('data-blockinfo');
        var data_id = $this.attr('data-relationship_id');
        var modal = '';

        if (type == 'BlockText') {
          modal = '#block-editor-modal-container';
        } else if(type == 'BlockImage') {
          modal = '#block-image-modal-container';
        } else if(type == 'BlockAttachment') {
          modal = '#block-attachment-modal-container';
        } else if(type == 'BlockProcedure') {
          modal = '#block-procedure-modal-container';
        } else if(type == 'BlockVideo') {
          modal = '#block-video-modal-container';
        }

        if (userType != '0') {          
          $.ajax({
            url: '/procedure/checkAllProposes',
            type: 'POST',
            data: {
              "_token": "{{ csrf_token() }}",
              'parent_id': data_id,
              'type': type
            },
          })
          .done(function(response) {
            if (type != 'BlockVideo') {
              $(modal+' .tab-content:eq(0) .tab-pane:not(:eq(0))').remove();
            }
            $(modal+' .nav-tabs:eq(0) li:not(:first-child)').remove();
            $(modal+' .nav-tabs:eq(0) li:first-child').addClass('active');
            $(modal+' .tab-content:eq(0) .tab-pane:eq(0)').addClass('active');
            if (response.length > 0) {
              $.each(response, function(index, val) {

                var area_expand = 'false';
                var tabStatus = '';
                var accept = '';
                var tabContent = `<div role="tabpanel" class="tab-pane `+tabStatus+`" id="by_`+val.procedure_data_id+`">
                                    <input type="hidden" class="procedure_data_id" value="`+val.procedure_data_id+`" />
                                      `+val.content+`
                                        <a style="margin: 0 2px;" class="publish_prop" data-procedure_data_id="`+val.procedure_data_id+`" href="/block-proposals/`+val.procedure_data_id+`/publish"><button type="button" class="btn btn-success mark-as-accepted-proposal-button">Publish</button></a>`;

                if (val.status == 'accept') {
                  accept = `style="background: #2ecc71;"`;
                  tabContent += `<button style="margin: 0 2px;" type="button" class="btn btn-primary">This is the currently accepted version</button>`;
                } else {
                  tabContent += `<a style="margin: 0 2px;" class="accept_prop" data-procedure_data_id="`+val.procedure_data_id+`" href="/block-proposals/`+val.procedure_data_id+`/accept"><button type="button" class="btn btn-primary publish-proposal-button">Accept</button></a>`;
                }
                tabContent += `<a style="margin: 0 2px;" class="reject_prop" data-procedure_data_id="`+val.procedure_data_id+`" href="/block-proposals/`+val.procedure_data_id+`/reject"><button type="button" class="btn btn-danger publish-proposal-button">Reject</button></a>
                                  </div>`;

                var tabLinkTop = `<li `+accept+` role="presentation"><a class="tab_link_button_top" href="#by_`+val.procedure_data_id+`" id="embed-tab-link`+val.procedure_data_id+`" role="tab" data-toggle="tab" aria-expanded="`+area_expand+`">`+val.name+`</a></li>`;

                $(modal+' .nav-tabs:eq(0)').append(tabLinkTop);

                $(modal+' .tab-content:eq(0)').append(tabContent);


              });
            }
          });
        }

        $(modal).modal('show');

        $(modal).find('.tab-pane.active .procedure_data_id').val(data_id);
        $(modal).find('.tab-pane.active .parent_id').val(data_id);

        // send ajax request and propse data
        $.ajax({
          url: '/procedure/checkPropose',
          type: 'POST',
          data: {
            "_token": "{{ csrf_token() }}",
            'parent_id': data_id,
            'type': type
          },
        })
        .done(function(response) {
          $(modal).find('.permanently-delete-block-button').remove();
          if (response.propose == '1') {
            $(modal+' .tab-content:eq(0) .tab-pane:eq(0) .proposedChange').attr('data-procedure_data_id', response.id);
            $(modal).find('.save-button-label').text('Update');
            var deleteButton = `<button data-data_id="`+data_id+`" data-type="`+type+`" data-id="`+response.id+`" type="button" class="btn btn-danger permanently-delete-block-button">Delete</button>`;
            $(modal+' .btn-group-tweaks').append(deleteButton);
          } else {
            $(modal+' .tab-content:eq(0) .tab-pane:eq(0) .proposedChange').attr('data-procedure_data_id', '0');
            $(modal).find('.save-button-label').text('Propose Change');
          }
          
          if (type == 'BlockText') {
            var tinymce_editor_id = 'tinymceEditor';
            tinymce.get(tinymce_editor_id).setContent(response.procedureContent);

          } else if(type == 'BlockImage') {
            $(modal).find('.modal-body .image_uploaded').html(response.procedureContent);

          } else if(type == 'BlockAttachment') {
            $(modal).find('.modal-body .attach_uploaded').html(response.procedureContent);

          } else if(type == 'BlockProcedure') {
            $(modal).find('.modal-body .attach_procedure').html(response.procedureContent);

          } else if(type == 'BlockVideo') {
            $(modal).find('.modal-body .attach_video').html(response.procedureContent);
          }
        });

      });


      // text paragraph on submit
      $('.block-editor-form').on('submit', function(e) {
        e.preventDefault();
        var $this = $(this);
        var procedure_data_id = $this.find('.procedure_data_id').val();
        var procedure_data_textblock = $this.find('.richTextBox').val();

        $.ajax({
          url: '/procedure/propose_new',
          type: 'POST',
          data: {
            "_token": "{{ csrf_token() }}",
            'procedure_id': "{{ $dataTypeContent->id }}",
            'procedure_data_id': procedure_data_id,
            'content': procedure_data_textblock,
            'type': 'BlockText'
          }
        })
        .done(function(response) {
           $('#block-editor-modal-container').modal('hide');
           if (response == 'add') {

            var proposeCount = $('#block-tools-'+procedure_data_id+' .badge').html();
            $('#block-tools-'+procedure_data_id+' .badge').html((parseInt(proposeCount) + 1));

           }
        });
      });

      // Tab button click save button text change for video modal
      $('.tab_link_button').on('shown.bs.tab', function(e) {
        var $this = $(this);
        $('#save-button-Video').html($this.attr('data-button'));
        $('#block-video-modal-container-label').html($this.attr('data-modal_title'));

      });

      // Embed procedure on procedure
      $('#procedure_embed_form').on('submit',  function(e) {
        e.preventDefault();
        var $this = $(this);
        $('#modal-loader4').attr('style', 'display:block');
        var procedureSelected = $this.find('#procedure_model_select').val();
        var procedure_data_id = $this.find('.procedure_data_id').val();
        var procedure_data_name = $this.find('#procedure_model_select option:selected').text();

        if (procedureSelected == '') {
          alert('Please select a procedure');
        } else {
          var procedure_data_attach_procedure = `<div id="block-contents-Procedure_`+procedure_data_id+`" class="portlet-body procedure-block-body" style="display:block">
             <h4><i class="fa fa-file-text"></i> <a target="_blank" href="/procedure/`+procedureSelected+`">External Procedure: `+procedure_data_name+`</a> <i class="fa fa-arrow-right"></i></h4>
          </div>`;
            $.ajax({
              url: '/procedure/propose_new',
              type: 'POST',
              data: {
                "_token": "{{ csrf_token() }}",
                'procedure_id': "{{ $dataTypeContent->id }}",
                'procedure_data_id': procedure_data_id,
                'content': procedure_data_attach_procedure,
                'type': 'BlockProcedure'
              }
            })
            .done(function(response) {
               if (response.status == 'add') {
                var proposeCount = $('#block-tools-'+procedure_data_id+' .badge').html();
                $('#block-tools-'+procedure_data_id+' .badge').html((parseInt(proposeCount) + 1));
                $('#block-procedure-modal-container').modal('hide');
                $('#procedure_model_select').val('').trigger('change');
               } else {
                  $('#block-procedure-modal-container .modal-body .attach_procedure').html(procedure_data_attach_procedure);
               }

                $('#modal-loader4').attr('style', 'display:none');

                // Update Procedure Data
                var obj = { embed: procedureSelected};
                var myJSON = JSON.stringify(obj);
                var data = {id: response.id, additional_data: myJSON};
                updateProcedureData(data);
            });

        }
      });

      // embed youtube or vimeo Video on procedure
      $('#video_embed_form').on('submit', function(e) {
        e.preventDefault();
        var $this = $(this);
        $this.closest('.modal').find('.modal_loader').attr('style', 'display:block');
        var procedure_data_id = $this.find('.parent_id').val();
        var embedUrl = $('#embedUrl').val();


        if (embedUrl.toLowerCase().indexOf("youtube") > -1 && embedUrl.length > 41) {
          var videoId = embedUrl.substr(embedUrl.indexOf("watch?v=") + 8);
          if (videoId.length > 10) {
            var videoIframe = `<div class="procedure-block" id="procedure-block-`+procedure_data_id+`"><iframe width="480" height="295" frameborder="0" allowfullscreen src="https://www.youtube.com/embed/`+ videoId+`?wmode=transparent"></iframe></div>`;
          }
        } else if (embedUrl.toLowerCase().indexOf("vimeo") > -1 && embedUrl.length > 25) {
          var videoId = embedUrl.substr(embedUrl.indexOf("vimeo.com/") + 10);

          if (videoId.length > 8) {
            var videoIframe = `<div class="procedure-block" id="procedure-block-`+procedure_data_id+`"><iframe width="480" height="295" frameborder="0" allowfullscreen src="http://player.vimeo.com/video/`+ videoId+`?wmode=transparent"></iframe></div>`;
          }

        } else {
          videoIframe = `<h3 align="center"> No video uploaded </h3>`;
        }

        $.ajax({
          url: '/procedure/propose_new',
          type: 'POST',
          data: {
            "_token": "{{ csrf_token() }}",
            'procedure_id': "{{ $dataTypeContent->id }}",
            'procedure_data_id': procedure_data_id,
            'content': videoIframe,
            'type': 'BlockVideo'
          }
        })
        .done(function(response) {
          $this.closest('.modal').find('.modal_loader').attr('style', 'display:none');
           if (response == 'add') {
              $('#block-video-modal-container').modal('hide');

              var proposeCount = $('#block-tools-'+procedure_data_id+' .badge').html();
              $('#block-tools-'+procedure_data_id+' .badge').html((parseInt(proposeCount) + 1));
           } else {
              $('#block-video-modal-container .modal-body .attach_video').html(videoIframe);
           }
        });

      });

      $(document).on('click', '.proposedChange', function(e) {
        e.preventDefault();
        var $this = $(this);
        var modal = $this.attr('data-modal');
        var procedure_data_id = $this.attr('data-procedure_data_id');

        $.ajax({
          url: '/proposedChange',
          type: 'POST',
          data: {
            "_token": "{{ csrf_token() }}",
            'procedure_data_id': procedure_data_id,
          }
        })
        .done(function(response) {
          $(modal).modal('hide');
        });

      });


      $(document).on('click', '.publish_prop', function(e) {
        e.preventDefault();
        var $this = $(this);
        var procedure_data_id = $this.attr('data-procedure_data_id');

        $.ajax({
          url: '/actionOnChange',
          type: 'POST',
          data: {
            "_token": "{{ csrf_token() }}",
            'procedure_data_id': procedure_data_id,
            'status': 'publish'
          }
        })
        .done(function(response) {
          $('#block-tools-'+response.procedure+' a').css('style', 'background: #2ecc71;');
          $this.closest('.modal').modal('hide');
          $('.procedure_data_id_'+response.procedure+' .panel-body').find('.hidden-print').next('*').remove();
          $('.procedure_data_id_'+response.procedure+' .panel-body').append(response.content);
        });

      });

      $(document).on('click', '.accept_prop', function(e) {
        e.preventDefault();
        var $this = $(this);
        var procedure_data_id = $this.attr('data-procedure_data_id');

        $.ajax({
          url: '/actionOnChange',
          type: 'POST',
          data: {
            "_token": "{{ csrf_token() }}",
            'procedure_data_id': procedure_data_id,
            'status': 'accept'
          }
        })
        .done(function(response) {
          $('#block-tools-'+response.procedure+' a').css('background', '#2ecc71');
          $this.closest('.modal').modal('hide');
        });


      });

      $(document).on('click', '.reject_prop', function(e) {
        e.preventDefault();
        var $this = $(this);
        var procedure_data_id = $this.attr('data-procedure_data_id');

        $.ajax({
          url: '/actionOnChange',
          type: 'POST',
          data: {
            "_token": "{{ csrf_token() }}",
            'procedure_data_id': procedure_data_id,
            'status': 'reject'
          }
        })
        .done(function(response) {
          $('#block-tools-'+response.procedure+' a').css('background', '#f39c12');
          $('#block-tools-'+response.procedure+' a span.badge').html((parseInt($('#block-tools-'+response.procedure+' a span.badge').html()) -1 ));
          $this.closest('.modal').modal('hide');
        });
      });

    });

  </script>
  <script type="text/javascript">

         // dropzone work form image block
        $("#my-dropzone").dropzone({
          autoDiscover: false,
          url: "/dropzone/imageuploadchange",
          maxFilesize: 10,
          maxFiles: 1,
          acceptedFiles: ".jpg,.png",
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          sending: function(file, xhr, formData) {
            formData.append('parent_id', $($(this)[0].element).prev('.parent_id').val());
            formData.append('type', 'BlockImage');
          },
          uploadprogress: function(file, progress, bytesSent) {
            var progressElement = $("[data-dz-uploadprogress_1]");
                progressElement.css('width', progress + "%");
          },
          addedfile: function() {
              var $this = $($(this)[0].element);
              $this.next('.progress').show();
          },
          success: function(file, response) 
          {
              var progressElement = $("[data-dz-uploadprogress_1]");
              var $this = $($(this)[0].element);
              var procedure_data_id = $this.prev('.parent_id').val();
              var procedure_data_imag = `
                <div class="image-show- image-show-alignment-container`+procedure_data_id+`" id="image-show-alignment-container-Image_`+procedure_data_id+`">
                  <img id="block_image-Image_`+procedure_data_id+`" style="margin-top:1em;" src="/procedure_images/`+response.success+`">
                </div>
              `;

              $('#block-image-modal-container .modal-body .image_uploaded').html(procedure_data_imag);
              $('#block-image-modal-container .modal-body .proposedChange').attr('data-procedure_data_id', response.procedure_data_id);

              // Update Procedure Data
              var obj = { size: "475", align: "center", file: response.success };
              var myJSON = JSON.stringify(obj);
              var data = {id: response.procedure_data_id, content: procedure_data_imag, attach:response.original_name, additional_data:myJSON};
              updateProcedureData(data);

              $this.next('.progress').hide();
              progressElement.css('width', 0);
              this.removeAllFiles(true);
              if (response.status == 'add') {
                var proposeCount = $('#block-tools-'+procedure_data_id+' .badge').html();
                $('#block-tools-'+procedure_data_id+' .badge').html((parseInt(proposeCount) + 1));
              }

          },
          error: function(file, response)
          {
            var $this = $($(this)[0].element);
            var progressElement = $("[data-dz-uploadprogress_1]");
            $this.closest('.modal').modal('hide');
            $this.show();
            $this.next('.progress').hide();
            progressElement.css('width', 0);
            this.removeAllFiles(true);
            return false;
          }

        });


         // dropzone work form attachment block
        $("#my-dropzone-attachment").dropzone({
          autoDiscover: false,
          url: "/dropzone/imageuploadchange",
          maxFilesize: 500,
          maxFiles: 1,
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          sending: function(file, xhr, formData) {
            formData.append('parent_id', $($(this)[0].element).prev('.parent_id').val());
            formData.append('type', 'BlockAttachment');
          },
          uploadprogress: function(file, progress, bytesSent) {
            var progressElement = $("[data-dz-uploadprogress_2]");
                progressElement.css('width', progress + "%");
          },
          addedfile: function() {
              var $this = $($(this)[0].element);
              $this.next('.progress').show();
          },
          success: function(file, response) 
          {
              var progressElement = $("[data-dz-uploadprogress_2]");
              var $this = $($(this)[0].element);
              var procedure_data_id = $this.prev('.parent_id').val();
              procedure_data_attachblock = `
                <div id="block-contents-Attachment_`+procedure_data_id+`" class="portlet-body procedure-block-body" style="display:block">
                    <div class="procedure-block" id="procedure-block-`+procedure_data_id+`">
                      <a href="/block-attachments/`+response.success+`/download" class="download_attachment"> Download `+response.original_name+`</a>
                    </div>
                </div>
              `;

              $('#block-attachment-modal-container .modal-body .attach_uploaded').html(procedure_data_attachblock);
              $('#block-attachment-modal-container .modal-body .proposedChange').attr('data-procedure_data_id', response.procedure_data_id);

              // Update Procedure Data
              var data = {id: response.procedure_data_id, content: procedure_data_attachblock, attach:response.original_name};
              updateProcedureData(data);

              $this.next('.progress').hide();
              progressElement.css('width', 0);
              this.removeAllFiles(true);
              if (response.status == 'add') {
                var proposeCount = $('#block-tools-'+procedure_data_id+' .badge').html();
                $('#block-tools-'+procedure_data_id+' .badge').html((parseInt(proposeCount) + 1));
              }
              
          },
          error: function(file, response)
          {
            var $this = $($(this)[0].element);
            var progressElement = $("[data-dz-uploadprogress_2]");
            $this.next('.progress').hide();
            progressElement.css('width', 0);
            this.removeAllFiles(true);
            return false;
          }

        });

         // dropzone work form video block
        $("#my-dropzone-video").dropzone({
          autoDiscover: false,
          url: "/dropzone/imageuploadchange",
          maxFilesize: 500,
          acceptedFiles: ".mp4,.avi,.ogg",
          maxFiles: 1,
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          sending: function(file, xhr, formData) {
            formData.append('parent_id', $($(this)[0].element).prev('.parent_id').val());
            formData.append('type', 'BlockVideo');
          },
          uploadprogress: function(file, progress, bytesSent) {
            var progressElement = $("[data-dz-uploadprogress_3]");
                progressElement.css('width', progress + "%");
          },
          addedfile: function() {
              var $this = $($(this)[0].element);
              $this.next('.progress').show();
          },
          success: function(file, response) 
          {
              var $this = $($(this)[0].element);
              var progressElement = $("[data-dz-uploadprogress_3]");
              var $this = $($(this)[0].element);
              var procedure_data_id = $this.prev('.parent_id').val();
              procedure_data_videoblock = `
                <div id="block-contents-Video_`+procedure_data_id+`" class="portlet-body procedure-block-body" style="display:block">
                    <video width="400" controls>
                      <source src="/procedure_videos/`+response.success+`" type="video/mp4">
                    </video>
                </div>
              `;
              $('#block-video-modal-container .modal-body .attach_video').html(procedure_data_videoblock);
              $('#block-video-modal-container .modal-body .proposedChange').attr('data-procedure_data_id', response.procedure_data_id);

              // Update Procedure Data
              var data = {id: response.procedure_data_id, content: procedure_data_videoblock, status:'pending', attach:response.original_name};
              updateProcedureData(data);

              $this.next('.progress').hide();
              progressElement.css('width', 0);
              this.removeAllFiles(true);
              if (response.status == 'add') {
                var proposeCount = $('#block-tools-'+procedure_data_id+' .badge').html();
                $('#block-tools-'+procedure_data_id+' .badge').html((parseInt(proposeCount) + 1));
              }
              
              $('#block-video-modal-container').modal('hide');
              
          },
          error: function(file, response)
          {
            var $this = $($(this)[0].element);
            var progressElement = $("[data-dz-uploadprogress_3]");
            $this.closest('.modal').modal('hide');
            $this.next('.progress').hide();
            progressElement.css('width', 0);
            this.removeAllFiles(true);
            return false;
          }

        });


        // Update Procedure Function
        function updateProcedureData(data) {

          data['_token'] = "{{ csrf_token() }}";
          
          $.ajax({
            url: '/procedure/updateProcedureData',
            type: 'POST',
            data: data
          });

        }
</script>
@stop