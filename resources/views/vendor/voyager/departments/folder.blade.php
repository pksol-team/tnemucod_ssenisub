<?php 
  use App\User;
  use App\Department;
?>
@extends('voyager::master')

@section('page_title', __('voyager::generic.view').' '.' Folder')

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
          <div class="clearfix"></div>
          <div class="panel panel-default" style="margin-top: 20px;">
             <div class="panel-heading" style="padding: 12px;">
                <div class="row">
                    <div class="col-xs-6" style="margin-top: 11px;font-size: 16px;"><i class="voyager-folder"></i> {{ $dataTypeContent->name }}</div>
                    <div class="col-xs-6 text-right" style="margin-bottom: 0;">
                          <a href="#rename-folder-modal" data-toggle="modal" data-target="#rename-folder-modal" class="btn btn-primary rename" id="rename_folder">
                              <i class="voyager-pen"></i> <span class="hidden-xs hidden-sm">Rename</span>
                          </a>
                          <a href="/folder/{{ $dataTypeContent->id }}/destroy" class="btn btn-danger delete" id="delete_folder">
                              <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">Delete</span>
                          </a>
                    </div>
                </div>
                
                </div>
             </div>
             <div class="panel-body" style="background: #fff;">
                <div class="btn-group">
                   <div class="dropdown">
                      <button class="btn btn-dark dropdown-toggle" type="button" id="departmentNewButtonDropdown" data-toggle="dropdown">
                      <i class="voyager-plus"></i> New
                      <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu" aria-labelledby="departmentNewButtonDropdown">
                         <li class="presentation" role="presentation">
                            <a href="#create-procedure-modal" role="menuitem" tabindex="-1" data-help="browser-create-new-procedure" data-toggle="modal" data-target="#create-procedure-modal">
                              <i class="voyager-file-text"></i> Procedure <br>
                              <span class="description">Create a new Procedure, that is ready to be used for your company.</span>
                            </a>
                         </li>
                         <li class="presentation" role="presentation">
                            <a href="#create-folder-modal" role="menuitem" tabindex="-1" data-help="browser-create-new-folder" data-toggle="modal" data-target="#create-folder-modal">
                              <i class="voyager-folder"></i> Folder <br>
                              <span class="description">Create a new Folder to help group procedures easier.</span>
                            </a>
                         </li>
                      </ul>
                   </div>
                </div>
                <!-- Folder Modal -->
                <div class="modal fade" id="create-folder-modal" tabindex="-1" role="dialog" aria-labelledby="create-folder-modal-label" aria-hidden="true">
                   <div class="modal-dialog">
                      <div class="modal-content">
                         <form method="POST" action="{{ URL::to('/folders') }}" accept-charset="UTF-8">
                            @csrf
                            <input type="hidden" name="department_id" value="{{ $dataTypeContent->department_id }}" />
                            <input name="type" type="hidden" value="folder">
                            <div class="modal-header">
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                               <h4 class="modal-title" id="create-folder-modal-label">Create new folder</h4>
                            </div>
                            <div class="modal-body">
                               <div class="form-group">
                                  <label for="name">Folder name</label>
                                  <input class="form-control" name="name" type="text" id="name" placeholder="Type Folder Name Here" required>
                               </div>
                               <input name="parent_id" type="hidden" value="{{ $dataTypeContent->id }}">
                            </div>
                            <div class="modal-footer">
                               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                               <button type="submit" class="btn btn-primary">Create folder</button>
                            </div>
                         </form>
                      </div>
                   </div>
                </div>
                <!-- End Folder Modal -->

                <!-- Procedure Modal -->
                <div class="modal fade" id="create-procedure-modal" tabindex="-1" role="dialog" aria-labelledby="create-procedure-modal-label" aria-hidden="true">
                   <div class="modal-dialog">
                      <div class="modal-content">
                         <form method="POST" action="{{ URL::to('/procedures') }}" accept-charset="UTF-8" id="create-procedure-modal-form">
                            @csrf
                            <input type="hidden" name="department_id" value="{{ $dataTypeContent->department_id }}" />
                            <input name="type" type="hidden" value="procedure">
                            <div class="modal-header">
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                               <h4 class="modal-title" id="create-procedure-modal-label">Create New Procedure</h4>
                            </div>
                            <div class="modal-body">
                               <div class="form-group">
                                  <label for="name">Procedure name</label>
                                  <input class="form-control" id="procedure-name-field" name="name" type="text" required>
                               </div>
                              <input name="type" type="hidden" value="procedure">
                               <input name="parent_id" type="hidden" value="{{ $dataTypeContent->id }}">
                               <div class="form-group">
                                  <label for="description">Procedure goal / description</label>
                                  <textarea class="form-control" name="description" cols="50" rows="10" id="description" required></textarea>
                               </div>
                            </div>
                            <div class="modal-footer">
                               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                               <button type="submit" id="create-procedure-button" class="btn btn-primary">Create Procedure</button>
                            </div>
                         </form>
                      </div>
                   </div>
                </div>
                <!-- End Procedure Modal -->

                <div class="form-group col-md-2 pull-right">
                   <input data-help="browse-filter-input" type="text" class="form-control" id="quick-filter" name="quick-filter" placeholder="Type to filter...">
                </div>
                <table id="item-listings" class="table table-hover">
                   <thead>
                      <tr>
                         <th width="1%"></th>
                         <th width="1%"></th>
                         <th width="1%"></th>
                         <th width="60%">Name</th>
                         <th width="10%">State</th>
                         <th width="10%">Owner</th>
                         <th width="25%">Last updated</th>
                      </tr>
                   </thead>
                   <tbody>
                      <!-- Then the activities -->
                      <!-- First the folders -->
                      <!-- Then the procedures -->
                      <?php $allProcNdFolds = DB::table('folder_and_procedure')->where([['department_id', $dataTypeContent->department_id], ['parent_id', $dataTypeContent->id], ['delete', NULL]])->orderBy('type', 'ASC')->orderBy('name', 'ASC')->get(); ?>
                      <?php if (count($allProcNdFolds) > 0): ?>
                        <?php foreach ($allProcNdFolds as $key => $procNdFold): ?>
                          
                          <tr class="procedure_department_listing">
                             <td><i data-help="browse-procedure-icon" class="{{ $procNdFold->type == 'folder' ? 'voyager-folder' : 'voyager-file-text' }}"></i></td>
                             <td>
                                <!-- check favourite or not -->
                                <?php $foundFavourite = DB::table('favourites')->where([['fold_proc_id', $procNdFold->id], ['user_id', Auth::user()->id], ['status', '1']])->first();
                                ?>
                                <!-- check favourite or not -->

                                <a data-help="browse-procedure-favorite" class="toggle-favorite" style="text-decoration: none;margin-left: 2px;" href="/{{ $procNdFold->type }}/{{ $procNdFold->id }}/toggle-favorite"><i class="voyager-star{{ $foundFavourite ? '-two' : ''}}"></i></a>
                             </td>
                             <td>
                                <div class="dropdown" style="display:inline">
                                   <i data-help="procedure-right-click-gear-icon" style="cursor: pointer" type="button" id="procedure-dropdown-{{ $procNdFold->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" class="voyager-settings"></i>
                                   <ul class="dropdown-menu" aria-labelledby="procedure-dropdown-{{ $procNdFold->id }}">
                                      <?php if ($procNdFold->type != 'folder'): ?>
                                        <li><a href="/procedure/{{ $procNdFold->id }}/edit">Edit</a></li>
                                        <li><a href="/procedures/{{ $procNdFold->id }}/duplicate">Duplicate</a></li>
                                      <?php endif ?>
                                      <!-- <li><a class="moveButton" data-target="#move-procedure-modal-{{ $procNdFold->id }}" data-type="{{ $procNdFold->type }}" data-id="{{ $procNdFold->id }}">Move</a></li> -->
                                      <li><a data-toggle="modal" data-target="#rename-{{ $procNdFold->type }}-modal-{{ $procNdFold->id }}">Rename</a></li>
                                      <li><a href="/{{ $procNdFold->type }}/{{ $procNdFold->id }}/destroy">Delete</a></li>
                                   </ul>
                                </div>
                                <!-- Various modals and addons required here -->
                                <!-- Modal -->
                                <div class="modal fade" id="move-procedure-modal-{{ $procNdFold->id }}" tabindex="-1" role="dialog" aria-labelledby="move-procedure-modal-label" aria-hidden="true">
                                   <div class="modal-dialog">
                                      <div class="modal-content">
                                         <form method="POST" action="/{{ $procNdFold->type }}/{{ $procNdFold->id }}/move" accept-charset="UTF-8">
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
                                <div class="modal fade" id="rename-{{ $procNdFold->type }}-modal-{{ $procNdFold->id }}" tabindex="-1" role="dialog" aria-labelledby="rename-{{ $procNdFold->type }}-modal-{{ $procNdFold->id }}-label" aria-hidden="true">
                                   <div class="modal-dialog">
                                      <div class="modal-content">
                                         <form method="POST" action="/proc_fold_rename" accept-charset="UTF-8">
                                            @csrf
                                            <input type="hidden" name="type_id" value="{{ $procNdFold->id }}" />
                                            <div class="modal-header">
                                               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                               <h4 class="modal-title" id="rename-{{ $procNdFold->type }}-modal-label"> Rename {{ ucfirst($procNdFold->type) }} </h4>
                                            </div>
                                            <div class="modal-body">
                                               <div class="form-group">
                                                  <div class="form-group">
                                                     <label for="name">New {{ $procNdFold->type }} name</label>
                                                     <input class="form-control" name="name" type="text" value="{{ $procNdFold->name }}" id="name" required>
                                                  </div>
                                                  <?php if ($procNdFold->type == 'procedure'): ?>
                                                  <div class="form-group">
                                                     <label for="description">Procedure goal / description</label>
                                                     <textarea class="form-control" name="description" cols="50" rows="10" id="description" required>{{ $procNdFold->description }}</textarea>
                                                  </div>
                                                  <?php endif ?>
                                               </div>
                                            </div>
                                            <div class="modal-footer">
                                               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                               <button type="submit" class="btn btn-primary"> Rename {{ ucfirst($procNdFold->type) }} </button>
                                            </div>
                                         </form>
                                      </div>
                                   </div>
                                </div>
                                <!-- Modal -->
                             </td>
                             <td>
                              <?php if ($procNdFold->type == 'folder'): ?>
                                <a class="item-name" href="/browse/folder/{{ $procNdFold->id }}">{{ $procNdFold->name }}</a>
                              <?php else: ?>
                                <?php 
                                  $request_found = DB::table('approval_requests')->where('procedure_id', $procNdFold->id)->first();
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
                                      <a class="item-name" href="/{{ $procNdFold->type }}/{{ $procNdFold->id }}/{{ $link }}">{{ $procNdFold->name }}</a>
                                  <?php endif ?>

                                <?php else: ?>
                                  
                                  <?php if ($procNdFold->status == NULL): ?>
                                    <?php if ($procNdFold->owner == Auth::user()->id): ?>
                                      <a class="item-name" href="/{{ $procNdFold->type }}/{{ $procNdFold->id }}/edit">{{ $procNdFold->name }}</a>
                                    <?php else: ?>
                                      <a class="item-name" href="/{{ $procNdFold->type }}/{{ $procNdFold->id }}/edit">{{ $procNdFold->name }}</a>
                                    <?php endif ?>
                                  <?php else: ?>
                                    <?php if ($procNdFold->edit != NULL): ?>
                                      <?php if ($procNdFold->owner == Auth::user()->id): ?>
                                        <a class="item-name" href="/{{ $procNdFold->type }}/{{ $procNdFold->id }}/edit">{{ $procNdFold->name }}</a>
                                      <?php else: ?>
                                        <a class="item-name" href="/{{ $procNdFold->type }}/{{ $procNdFold->id }}">{{ $procNdFold->name }}</a>
                                      <?php endif ?>
                                    <?php else: ?>
                                      <a class="item-name" href="/{{ $procNdFold->type }}/{{ $procNdFold->id }}">{{ $procNdFold->name }}</a>
                                    <?php endif ?>
                                  <?php endif ?>
                                <?php endif ?>
                              <?php endif ?>
                             </td>
                             <td>
                              <?php if ($procNdFold->type == 'procedure' && $procNdFold->status == NULL): ?>
                                <span class="label label-warning">Draft </span>
                              <?php endif ?>
                             </td>
                             <td>
                              <?php $owner = User::find($procNdFold->owner); ?>
                              {{ $owner->name }}
                             </td>
                             <td>
                                <?php if ($procNdFold->updated_at == NULL): ?>
                                  {{ date('D, M d, Y g:i A', strtotime($procNdFold->created_at)) }}
                                <?php else: ?>
                                  {{ date('D, M d, Y g:i A', strtotime($procNdFold->updated_at)) }}
                                <?php endif ?>
                             </td>
                          </tr>
                        <?php endforeach ?>
                      <?php endif ?>

                      <!-- Then the procedure drafts -->
                   </tbody>
                </table>
             </div>
          </div>
          <!-- Rename Modal -->
          <div class="modal fade" id="rename-folder-modal" tabindex="-1" role="dialog" aria-labelledby="rename-folder-modal-label" aria-hidden="true">
             <div class="modal-dialog">
                <div class="modal-content">
                   <form method="POST" action="/proc_fold_rename" accept-charset="UTF-8">
                      @csrf
                      <input type="hidden" name="type_id" value="{{ $dataTypeContent->id }}" />
                      <div class="modal-header">
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                         <h4 class="modal-title" id="rename-department-modal-label-5">Rename Folder</h4>
                      </div>
                      <div class="modal-body">
                         <div class="form-group">
                            <label for="name">Folder name</label>
                            <input class="form-control" name="name" type="text" value="{{ $dataTypeContent->name }}" id="name" required>
                         </div>
                      </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                         <button type="submit" class="btn btn-primary">Rename Folder</button>
                      </div>
                   </form>
                </div>
             </div>
          </div>
          <!-- Rename Modal -->

          <div class="modal fade" id="create-procedure-draft">
             <div class="modal-dialog">
                <div class="modal-content">
                   <form method="POST" action="/procedureDrafts" accept-charset="UTF-8">
                      @csrf
                      <div class="modal-header">
                         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                         <h4 class="modal-title">Create a Procedure Placeholder</h4>
                      </div>
                      <div class="modal-body">
                         <div class="form-group">
                            <label for="name">Procedure Placeholder Name</label>
                            <input class="form-control" name="name" type="text" id="name">
                         </div>
                         <div class="form-group">
                            <label for="description">Procedure Goal</label>
                            <textarea class="form-control" name="description" cols="50" rows="10" id="description"></textarea>
                         </div>
                         <div class="form-group">
                            <label for="user_assigned_id">User in charge of creating procedure:</label>
                            <select class="form-control" id="user_assigned_id" name="user_assigned_id">
                               <option value="1" selected="selected"> Me (daniel landa)</option>
                            </select>
                         </div>
                         <div class="form-group">
                            <label for="deadline">Deadline:</label>
                            <select class="form-control" id="deadline" name="deadline">
                               <option value="none">None</option>
                               <option value="today">Today</option>
                               <option value="this_week">End of this week</option>
                               <option value="next_week">End of next week</option>
                               <option value="this_month">End of this month</option>
                            </select>
                         </div>
                         <input type="hidden" name="department_id" value="5">
                      </div>
                      <div class="modal-footer">
                         <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                         <button type="submit" class="btn btn-primary">Create Procedure Draft</button>
                      </div>
                   </form>
                </div>
                <!-- /.modal-content -->
             </div>
             <!-- /.modal-dialog -->
          </div>
          <!-- /.modal -->                
       </div>
    </div>
@stop

@section('javascript')
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

        $('.moveButton').on('click',  function(e) {
            e.preventDefault();
            var $this = $(this);
            var model = $this.attr('data-target');
            var type = $this.attr('data-type');
            var id = $this.attr('data-id');

            var typeCapitalized = type.charAt(0).toUpperCase() + type.slice(1);

            $(model+' h4.modal-title').text('Move '+typeCapitalized);
            $(model+' button.moveButton').text('Move '+typeCapitalized);

            $(model).modal('show');

        });

        var breadcrumbs = '';

        $($(".breadcrumb_li").get().reverse()).each(function(index, el) {
          breadcrumbs += '<li class="active breadcrumb_li">'+$(el).html()+'</li>';
        });

        $('.breadcrumb_li').remove();
        $(breadcrumbs).insertAfter('li.department_li_breadcrumb');


        $("#quick-filter").on("keyup", function() {
           var $this = $(this);
           var keywords = $this.val().toLowerCase();
           if (keywords.trim().length == 0) {
              $this.val('');
           }
           $(".procedure_department_listing").filter(function() {
              $(this).toggle($(this).find('.item-name').text().toLowerCase().indexOf(keywords) > -1)
           });
        });

    </script>
@stop
