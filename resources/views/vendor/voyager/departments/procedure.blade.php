<?php 
   use App\User;
   use App\Department;
   ?>
@extends('voyager::master')
@section('page_title', __('voyager::generic.view').' '.' Procedure')
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
<div class="panel-body" style="background: #fff;margin-left: 16px;">
   <div class="row">
      <div class="col-md-10 col-md-offset-1" id="flash_messages_container">
      </div>
   </div>
   <div class="row">
      <div class="col-md-10 col-md-offset-1">
      </div>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div id="blocks-container" class="col-lg-8 col-sm-12">
            <div class="row">
               <div class="col-md-10">
                  <h2 style="cursor:pointer" data-toggle="modal" data-target="#rename-procedure-modal">{{ $dataTypeContent->name }} </h2>
                  <p style="cursor:pointer" data-toggle="modal" data-target="#rename-procedure-modal">{{ $dataTypeContent->description }}</p>
               </div>
               <div class="col-md-2">
                  <a style="position: relative; top:20px;" id="procedure-button-toggle-blocks" href="#" class="btn btn-dark pull-right openCloseall" data-action="collapse">
                     <i class="fa fa-minus-square"></i> <span>Collapse All</span>
                  </a>
               </div>
            </div>
            <div id="procedure-nestable-container" class="dd">
               <div class="panel-group" id="accordion">
                <?php if ($procedureContent): ?>
                  <?php $count = 0; ?>
                  <?php foreach ($procedureContent as $key => $singleProcedureData): ?>
                    <?php 
                      $accordiontext = '';
                      $previewClass = '';
                      $modal = '';
                      if ($singleProcedureData->type == 'BlockText') {
                        $modal = '#block-editor-modal-container';
                        if ($singleProcedureData->content ==  NULL) {
                          $accordiontext = 'No text inside paragraph';
                        } else {
                          $text = strip_tags($singleProcedureData->content);
                          if (strlen($text) > 30) {
                            $accordiontext = substr($text,0,30).'...';
                          } else {
                            $accordiontext = $text;
                          }
                        }
                      } else if($singleProcedureData->type == 'BlockImage') {
                        $previewClass = 'dropzone-prev-'.$singleProcedureData->id;
                        $modal = '#block-image-modal-container';
                        if ($singleProcedureData->content ==  '<h3 align="center"> No image uploaded </h3>') {
                          $accordiontext = 'No image uploaded';
                        } else {
                          $accordiontext = $singleProcedureData->attach;
                        }
                      } else if($singleProcedureData->type == 'BlockAttachment') {
                        $modal = '#block-attachment-modal-container';
                        $previewClass = 'dropzone-prev-'.$singleProcedureData->id;
                        if ($singleProcedureData->content ==  '<h3 align="center"> No file uploaded </h3>') {
                          $accordiontext = 'No file uploaded';
                        } else {
                          $accordiontext = $singleProcedureData->attach;
                        }
                      } else if($singleProcedureData->type == 'BlockProcedure') {
                        $previewClass = 'dropzone-prev-'.$singleProcedureData->id;
                        $modal = '#block-procedure-modal-container';
                        if ($singleProcedureData->content ==  '<h3 align="center"> No procedure selected for embedding. </h3>') {
                          $accordiontext = 'No procedure attached';
                        } else {
                          if (strlen($singleProcedureData->attach) > 30) {
                            $accordiontext = substr($singleProcedureData->attach,0,30).'...';
                          } else {
                            $accordiontext = $singleProcedureData->attach;
                          }
                        }
                      } else if($singleProcedureData->type == 'BlockVideo') {
                        $previewClass = 'dropzone-prev-'.$singleProcedureData->id;
                        $modal = '#block-video-modal-container';
                        if ($singleProcedureData->content ==  NULL) {
                          $accordiontext = 'No video uploaded';
                        } else {
                          $accordiontext = 'Embedded video';
                        }
                      }

                    ?>

                    <div class="panel panel-default procedure_data_id_{{ $singleProcedureData->id }}" data-id="{{ $singleProcedureData->id }}">

                      <?php 
                      if ($singleProcedureData->status == 'deactive') {
                          $bgColor = '#a99b9b !important';
                      } else {

                        if ($singleProcedureData->step != NULL) {
                          $count++;
                          $bgColor = '#3598dc !important;';
                        } else {
                          $bgColor = '#1bbc9b !important;';
                        }

                      }

                      ?>

                       <div class="panel-heading" style="background: {{ $bgColor }}" >
                         <h4 class="panel-title">
                           <div class="accordion-toggle caption_proc">
                            <i class="fas fa-th-list" style="margin-right: 5px"></i>
                             <span>{{ $accordiontext }}</span>
                           </div>
                           <div class="tools {{ $singleProcedureData->status == 'deactive' ? 'hidden' : NULL }}">
                              <a class="btn-step" data-step="{{ $singleProcedureData->step }}" style="margin: 2px;" href="#" data-id="{{ $singleProcedureData->id }}"> <i class="fa fa-tasks"></i> <span class="step-string">{{ $singleProcedureData->step != NULL ? 'Unmark as step '.$count : 'Mark as step' }}</span> </a>
                              <a href="#" style="margin: 2px;" data-id="{{ $singleProcedureData->id }}" class="block-copy-to-clipboard" title="Duplicate block"><i class="fas fa-copy"></i></a>
                              <a class="visibility_edit" data-id="{{ $singleProcedureData->id }}" style="margin: 2px;" data-toggle="collapse" data-target="#collapse_{{ $singleProcedureData->id }}">
                              <i class="fas fa-chevron-{{ $singleProcedureData->expand == 'in' ? 'down' : 'up' }}"></i></a>
                              <a class="block-edit-button BlockText-edit-button" data-procedure_type="{{ $singleProcedureData->type }}" style="margin: 2px;" href="{{ $modal }}" data-procedure_id="{{ $singleProcedureData->id }}"><i class="fa fa-edit"></i></a>
                              <a class="remove-block-button" href="#" data-id="{{ $singleProcedureData->id }}" style="margin: 2px;"><i class="fas fa-times"></i></a>
                           </div>
                           <div class="tools-deleted pull-right {{ $singleProcedureData->status == 'active' ? 'hidden' : NULL }}">
                               <a data-blockinfo="" data-id="{{ $singleProcedureData->id }}" class="unremove-block-button btn btn-dark" href="javascript:;"><i class="fas fa-long-arrow-alt-left"></i> Undo delete </a>
                               <a data-blockinfo="" data-id="{{ $singleProcedureData->id }}" class="permanently-delete-block-button btn btn-danger pull-right" href="javascript:;"><i class="fas fa-times"></i></a>
                           </div>
                         </h4>
                       </div>
                       <?php $expand = $singleProcedureData->expand; ?>
                       <div id="collapse_{{ $singleProcedureData->id }}" class="panel-collapse collapse {{ $singleProcedureData->status == 'deactive' ? NULL : $expand }}">
                         <div class="panel-body {{ $previewClass }}">

                        <?php if ($singleProcedureData->type == 'BlockImage'): ?>
                          <?php $jArr = json_decode($singleProcedureData->additional_data);?>
                          <?php if ($singleProcedureData->content != '<h3 align="center"> No image uploaded </h3>' && $jArr != NULL): ?>
                            
                          <div class="row">
                              <div class="col-md-2">
                                  <p>Image Size</p>
                              </div>
                              <div class="col-md-8">
                                  <div id="slider-Image_{{ $singleProcedureData->id }}"></div>
                              </div>
                              <div class="col-md-2">
                                  <div class="btn-group btn-group-xs btn-group-justified">
                                      <a class="btn btn-default btn-image-align {{ $jArr->align == 'left' ? 'selected' : NULL }}" data-id="{{ $singleProcedureData->id }}" data-align="left" href="javascript:;"><i class="fa fa-align-left"></i></a>
                                      <a class="btn btn-default btn-image-align {{ $jArr->align == 'center' ? 'selected' : NULL }}" data-id="{{ $singleProcedureData->id }}" data-align="center" href="javascript:;"><i class="fa fa-align-center"></i></a>
                                      <a class="btn btn-default btn-image-align {{ $jArr->align == 'right' ? 'selected' : NULL }}" data-id="{{ $singleProcedureData->id }}" data-align="right" href="javascript:;"><i class="fa fa-align-right"></i></a>
                                  </div>
                              </div>
                          </div>

                          <?php endif ?>
                        <?php endif ?>

                          {!! $singleProcedureData->content !!}
                         </div>
                       </div>
                     </div>
                    <?php if ($singleProcedureData->type == 'BlockImage'): ?>
                      <?php if ($singleProcedureData->content != '<h3 align="center"> No image uploaded </h3>' && $jArr != NULL): ?>

                        @push('custom-scripts')
                         <script>
                          $(document).ready(function() {
                            
                             var rangeSlider = $('#slider-Image_{{ $singleProcedureData->id }}')[0];

                             noUiSlider.create(rangeSlider, {
                                 start: ['{{ $jArr->size }}'],
                                 range: {
                                     'min': [50],
                                     'max': [1000]
                                 }
                             });

                             var rangeSliderValueElement = $('.image-show-alignment-container{{ $singleProcedureData->id }} img');

                             rangeSlider.noUiSlider.on('update', function (values, handle) {
                                 rangeSliderValueElement.attr('width', values[handle]);
                             });

                             rangeSlider.noUiSlider.on('end', function (values, handle) {

                                var alignDiv = $('.image-show-alignment-container{{ $singleProcedureData->id }}').attr('style');
                                if(alignDiv != undefined) {
                                  var n = alignDiv.indexOf("-align:");
                                  var align = alignDiv.substring(n+7);
                                } else {
                                  var align = 'center';
                                }

                                var fullImageLink = rangeSliderValueElement.attr('src');

                                var n = fullImageLink.indexOf("_images/");
                                
                                var imageLink = fullImageLink.substring(n+8);

                                // Update Procedure Data
                                 var obj = { size: values.toString(), align: align, file: imageLink };
                                 var myJSON = JSON.stringify(obj);
                                 var data = {id: '{{ $singleProcedureData->id }}', additional_data: myJSON};
                                 updateProcedureData(data);
                             });

                          });
                         </script>
                         @endpush

                      <?php endif ?>
                    <?php endif ?>
                  <?php endforeach ?>
                  
                <?php endif ?>

               </div>

            </div>
            <div class="portlet solid grey-cascade">
               <div class="portlet-body">
                  <div class="col-sm-12 col-md-12 col-lg-12 add_cascades">
                     <h4><i class="fa fa-plus"></i> Add a new block</h4>
                     <a data-model="#block-editor-modal-container" data-blockinfo="BlockText" href="#" class="btn btn-default create-block-button-standard"><i class="fa fa-indent"></i> Paragraph</a>
                     <a data-model="#block-image-modal-container" data-blockinfo="BlockImage" href="#" class="btn btn-default create-block-button-standard"><i class="far fa-image"></i> Image</a>
                     <a data-model="#block-attachment-modal-container" data-blockinfo="BlockAttachment" href="#" class="btn btn-default create-block-button-standard"><i class="fa fa-paperclip"></i> Attachment</a>
                     <a data-model="#block-procedure-modal-container" data-blockinfo="BlockProcedure" href="#" class="btn btn-default create-block-button-standard"><i class="fas fa-file-alt"></i> Procedure</a>
                     <a data-model="#block-video-modal-container" data-blockinfo="BlockVideo" href="#" class="btn btn-default create-block-button-standard"><i class="fas fa-video"></i> Video</a>
                  </div>
                  <div class="clearfix"></div>
               </div>

               <!-- /.modal -->

               <!-- <div class="modal fade" id="paste-modal-explanations">
                  <div class="modal-dialog ui-draggable">
                     <div class="modal-content">
                        <div class="modal-header ui-draggable-handle">
                           <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                           <h4 class="modal-title">Copy / Paste / Link blocks</h4>
                        </div>
                        <div class="modal-body">
                           <h2>Paste a block:</h2>
                           <p>Explanation here...</p>
                           <h2>Link a block</h2>
                           <p>Explanation here...</p>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                     </div>
                  </div>
               </div> -->
               <!-- /.modal -->
            </div>
         </div>
         <div class="col-lg-4 col-sm-12 procedure_actions">
            <div data-spy="">
               <div class="panel panel-primary">
                  <div class="panel-heading">
                     Procedure actions
                  </div>
                  <div class="panel-body">
                     <div class="btn-group btn-group-solid procedure-actions-buttons-container">
                        <?php if ($dataTypeContent->status == '1'): ?>
                          <a id="back-procedure" class="btn btn-dark" href="/procedure/{{ $dataTypeContent->id }}"><i class="fas fa-arrow-circle-left"></i> Back to Procedure </a>
                        <?php endif ?>
                        <a data-help="procedure-editing-rename-button" id="rename-procedure" data-toggle="modal" data-target="#rename-procedure-modal" class="btn btn-primary"><i class="fas fa-pencil-alt"></i> Rename </a>
                        <a data-help="procedure-editing-submit-for-approval-button" id="submit-for-approval" data-toggle="modal" data-target="#submit-for-approval-procedure-{{ $dataTypeContent->id }}" class="btn btn-primary"><i class="fa fa-user"></i> Submit for Approval</a>
                        <a data-help="procedure-editing-submit-for-review-button" id="request-review" data-toggle="modal" data-target="#request-review-procedure-{{ $dataTypeContent->id }}" class="btn btn-yellow"><i class="fa fa-cogs"></i> Request Review</a>
                        <?php 
                           $user = Auth::user();
                        ?>
                        <?php if ($user->role_id == '1'): ?>
                          <a data-help="procedure-editing-publish-directly-button" id="publish" href="/procedure/{{ $dataTypeContent->id }}/publish_directly" class="btn btn-success"><i class="fa fa-bullhorn"></i> Publish Directly </a>
                        <?php else: ?>
                          <?php 
                            $userAccess = DB::table('user_department')->where([['user_id', $user->id],['department_id', $dataTypeContent->department_id],['role', 'publisher']])->first(); ?>
                            <?php if ($userAccess): ?>
                              <a data-help="procedure-editing-publish-directly-button" id="publish" href="/procedure/{{ $dataTypeContent->id }}/publish_directly" class="btn btn-success"><i class="fa fa-bullhorn"></i> Publish Directly </a>
                            <?php endif ?>
                        <?php endif ?>
                     </div>
                  </div>
               </div>
               <div class="panel panel-success editing_procedure">
                  <div class="panel-heading"> Editing </div>
                  <div class="panel-body">
                     <div class="btn-group btn-group-sm procedure-editing-buttons-container">
                        {{-- <a data-help="procedure-editing-save-button" id="procedure-button-save" href="" class="btn btn-primary"> <i class="far fa-save"></i> Save</a> --}}
                        <a data-help="procedure-editing-preview-button" id="procedure-button-preview" href="/procedure/{{ $dataTypeContent->id }}/preview" target="_blank" class="btn btn-success"><i class="fa fa-eye"></i> Preview </a>
                        <a data-help="procedure-editing-delete-button" id="procedure-button-destroy" href="/procedure/{{ $dataTypeContent->id }}/destroy" onclick="return confirm('are you sure want to delete?');" class="btn btn-danger"><i class="fas fa-times"></i> Delete </a>
                     </div>
                  </div>
               </div>
            </div>
            <br clear="all">
         </div>
         <!-- Modal -->
         <div class="modal fade" id="submit-for-approval-procedure-{{ $dataTypeContent->id }}" tabindex="-1" role="dialog" aria-labelledby="submit-for-approval-procedure-{{ $dataTypeContent->id }}-label" aria-hidden="true">
            <div class="modal-dialog ui-draggable">
               <div class="modal-content">
                  <form method="POST" action="/procedure/submit_for_approval" id="procedureAskForApprovalModalForm">
                     @csrf
                     <input type="hidden" name="procedure_id" value="{{ $dataTypeContent->id }}">
                     <input type="hidden" name="status" value="approval">
                     <div class="modal-header ui-draggable-handle">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="create-procedure-modal-label"> Submit For Approval </h4>
                     </div>
                     <div class="modal-body">
                        <div class="form-group">
                           <label for="comment">Your comment:</label>
                           <textarea class="form-control" name="comment" cols="50" rows="10"></textarea>
                        </div>
                        <h3>Select users to ask approval from </h3>
                        <div class="checkbox">
                          <?php 
                            $allUsers = User::where('id', '!=', Auth::user()->id)->get();
                          ?>
                          <?php if ($allUsers): ?>
                            <?php foreach ($allUsers as $key => $user): ?>
                              <?php $userAccess =  DB::table('user_department')->where([['user_id', $user->id],['department_id', $dataTypeContent->department_id],['role', 'publisher']])->first(); ?>
                              <?php if (!$userAccess): ?>
                                <?php continue; ?>
                                <?php else: ?>
                                  <label>
                                     <div class="checker">
                                       <span>
                                         <input type="checkbox" class="users-approval-checkbox" value="{{ $user->id }}" name="users[]">
                                       </span>
                                     </div>
                                     {{ $user->name }}
                                  </label>
                              <?php endif ?>
                            <?php endforeach ?>
                          <?php endif ?>
                        </div>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="submit_for_approval_button"> Submit for Approval </button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
         <!-- Modal -->
         <div class="modal fade" id="request-review-procedure-{{ $dataTypeContent->id }}" tabindex="-1" role="dialog" aria-labelledby="request-review-procedure-{{ $dataTypeContent->id }}-label" aria-hidden="true">
            <div class="modal-dialog ui-draggable">
               <div class="modal-content">
                  <form method="POST" action="/procedure/ask_for_review" id="procedureAskForReviewModalForm">
                     @csrf
                     <input type="hidden" name="procedure_id" value="{{ $dataTypeContent->id }}">
                     <input type="hidden" name="status" value="review">
                     <div class="modal-header ui-draggable-handle">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="create-procedure-modal-label"> Ask For Review </h4>
                     </div>
                     <div class="modal-body">
                        <div class="form-group">
                           <label for="comment">Your comment:</label>
                           <textarea class="form-control" name="comment" cols="50" rows="10"></textarea>
                        </div>
                        <h3> Select users to ask review from </h3>
                        <div class="checkbox">
                          <?php 
                            $allUsers = User::where('id', '!=', Auth::user()->id)->get();
                          ?>
                          <?php if ($allUsers): ?>
                            <?php foreach ($allUsers as $key => $user): ?>
                              <?php $userAccess =  DB::table('user_department')->where([['user_id', $user->id],['department_id', $dataTypeContent->department_id],['role', 'publisher']])->first(); ?>
                              <?php if (!$userAccess): ?>
                                <?php continue; ?>
                                <?php else: ?>
                                  <label>
                                     <div class="checker">
                                       <span>
                                         <input type="checkbox" class="users-approval-checkbox" value="{{ $user->id }}" name="users[]">
                                       </span>
                                     </div>
                                     {{ $user->name }}
                                  </label>
                              <?php endif ?>
                            <?php endforeach ?>
                          <?php endif ?>
                        </div>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button id="submit_for_review_button" type="submit" class="btn btn-danger"><i class="fa fa-cogs"></i>  Request review </button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
         <!-- Modal -->
         <div class="modal fade" id="rename-procedure-modal-{{ $dataTypeContent->id }}" tabindex="-1" role="dialog" aria-labelledby="rename-procedure-modal-{{ $dataTypeContent->id }}-label" aria-hidden="true">
            <div class="modal-dialog ui-draggable">
               <div class="modal-content">
                  <form method="POST" action="/procedure/{{ $dataTypeContent->id }}" accept-charset="UTF-8">
                     @csrf
                     <div class="modal-header ui-draggable-handle">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="rename-procedure-modal-label"> Rename Procedure </h4>
                     </div>
                     <div class="modal-body">
                        <div class="form-group">
                           <div class="form-group">
                              <label for="name">New procedure name</label>
                              <input class="form-control" name="name" type="text" value="proc name" id="name">
                           </div>
                           <div class="form-group">
                              <label for="description">Procedure goal / description</label>
                              <textarea class="form-control" name="description" cols="50" rows="10" id="description">proc desc</textarea>
                           </div>
                        </div>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"> Rename Procedure </button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
         <!-- paragraph Modal -->
         <div class="modal fade" id="block-editor-modal-container" tabindex="-1" role="dialog" aria-labelledby="block-editor-modal-container-label" aria-hidden="true" style="display: none;">
            <div class="modal-dialog ui-draggable">
               <div class="modal-content">
                  <div class="modal-header ui-draggable-handle">
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                     <span style="display: none;" id="modal-loader" class="pull-right"><i class="fa fa-cog fa-spin"></i> Loading...</span>
                     <h4 class="modal-title" id="block-editor-modal-container-label">Edit Paragraph</h4>
                  </div>
                  <div class="modal-body">
                     <form method="POST" action="" class="block-editor-form" accept-charset="UTF-8">
                      <input type="hidden" class="procedure_data_id" />
                      <input type="hidden" class="modal_name" value="#block-editor-modal-container" />
                      <textarea name="text" id="tinymceEditor" class="richTextBox" cols="10" rows="5"></textarea>
                     <div id="modal-footer-Text_65" class="modal-footer modal-tweak-footer">
                         <div id="buttons-container-Text_65" class="btn-group-tweaks pull-right">
                            <button id="save-button-Text_65" data-procedure="" type="submit" class="btn btn-primary pull-right textblock-save-button">
                              <span class="save-button-label">Save</span>
                            </button>
                         </div>
                     </div>

                     </form>
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
                     <span style="display: none;" id="modal-loader" class="pull-right"><i class="fa fa-cog fa-spin"></i> Loading...</span>
                     <h4 class="modal-title" id="block-editor-modal-container-label">Upload Image</h4>
                  </div>
                  <div class="modal-body">
                      <input type="hidden" class="procedure_data_id" />
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
                     <div id="modal-footer-Image_17" class="modal-footer modal-tweak-footer">
                        <div id="buttons-container-Image" class="btn-group-tweaks pull-right">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End Image Modal -->

         <!-- Attachment Modal -->
         <div class="modal fade" id="block-attachment-modal-container" tabindex="-1" role="dialog" aria-labelledby="block-image-modal-container-label" aria-hidden="true">
            <div class="modal-dialog ui-draggable">
               <div class="modal-content">
                  <div class="modal-header ui-draggable-handle">
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                     <span style="display: none;" id="modal-loader" class="pull-right"><i class="fa fa-cog fa-spin"></i> Loading...</span>
                     <h4 class="modal-title" id="block-editor-modal-container-label">Upload Attachment</h4>
                  </div>
                  <div class="modal-body">
                     <input type="hidden" class="procedure_data_id" />
                     <div id="my-dropzone-attachment" class="my-dropzone">
                       <div class="dropzone needsclick dz-clickable dz-message">
                           Drop a file here or click to upload.
                       </div>
                     </div>
                     <div class="progress">
                        <div class="progress-bar progress-bar-primary" role="progressbar" data-dz-uploadprogress_2>
                        </div>
                     </div>
                     <div id="modal-footer-attachment" class="modal-footer modal-tweak-footer">
                        <div id="buttons-container-attachment" class="btn-group-tweaks pull-right">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End Attachment Modal -->

         <!-- Procedure Modal -->
         <div class="modal fade" id="block-procedure-modal-container" tabindex="-1" role="dialog" aria-labelledby="block-image-modal-container-label" aria-hidden="true">
            <div class="modal-dialog ui-draggable">
              <form id="procedure_embed_form" class="procedure_embed_form">
                 <div class="modal-content">
                    <div class="modal-header ui-draggable-handle">
                        <input type="hidden" class="procedure_data_id" />
                       <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                       <span style="display: none;" id="modal-loader" class="pull-right"><i class="fa fa-cog fa-spin"></i> Loading...</span>
                       <h4 class="modal-title" id="block-editor-modal-container-label">Select a Procedure to Embed</h4>
                    </div>
                    <div class="modal-body">
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
                    </div>
                    <div id="modal-footer-Procedure" class="modal-footer modal-tweak-footer">
                        <div id="buttons-container-Procedure" class="btn-group-tweaks pull-right">
                          <button type="submit" id="save-button-Procedure" class="btn btn-dark select-procedure-selector"> Embed Procedure </button>
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
                     <span style="display: none;" id="modal-loader" class="pull-right"><i class="fa fa-cog fa-spin"></i> Loading...</span>
                     <h4 class="modal-title" id="block-video-modal-container-label">Embed Video</h4>
                  </div>
                  <div class="modal-body">
                     <div>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" style="background-color: #fff !important;" role="tablist">
                           <li role="presentation" class="active"><a class="tab_link_button" href="#embed" data-button="Embed" data-modal_title="Embed Video" id="embed-tab-link" aria-controls="embed" role="tab" data-toggle="tab" aria-expanded="false">Embed video</a></li>
                           <li role="presentation" class=""><a class="tab_link_button" href="#upload" data-button="Save" data-modal_title="Upload Video" id="upload-tab-link" aria-controls="upload" role="tab" data-toggle="tab" aria-expanded="true">Upload video</a></li>
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
                              <input type="hidden" class="procedure_data_id"/>
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
                        <div id="block-preview-Video" style="display: none;">
                           <div id="procedure-block-content-container-Video" class="procedure-block-content-container">
                              <h3 align="center"> No video uploaded </h3>
                           </div>
                        </div>
                        <div id="modal-footer-Video" class="modal-footer modal-tweak-footer">
                           <div id="buttons-container-Video" class="btn-group-tweaks pull-right">
                              <button id="save-button-Video" type="submit" class="btn btn-primary pull-right textblock-save-button">Embed</button>
                           </div>
                        </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End Video Modal -->

      </div>
   </div>
</div>
<!-- Rename Modal -->
<div class="modal fade" id="rename-procedure-modal" tabindex="-1" role="dialog" aria-labelledby="rename-procedure-modal-label" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <form method="POST" action="/proc_fold_rename" accept-charset="UTF-8">
            @csrf
            <input type="hidden" name="type_id" value="{{ $dataTypeContent->id }}" />
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
               <h4 class="modal-title" id="rename-department-modal-label-5">Rename Procedure</h4>
            </div>
            <div class="modal-body">
               <div class="form-group">
                  <label for="name">Procedure name</label>
                  <input class="form-control" name="name" type="text" value="{{ $dataTypeContent->name }}" id="name" required>
               </div>
               <div class="form-group">
                  <label for="description">Procedure goal / description</label>
                  <textarea class="form-control" name="description" cols="50" rows="10" id="description" required>{{ $dataTypeContent->description }}</textarea>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-primary">Rename Procedure</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- Rename Modal -->
@stop
@section('javascript')
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
   var deleteFormAction;
   $('.delete').on('click', function (e) {
       var form = $('#delete_form')[0];
   
       if (!deleteFormAction) {
           // Save form action initial value
           deleteFormAction = form.action;
       }
   
       form.action = deleteFormAction.match(/\/[0-9]+$/)
           ? deleteFormAction.replace(/([0-9]+$)/, $(this).data('id'))
           : deleteFormAction + '/' + $(this).data('id');
   
       $('#delete_modal').modal('show');
   });
   

   // $('.moveButton').on('click',  function(e) {
   //     e.preventDefault();
   //     var $this = $(this);
   //     var model = $this.attr('data-target');
   //     var type = $this.attr('data-type');
   //     var id = $this.attr('data-id');
   
   //     var typeCapitalized = type.charAt(0).toUpperCase() + type.slice(1);
   
   //     $(model+' h4.modal-title').text('Move '+typeCapitalized);
   //     $(model+' button.moveButton').text('Move '+typeCapitalized);
   
   //     $(model).modal('show');
   
   // });
   
   // var breadcrumbs = '';
   
   // $($(".breadcrumb_li").get().reverse()).each(function(index, el) {
   //   breadcrumbs += '<li class="active breadcrumb_li">'+$(el).html()+'</li>';
   // });
   
   // $('.breadcrumb_li').remove();
   // $(breadcrumbs).insertAfter('li.department_li_breadcrumb');
   
</script>
<script>
   // function setUpBlock(params) {
       // if (typeof params == 'undefined') {
       //     // the variable is defined
       //     var params = {
       //         success: function () {
       //             console.log('DEFAULT SUCCESS')
       //         },
       //         complete: function () {
       //             console.log('DEFAULT COMPLETE')
       //         }
       //     }
       // }
   
       // if (typeof params.complete  == 'undefined') {
       //     // the variable is defined
       //     params.complete =  function () {
       //         console.log('DEFAULT COMPLETE')
       //     }
       // }
   
       // if (typeof params.success  == 'undefined') {
       //     // the variable is defined
       //     params.success =  function () {
       //         console.log('DEFAULT SUCCESS')
       //     }
       // }
   
       // if (typeof params.setup  == 'undefined') {
       //     // the variable is defined
       //     params.setup =  function () {
       //         console.log('DEFAULT BLOCK LOADED')
       //     }
       // }
   
   
       // params.setup('Video_8','');


       // //setup the events for tab switching
       // $('#embed-tab-link').off().on('shown.bs.tab', function (e) {
   
       //     $('#save-button-Video_8').html('Embed');
       //     setModalTitle('Embed Video');
   
       //     $('#save-button-Video_8').off().on('click', function (e) {
       //         e.preventDefault();
       //         $.ajax({
       //             url:'/BlockVideo/8/update',
       //             type:'POST',
       //             data:'url=' + encodeURIComponent($('#url-8').val()) + '&type=embed',
       //             success: function (data) {
       //                params.success(data,e);
       //             },
       //             complete: function (a,b) {
       //                 params.complete(a,b);
       //             }
       //         });
       //         return false;
       //     });
       //     //we save it expand the block and reload
       // });
   
   
       // $('#upload-tab-link').off().on('shown.bs.tab', function (e) {
       //     $('#save-button-Video_8').html('Save');
   
       //     setModalTitle('Upload Video');
   
       //     var url = '/BlockVideo/8/update';
       //     $('#videoupload-show-Video_8').fileupload({
       //         url: url,
       //         dataType: 'json',
       //         acceptFileTypes: /(\.|\/)(mp4|avi|ogg|mov)$/i,
       //         maxFileSize: 1073741824,
       //         type:'POST',
       //         formData: {
       //             _token: 'yPSLpab4jL6CNtpjv8NHEv7GJlk9FUUSSlrA9mGT'
       //             },
       //         start: function () {
       //             $('#progress_Video_8').show('fast');
       //             $('#file-input-button-Video_8').hide();
       //         },
       //         done: function (e, data) {
       //                             params.success(data,e);
       //         },
       //         progressall: function (e, data) {
       //             var progress = parseInt(data.loaded / data.total * 100, 10);
       //             $('#progress_Video_8 .progress-bar').css(
       //                 'width',
       //                 progress + '%'
       //             );
       //         }
       //     }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
       // });
   
       // $('#upload').off().on('hide.bs.tab', function (e) {
       //     $('#videoupload-show-Video_8').fileupload('destroy');
       // });
   
       //  $('#embed-tab-link').trigger('shown.bs.tab');
       //  hideModalLoader();
   
       // }

       jQuery(document).ready(function($) {

          // select 2 
          $('#procedure_model_select').select2({
            placeholder: "Type or click here",
          });

         
           $(document).on('click', '.visibility_edit', function(e) {
            e.preventDefault();
            var $this = $(this);
            var fontAwsom = $this.find('i');
            var id = $this.attr('data-id');
            var collapse = $this.attr('data-target');
            var iconClass = fontAwsom.attr('class');
            if (iconClass == 'fas fa-chevron-down') {
               fontAwsom.attr('class', 'fas fa-chevron-up');
            } else {
               fontAwsom.attr('class', 'fas fa-chevron-down');
            }

            $.ajax({
              url: '/procedure/expand',
              type: 'POST',
              data: {
                "_token": "{{ csrf_token() }}",
                'id': id,
              },
            });
            
           });

           // by default check collapsed or not 
           var collapsedCheck = $('.panel-collapse.in').length;
           if (collapsedCheck > 0) {

            $('.openCloseall span').text('Collapse All');
            $('.openCloseall i').attr('class', 'fa fa-minus-square');

           } else {
            $('.openCloseall span').text('Expand All');
            $('.openCloseall i').attr('class', 'fa fa-plus-square');
           }

           // toggle accordion collapsed 
           $('.openCloseall').click(function(e){
               e.preventDefault();

              var collapsedCheck = $('.panel-collapse.in').length;
              if (collapsedCheck > 0) {
                 $('.panel-collapse.in').collapse('hide');
                 $('.openCloseall span').text('Expand All');
                 $('.openCloseall i').attr('class', 'fa fa-plus-square');

              } else {
                $('.panel-collapse:not(".in")').collapse('show');
               
               $('.openCloseall span').text('Collapse All');
               $('.openCloseall i').attr('class', 'fa fa-minus-square');
              }

           });


          // Drag and drop
          $( "#accordion" ).sortable({
            placeholder: "ui-sortable-placeholder",
              start: function(e, ui){
                  ui.placeholder.height(ui.item.height());
              },
              update: function( ) {

                checkStep();
                
                $('.panel.panel-default').each(function(index, el) {
                  var id = $(el).attr('data-id');
                  var order = index+1;

                  var data = {id: id, order:order};
                  updateProcedureData(data);
                });
              }

          });


          // clone block
          $(document).on('click', '.block-copy-to-clipboard', function(e) {
            e.preventDefault();
            var $this = $(this);
            var id = $this.attr('data-id');
            
            var elem = $this.parents().eq(3);
            $(elem).clone(true).insertAfter(elem);
            /* Act on the event */

            checkStep();

            $.ajax({
              url: '/procedure/cloneBlock',
              type: 'POST',
              data: {
                "_token": "{{ csrf_token() }}",
                'id': id,
              },
            })
            .done(function(response) {
              var newId = response;
              var newElement = $('.procedure_data_id_'+id).last();
              newElement.addClass('procedure_data_id_'+newId).removeClass('procedure_data_id_'+id).attr('data-id', newId);
              newElement.find('.tools .btn-step').attr('data-id', newId);
              newElement.find('.tools .block-copy-to-clipboard').attr('data-id', newId);
              newElement.find('.tools .visibility_edit').attr({
                'data-id': newId,
                'data-target': '#collapse_'+newId
              });
              newElement.find('.tools .BlockText-edit-button').attr('data-procedure_id', newId);
              newElement.find('.tools .remove-block-button').attr('data-id', newId);
              newElement.find('#collapse_'+id).attr('id', 'collapse_'+newId);
              newElement.find('.dropzone-prev-'+id).addClass('dropzone-prev-'+newId).removeClass('dropzone-prev-'+id);
              newElement.find('#slider-Image_'+id).attr('id', 'slider-Image_'+newId).removeAttr('class').html('');
              newElement.find('.panel-body .btn-image-align').attr('data-id', newId);
              newElement.find('.image-show-').addClass('image-show-alignment-container'+newId).removeClass('image-show-alignment-container'+id).attr('id', 'image-show-alignment-container-Image_'+newId);
              newElement.find('.image-show- img').attr('id', 'block_image-Image_'+newId);
              newElement.find('#block-contents-Attachment_'+id).attr('id', 'block-contents-Attachment_'+newId);
              newElement.find('#procedure-block-'+id).attr('id', 'procedure-block-'+newId);
              newElement.find('#block-contents-Video_'+id).attr('id', 'block-contents-Video_'+newId);
              newElement.find('#procedure-block-'+id).attr('id', 'procedure-block-'+newId);
              newElement.find('.tools .permanently-delete-block-button').attr('data-id', newId);
              newElement.find('.tools .unremove-block-button').attr('data-id', newId);
              newElement.find('.block-contents-Procedure_'+id).addClass('block-contents-Procedure_'+newId).removeClass('block-contents-Procedure_'+id);



              var rangeSlider = $('#slider-Image_'+newId)[0];


              if($(rangeSlider).length > 0) {
              var start = newElement.find('#block_image-Image_'+newId).attr('width');

                noUiSlider.create(rangeSlider, {
                    start: [start],
                    range: {
                        'min': [50],
                        'max': [1000]
                    }
                });

                var rangeSliderValueElement = $('.image-show-alignment-container'+newId+' img');
                var fullImageLink = rangeSliderValueElement.attr('src');
                var n = fullImageLink.indexOf("_images/");
                var imageLink = fullImageLink.substring(n+8);

                procedure_data_imag = `
                  <div class="image-show- image-show-alignment-container`+newId+`" id="image-show-alignment-container-Image_`+newId+`">
                    <img id="block_image-Image_`+newId+`" style="margin-top:1em;" src="/procedure_images/`+imageLink+`">
                  </div>
                `;
                rangeSlider.noUiSlider.on('update', function (values, handle) {
                    rangeSliderValueElement.attr('width', values[handle]);

                });
                rangeSlider.noUiSlider.on('end', function (values, handle) {

                  var alignDiv = $('.image-show-alignment-container'+newId).attr('style');
                  if(alignDiv != undefined) {
                    var n = alignDiv.indexOf("-align:");
                    var align = alignDiv.substring(n+7);
                  } else {
                    var align = 'center';
                  }

                    // Update Procedure Data
                     var obj = { size: values.toString(),  align: align, file: imageLink };
                     var myJSON = JSON.stringify(obj);
                     var data = {id: newId, content: procedure_data_imag, additional_data: myJSON};
                     updateProcedureData(data);
                });

              }

            });
          });


          // delete procedure data
          $(document).on('click', '.remove-block-button', function(e) {
            e.preventDefault();
            var $this = $(this);
            var status = 'deactive';
            var id = $this.attr('data-id');
            var panel = $this.parents().eq(2);

            $this.parent().addClass('hidden').next().removeClass('hidden');
            $('#collapse_'+id).removeClass('in');

            $('#collapse_'+id).collapse('hide');

            panel.attr('style', 'background: #a99b9b !important;');

            changeStatus(id, status);

          });


          // Undo delete procedure data
          $(document).on('click', '.unremove-block-button', function(e) {
            e.preventDefault();
            var $this = $(this);
            var status = 'active';
            var id = $this.attr('data-id');
            var panel = $this.parents().eq(2);

            panel.attr('style', 'background: red !important;');
            
            $('#collapse_'+id).collapse('show');

            var tools = $this.parent().addClass('hidden').prev();

            tools.removeClass('hidden');

            var step = tools.find('.btn-step').attr('data-step');

            if(step == '1') {
              panel.attr('style', 'background: #3598dc !important;');
            } else {
              panel.attr('style', 'background: #1bbc9b !important');
            }

            changeStatus(id, status);

          });

          // delete permenently
          $(document).on('click', '.permanently-delete-block-button', function(e) {
            e.preventDefault();
            var $this = $(this);
            var id = $this.attr('data-id');
            
            var confirming = confirm("Are you sure you want to permanently delete this block?");

            if(confirming == true) {
              $.ajax({
                url: '/procedure/permenentDelete',
                type: 'POST',
                data: {
                      "_token": "{{ csrf_token() }}",
                      'id': id,
                    }
              }).done(function(response) {
                
                $('.procedure_data_id_'+id).fadeOut('slow', function() {
                  $(this).remove();
                });
              });
            }
            
          });


          
          // add text block paragraph
          $('.create-block-button-standard').on('click', function(e) {
            e.preventDefault();
            var $this = $(this);
            var type = $this.attr('data-blockinfo');
            var model = $this.attr('data-model');
            var accordionHeading = '';
            var accordiontext = '';
            var previewClass = '';
            var modal = '';
            var order = $('.panel.panel-default').length + 1;
            $(model).modal('show');

            //send ajax request and return id
            $.ajax({
              url: '/procedure/addNewProcedureData',
              type: 'POST',
              data: {
                "_token": "{{ csrf_token() }}",
                'procedure_id': "{{ $dataTypeContent->id }}",
                'type': type,
                'order': order
              },
            })
            .done(function(response) {
              var id = response;
              if (type == 'BlockText') {
                accordionHeading = 'No text inside paragraph';
                modal = '#block-editor-modal-container';
              } else if(type == 'BlockImage') {
                accordionHeading = 'No image uploaded';
                accordiontext = '<h3 align="center"> No image uploaded </h3>';
                previewClass = 'dropzone-prev-'+id;
                modal = '#block-image-modal-container';
              } else if(type == 'BlockAttachment') {
                accordionHeading = 'No file uploaded';
                accordiontext = '<h3 align="center"> No file uploaded </h3>';
                modal = '#block-attachment-modal-container';
                previewClass = 'dropzone-prev-'+id;
              } else if(type == 'BlockProcedure') {
                accordionHeading = 'No procedure attached';
                accordiontext = '<h3 align="center"> No procedure selected for embedding. </h3>';
                modal = '#block-procedure-modal-container';
                previewClass = 'dropzone-prev-'+id;
                $('#procedure_model_select').val('').trigger('change');
              } else if(type == 'BlockVideo') {
                $(model).find('#embedUrl').val('');
                accordionHeading = 'No video uploaded or embedded';
                accordiontext = '<h3 align="center"> No video uploaded </h3>';
                previewClass = 'dropzone-prev-'+id;
                modal = '#block-video-modal-container';
              }

              paragraph = `
              <div class="panel panel-default procedure_data_id_`+id+`" data-id="`+id+`">
                 <div class="panel-heading">
                   <h4 class="panel-title">
                     <div class="accordion-toggle caption_proc">
                      <i class="fas fa-th-list" style="margin-right: 5px"></i>
                       <span>`+accordionHeading+`</span>
                     </div>
                     <div class="tools">
                        <a class="btn-step" data-step="" style="margin: 2px;" href="#" data-id="`+id+`"> <i class="fa fa-tasks"></i> <span class="step-string"> Mark as step</span> </a>
                        <a href="#" style="margin: 2px;" data-id="`+id+`" class="block-copy-to-clipboard" title="Duplicate block"><i class="fas fa-copy"></i></a>
                        <a class="visibility_edit" data-id="`+id+`" style="margin: 2px;" data-toggle="collapse" data-target="#collapse_`+id+`">
                        <i class="fas fa-chevron-down"></i></a>
                        <a class="block-edit-button BlockText-edit-button" data-procedure_type="`+type+`" style="margin: 2px;" href="`+modal+`" data-procedure_id="`+id+`"><i class="fa fa-edit"></i></a>
                        <a class="remove-block-button" href="#" data-id="`+id+`" style="margin: 2px;"><i class="fas fa-times"></i></a>
                     </div>
                    <div class="tools-deleted pull-right hidden">
                        <a data-blockinfo="" data-id="`+id+`" class="unremove-block-button btn btn-dark" href="javascript:;"><i class="fas fa-long-arrow-alt-left"></i> Undo delete </a>
                        <a data-blockinfo="" data-id="`+id+`" class="permanently-delete-block-button btn btn-danger pull-right" href="javascript:;"><i class="fas fa-times"></i></a>
                    </div>
                   </h4>
                 </div>
                 <div id="collapse_`+id+`" class="panel-collapse collapse in">
                   <div class="panel-body `+previewClass+`">
                   `+accordiontext+`
                   </div>
                 </div>
               </div>
              `;
               $('#accordion.panel-group').append(paragraph);
               $(model+' .procedure_data_id').val(id);
               $(model).modal('show');
            })
            .fail(function() {
              location.reload(true);
            })

          });


          // text paragraph on submit
          $('.block-editor-form').on('submit', function(e) {
            e.preventDefault();
            var $this = $(this);
            var modal = $this.find('.modal_name').val();
            var procedure_data_id = $this.find('.procedure_data_id').val();
            var procedure_data_textblock = $this.find('.richTextBox').val();

            var text = procedure_data_textblock.replace(/(<([^>]+)>)/g, "");

            if(text.trim().length > 0) {
              
              if(text.trim().length > 30){
                text = text.substring(0,30)+'...'; 
              }
              $('.procedure_data_id_'+procedure_data_id+ ' .caption_proc span').html(text);
              $('.procedure_data_id_'+procedure_data_id+ ' .panel-body').html(procedure_data_textblock);
            } else {
              $('.procedure_data_id_'+procedure_data_id+ ' .caption_proc span').html('No text inside paragraph');
              $('.procedure_data_id_'+procedure_data_id+ ' .panel-body').html('');
            }

            // Update Procedure Data
            var data = {id: procedure_data_id, content: procedure_data_textblock};
            updateProcedureData(data);

            $(modal).modal('hide');

            var tinymce_editor_id = 'tinymceEditor';
            tinymce.get(tinymce_editor_id).setContent('');

          });

          // edit procedure Data button
          $(document).on('click', '.BlockText-edit-button', function(e) {
            e.preventDefault();
            var $this = $(this);
            var model = $this.attr('href');
            var procedure_data_id = $this.attr('data-procedure_id');
            var procedureType = $this.attr('data-procedure_type');
            $(model+' .procedure_data_id').val(procedure_data_id);
            if (procedureType == 'BlockText') {
              var modelParagraphContent = $('.procedure_data_id_'+procedure_data_id+' .panel-body').html();
              var tinymce_editor_id = 'tinymceEditor';
              tinymce.get(tinymce_editor_id).setContent(modelParagraphContent); 
            } else if(procedureType == 'BlockImage'){

            } else if(procedureType == 'BlockAttachment'){
              
            } else if(procedureType == 'BlockProcedure'){
              
            } else if(procedureType == 'BlockVideo'){
              var data_link = $this.attr('data-link');
              $(model+' #embedUrl').val(data_link);
            }

            $(model).modal('show');

          });


          // image alignment change with buttons
          $(document).on('click', '.btn-image-align', function(e) {
            e.preventDefault();
            var $this = $(this);
            var imgID = $this.attr('data-id');
            var align = $this.attr('data-align');
            $this.siblings().removeClass('selected');
            $this.addClass('selected');
            $('.image-show-alignment-container'+imgID).attr('style', 'text-align:'+align);

            var imageSize = $('.image-show-alignment-container'+imgID+ ' img').attr('width');

            var fullImageLink = $('.image-show-alignment-container'+imgID+ ' img').attr('src');

            var n = fullImageLink.indexOf("_images/");
            
            var imageLink = fullImageLink.substring(n+8);

            procedure_data_id = imgID;

            var procedure_data_imageblock = $('.procedure_data_id_'+imgID+' .panel-body').html();


            procedure_data_imag = `
              <div class="image-show- image-show-alignment-container`+procedure_data_id+`" id="image-show-alignment-container-Image_`+procedure_data_id+`" style="text-align:`+align+`">
                <img id="block_image-Image_`+procedure_data_id+`" style="margin-top:1em;" src="/procedure_images/`+imageLink+`">
              </div>
            `;
            // Update Procedure Data
            var obj = { size: imageSize, align: align, file: imageLink };
            var myJSON = JSON.stringify(obj);
            var data = {id: procedure_data_id, content: procedure_data_imag, additional_data: myJSON};
            updateProcedureData(data);

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
            var procedureSelected = $this.find('#procedure_model_select').val();
            var procedure_data_id = $this.find('.procedure_data_id').val();
            var procedure_data_name = $this.find('#procedure_model_select option:selected').text();

            if (procedureSelected == '') {
              alert('Please select a procedure');
            } else {

              $(".procedure_data_id_"+procedure_data_id+' .panel-body').html('<h3 align="center"> No video uploaded </h3>');

              var procedure_data_attach_procedure = `<div id="block-contents-Procedure_`+procedure_data_id+`" class="portlet-body procedure-block-body" style="display:block">
                 <h4><i class="fa fa-file-text"></i> <a target="_blank" href="/procedure/`+procedureSelected+`">External Procedure: `+procedure_data_name+`</a> <i class="fa fa-arrow-right"></i></h4>
              </div>`;
              $('.dropzone-prev-'+procedure_data_id).html(procedure_data_attach_procedure);

              var text = procedure_data_name;

              if(text.length > 30){
                text = text.substring(0,30)+'...';
              }


              $('.procedure_data_id_'+procedure_data_id+' .caption_proc span').text(text);
              $this.closest('.modal').modal('hide');
              $('#procedure_model_select').val('').trigger('change');


              // Update Procedure Data
              var obj = { embed: procedureSelected};
              var myJSON = JSON.stringify(obj);
              var data = {id: procedure_data_id, content: procedure_data_attach_procedure, attach:procedure_data_name, additional_data: myJSON};
              updateProcedureData(data);
            }
          });

          // embed youtube or vimeo Video on procedure
          $('#video_embed_form').on('submit', function(e) {
            e.preventDefault();
            var $this = $(this);
            var procedure_data_id = $this.find('.procedure_data_id').val();
            var embedUrl = $('#embedUrl').val();
            if (embedUrl.toLowerCase().indexOf("youtube") > -1 && embedUrl.length > 41) {
              var videoId = embedUrl.substr(embedUrl.indexOf("watch?v=") + 8);
              if (videoId.length > 10) {
                var videoIframe = `<div class="procedure-block" id="procedure-block-`+procedure_data_id+`"><iframe width="480" height="295" frameborder="0" allowfullscreen src="https://www.youtube.com/embed/`+ videoId+`?wmode=transparent"></iframe></div>`;
                    $(".procedure_data_id_"+procedure_data_id+' .panel-body').html(videoIframe);
              }
            } else if (embedUrl.toLowerCase().indexOf("vimeo") > -1 && embedUrl.length > 25) {
              var videoId = embedUrl.substr(embedUrl.indexOf("vimeo.com/") + 10);

              if (videoId.length > 8) {
                var videoIframe = `<div class="procedure-block" id="procedure-block-`+procedure_data_id+`"><iframe width="480" height="295" frameborder="0" allowfullscreen src="http://player.vimeo.com/video/`+ videoId+`?wmode=transparent"></iframe></div>`;
                    $(".procedure_data_id_"+procedure_data_id+' .panel-body').html(videoIframe);
              }

            } else {
              $(".procedure_data_id_"+procedure_data_id+' .panel-body').html('<h3 align="center"> No video uploaded </h3>');
            }

            // update procedure data 
            var data = {id: procedure_data_id, content: videoIframe, attach:embedUrl};
            updateProcedureData(data);

            $('.procedure_data_id_'+procedure_data_id+' .caption_proc span').text('Embedded video');
            $(".procedure_data_id_"+procedure_data_id+' .panel-heading .tools a.BlockText-edit-button').attr('data-link', embedUrl);

            $('#embedUrl').val('');
            $('#block-video-modal-container').modal('hide');
          });

          // Mark/Unmark Step
          $(document).on('click', '.btn-step', function(e) {
            e.preventDefault();
            var $this = $(this);
            var id = $this.attr('data-id');
            var step = $this.attr('data-step');

            var heading = $('.procedure_data_id_'+id+' .panel-heading');

            if(step != '1') {
              $this.attr('data-step', '1');
              heading.attr('style', 'background: #3598dc !important;');
            } else {
              $this.attr('data-step', '');
              heading.attr('style', 'background: #1bbc9b !important;');
            }

            checkStep();

            $.ajax({
              url: '/procedure/markStep',
              type: 'POST',
              data: {
                "_token": "{{ csrf_token() }}",
                'id': id,
                'step': step
              },
            })
            .done(function(response) {
            });


          });

        });

</script>

<script type="text/javascript">

       // dropzone work form image block
      $("#my-dropzone").dropzone({
        autoDiscover: false,
        url: "/dropzone/imageupload",
        maxFilesize: 10,
        maxFiles: 1,
        acceptedFiles: ".jpg,.png",
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        sending: function(file, xhr, formData) {
          formData.append('data_id', $($(this)[0].element).prev('.procedure_data_id').val());
        },
        uploadprogress: function(file, progress, bytesSent) {
          var progressElement = $("[data-dz-uploadprogress_1]");
              progressElement.css('width', progress + "%");
        },
        addedfile: function() {
            var $this = $($(this)[0].element);
            $this.hide();
            $this.next('.progress').show();
        },
        success: function(file, response) 
        {
            var progressElement = $("[data-dz-uploadprogress_1]");
            var $this = $($(this)[0].element);
            var procedure_data_id = $this.prev('.procedure_data_id').val();
            procedure_data_imageblock = `
              <div class="row">
                  <div class="col-md-2">
                      <p>Image Size</p>
                  </div>
                  <div class="col-md-8">
                      <div id="slider-Image_`+procedure_data_id+`"></div>
                  </div>
                  <div class="col-md-2">
                      <div class="btn-group btn-group-xs btn-group-justified">
                          <a class="btn btn-default btn-image-align" data-id="`+procedure_data_id+`" data-align="left" href="javascript:;"><i class="fa fa-align-left"></i></a>
                          <a class="btn btn-default btn-image-align" data-id="`+procedure_data_id+`" data-align="center" href="javascript:;"><i class="fa fa-align-center"></i></a>
                          <a class="btn btn-default btn-image-align" data-id="`+procedure_data_id+`" data-align="right" href="javascript:;"><i class="fa fa-align-right"></i></a>
                      </div>
                  </div>
              </div>`;
            procedure_data_imag = `
              <div class="image-show- image-show-alignment-container`+procedure_data_id+`" id="image-show-alignment-container-Image_`+procedure_data_id+`">
                <img id="block_image-Image_`+procedure_data_id+`" style="margin-top:1em;" src="/procedure_images/`+response.success+`">
              </div>
            `;
            $('.dropzone-prev-'+procedure_data_id).html(procedure_data_imageblock+procedure_data_imag);

            $('.procedure_data_id_'+procedure_data_id+' .caption_proc span').text(response.original_name);
            $this.closest('.modal').modal('hide');
            
            // Update Procedure Data
            var obj = { size: "475", align: "center", file: response.success };
            var myJSON = JSON.stringify(obj);
            var data = {id: procedure_data_id, content: procedure_data_imag, attach:response.original_name, additional_data:myJSON};
            updateProcedureData(data);

            $this.show();
            $this.next('.progress').hide();
            progressElement.css('width', 0);
            this.removeAllFiles(true);

            var rangeSlider = $('#slider-Image_'+procedure_data_id)[0];

            noUiSlider.create(rangeSlider, {
                start: [475],
                range: {
                    'min': [50],
                    'max': [1000]
                }
            });

            var rangeSliderValueElement = $('.image-show-alignment-container'+procedure_data_id+' img');

            rangeSlider.noUiSlider.on('update', function (values, handle) {
                rangeSliderValueElement.attr('width', values[handle]);

            });
            rangeSlider.noUiSlider.on('end', function (values, handle) {

              var alignDiv = $('.image-show-alignment-container'+procedure_data_id).attr('style');
              if(alignDiv != undefined) {
                var n = alignDiv.indexOf("-align:");
                var align = alignDiv.substring(n+7);
              } else {
                var align = 'center';
              }

                // Update Procedure Data
                 var obj = { size: values.toString(), align: align, file: response.success };
                 var myJSON = JSON.stringify(obj);
                 var data = {id: procedure_data_id, additional_data: myJSON};
                 updateProcedureData(data);
            });
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
        url: "/dropzone/attachupload",
        maxFilesize: 500,
        maxFiles: 1,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        uploadprogress: function(file, progress, bytesSent) {
          var progressElement = $("[data-dz-uploadprogress_2]");
              progressElement.css('width', progress + "%");
        },
        addedfile: function() {
            var $this = $($(this)[0].element);
            $this.hide();
            $this.next('.progress').show();
        },
        success: function(file, response) 
        {
            var progressElement = $("[data-dz-uploadprogress_2]");
            var $this = $($(this)[0].element);
            var procedure_data_id = $this.prev('.procedure_data_id').val();
            procedure_data_attachblock = `
              <div id="block-contents-Attachment_`+procedure_data_id+`" class="portlet-body procedure-block-body" style="display:block">
                  <div class="procedure-block" id="procedure-block-`+procedure_data_id+`">
                    <a href="/block-attachments/`+response.success+`/download" class="download_attachment"> Download `+response.original_name+`</a>
                  </div>
              </div>
            `;
            $('.dropzone-prev-'+procedure_data_id).html(procedure_data_attachblock);

            $('.procedure_data_id_'+procedure_data_id+' .caption_proc span').text(response.original_name);
            $this.closest('.modal').modal('hide');

            // Update Procedure Data
            var data = {id: procedure_data_id, content: procedure_data_attachblock, attach:response.original_name};
            updateProcedureData(data);

            $this.show();
            $this.next('.progress').hide();
            progressElement.css('width', 0);
            this.removeAllFiles(true);
            
        },
        error: function(file, response)
        {
          var $this = $($(this)[0].element);
          var progressElement = $("[data-dz-uploadprogress_2]");
          $this.closest('.modal').modal('hide');
          $this.show();
          $this.next('.progress').hide();
          progressElement.css('width', 0);
          this.removeAllFiles(true);
          return false;
        }

      });

       // dropzone work form video block
      $("#my-dropzone-video").dropzone({
        autoDiscover: false,
        url: "/dropzone/attachvideo",
        maxFilesize: 500,
        acceptedFiles: ".mp4,.avi,.ogg",
        maxFiles: 1,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        uploadprogress: function(file, progress, bytesSent) {
          var progressElement = $("[data-dz-uploadprogress_3]");
              progressElement.css('width', progress + "%");
        },
        addedfile: function() {
            var $this = $($(this)[0].element);
            $this.hide();
            $this.next('.progress').show();
        },
        success: function(file, response) 
        {
            var progressElement = $("[data-dz-uploadprogress_3]");
            var $this = $($(this)[0].element);
            var procedure_data_id = $this.prev('.procedure_data_id').val();
            procedure_data_videoblock = `
              <div id="block-contents-Video_`+procedure_data_id+`" class="portlet-body procedure-block-body" style="display:block">
                  <video width="400" controls>
                    <source src="/procedure_videos/`+response.success+`" type="video/mp4">
                  </video>
              </div>
            `;
            $('.dropzone-prev-'+procedure_data_id).html(procedure_data_videoblock);

            $('.procedure_data_id_'+procedure_data_id+' .caption_proc span').text(response.original_name);
            $this.closest('.modal').modal('hide');

            // Update Procedure Data
            var data = {id: procedure_data_id, content: procedure_data_videoblock, attach:response.original_name};
            updateProcedureData(data);

            $this.show();
            $this.next('.progress').hide();
            progressElement.css('width', 0);
            this.removeAllFiles(true);
            
        },
        error: function(file, response)
        {
          var $this = $($(this)[0].element);
          var progressElement = $("[data-dz-uploadprogress_3]");
          $this.closest('.modal').modal('hide');
          $this.show();
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

      function checkStep() {
        $(".btn-step[data-step='1']").each(function(index, el) {
          $(this).find('span').html('Unmark as step '+(index+1));
        });

        $(".btn-step[data-step='']").each(function(index, el) {
          $(this).find('span').html('Mark as step');
        });
      }


      function changeStatus(id, status) {
        $.ajax({
          url: '/procedure/changeStatus',
          type: 'POST',
          data: {
                "_token": "{{ csrf_token() }}",
                'id': id,
                'status': status
              }
        });
      }

</script>
@stop