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
                                                            <?php $user = User::findOrFail($comment->user_id); ?>
                                                              <img src="{{ Voyager::image($user->avatar) }}" class="avatar">
                                                              <div class="message"><span class="arrow"></span>
                                                                 <a class="name">{{ $user->name }}</a><br>
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
               <div class="panel panel-success editing_procedure">
                  <div class="panel-heading"> Procedure actions </div>
                  <div class="panel-body text-left">
                    <div class="btn-group">
                        

                        <a id="back_to_proc" href="/procedure/{{ $dataTypeContent->id }}/" class="btn btn-dark"><i class="far fa-arrow-alt-circle-left"></i> Back to Procedure</a>


                       <?php $userAccess =  DB::table('user_department')->where([['user_id', Auth::user()->id],['department_id', $dataTypeContent->department_id],['role', 'publisher']])->first(); ?>
                       <?php if ($userAccess || Auth::user()->role_id == '1'): ?>
                          <a id="procedure-print-button" href="/procedure/{{ $dataTypeContent->id }}/publish_directly" class="btn btn-primary"> Publish Procedure</a>

                          <a id="procedure-reject-button" href="/procedure/{{ $dataTypeContent->id }}/reject" class="btn btn-danger"> Reject Procedure</a>

                       <?php endif ?>

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

               <div class="panel panel-success editing_procedure">
                  <?php 
                   $requestedBy = User::find($approval_requests->user_id);
                  ?>
                  <div class="panel-heading"> Approval requested by {{ $requestedBy->name }}: </div>
                  <div class="panel-body text-left">
                    <div class="btn-group">
                      {{ $approval_requests->comment }}
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
                  <img src="{{ Voyager::image(Auth::user()->avatar) }}" class="avatar">
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


    });

  </script>
@stop