<?php

namespace App\Http\Controllers\Custom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Console\Scheduling\Schedule;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Str;
use Session;
use Carbon\Carbon;
use Auth;
use Dwij\Laraadmin\Models\Module;
use Dwij\Laraadmin\Models\ModuleFields;
use Validator;
use App\User;
use App\Department;
use App\Notification;
use App\ProcedureData;
use App\FolderProcedure;
use Mail;
use Log;
use URL;
use File;
use Lang;

class CustomController extends Controller
{

	/**
     * Create a new controller instance.
     *
     * @return void
     */

	public function __construct()
    {
        //
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

     public function reset_password_send($id)
     {
     	$user = User::find($id);
     	if ($user) {

     		$updateuser = User::find($id);
     		$password = uniqid();

     		$updateuser->password = bcrypt($password);

     		$updateuser->save();

			$msg_template = '
				<div style="text-align: left;padding-left: 20px;padding-top: 50px;padding-bottom: 30px;">
					<h3>Hello '.$user->name.'<br></h3>
					<p>your password has been reset, you can login with: <br>
						Email: '.$user->email.' <br>
						Password: '.$password.'
					</p>
					<br><br>
					<a href="'.URL::to('/login').'">Click here to login with your account</a>
				</div>
			';
			$to = $user->email;
		    $subject = 'Your BDS Password has been reset';
		    $content = $msg_template;

			$data = array( 'email' => $to, 'subject' => $subject, 'message' => $content);
			//Mail::send([], $data, function ($m) use($data) {
	           // $m->to($data['email'])->subject($data['subject'])->setBody($data['message'], 'text/html');
	    	//});

			return redirect()->back()->with('message', 'User password has been reset and sent over email');
     	} else {
			return redirect()->back()->with('error', 'Oops! Something went wrong');
     	}
     }

    // add user to department
    public function add_user_to_department(Request $request)
    {
     	$user_id = $request->input('user_id');
     	$department_id = $request->input('department_id');
     	$role = $request->input('role');

     	if ($department_id == NULL) {
			return redirect()->back()->with('error', 'Please select a department to add this user to.');	
     	}
     	if ($user_id == NULL) {
			return redirect()->back()->with('error', 'Please select a user to add to this department.');	
     	}

     	$add_in_depart = [
     		'department_id' => $department_id,
     		'user_id' => $user_id,
     		'role' => $role,
     	];

        $userInsertInDepart = DB::table('user_department')->insert($add_in_depart);
        if ($userInsertInDepart) {

	     	//get department and user name
	     	$department = Department::find($department_id);
	     	$user = User::find($user_id);
        	
        	//success message 
        	$message = $user->name.' succesfully added to department '.$department->name.' as '.ucfirst($role);

			return redirect()->back()->with('message', $message);
        } else {
			return redirect()->back()->with('error', 'Oops! Something went wrong');	
        }
    }

    // delete user to department
    public function delete_user_to_department($department_id, $user_id)
    {
        $userDeletedInDepart = DB::table('user_department')->where([['department_id', $department_id], ['user_id', $user_id]])->delete();
        
        if ($userDeletedInDepart) {

	     	//get department and user name
	     	$department = Department::find($department_id);
	     	$user = User::find($user_id);
        	
        	//success message 
        	$message = $user->name.' has been succesfully removed from the '.$department->name.' department.';

			return redirect()->back()->with('message', $message);
        } else {
			return redirect()->back()->with('error', 'Oops! Something went wrong');	
        }
    }

    // Rename Department
    public function rename_department(Request $request)
    {
     	$department_id = $request->input('department_id');
        $oldData = Department::where('id', $department_id)->first();
        $name = $request->input('name');

     	$updateName = [
     		'name' => $name
     	];

     	$updated = Department::where('id', $department_id)->update($updateName);
     	if ($updated) {

            $getDepartmentUsers = DB::table('user_department')->where('department_id', $department_id)->get();
            if ($getDepartmentUsers) {
                $title = \Lang::get('messages.department.rename', ['user' => Auth::user()->name, 'oldDepartmentName' => $oldData->name, 'newDepartmentName' => $name]);
                $body = \Lang::get('messages.department.rename', ['user' => Auth::user()->name, 'oldDepartmentName' => '<a href="/departments/'.$department_id.'">'.$oldData->name.'</a>', 'newDepartmentName' => '<a href="/departments/'.$department_id.'">'.$name.'</a>']);
                $type = 'department';
                foreach ($getDepartmentUsers as $key => $getDepartmentUser) {
                    $this->addNotification($title, $body, $type, $department_id, NULL, Auth::user()->id, $getDepartmentUser->user_id);
                }
            }

			return redirect()->back()->with('message', 'Department name succesfully updated');
     	} else {
			return redirect()->back()->with('error', 'Oops! Something went wrong');
     	}
    }

    // Add folder in department
    public function add_folder_procedure(Request $request)
    {
     	$department_id = $request->input('department_id');
     	$name = $request->input('name');
     	$owner = Auth::user()->id;
     	$type = $request->input('type');
     	$parent_id = $request->input('parent_id');
     	$description = $request->input('description');

     	$addProcFold = [
     		'department_id' => $department_id,
     		'name' => $name,
     		'owner' => $owner,
     		'type' => $type,
            'parent_id' => $parent_id,
     		'edit' => '1',
     		'description' => $description,
		    'created_at' => Carbon::now()
     	];

        $addNew = DB::table('folder_and_procedure')->insertGetId($addProcFold);

    	if (!empty($addNew)) {

    		if ($type == 'folder') {
    			$message = 'Folder added succesfully';
				return redirect()->back()->with('message', $message);
    		} else {
				return redirect('/procedure/'.$addNew.'/edit');
    		}

    	} else {
			return redirect()->back()->with('error', 'Oops! Something went wrong');	
    	}
    }

    // Duplicate Procedure
    public function duplicate_procedure($id)
    {

        $procedure = FolderProcedure::find($id);
        $cloneProcedure = $procedure->replicate();
        $cloneProcedure->name = 'Copy of '.$cloneProcedure->name;
        $cloneProcedure->status = NULL;
        $cloneProcedure->owner = Auth::user()->id;
        $cloneProcedure->save();
        $newID = $cloneProcedure->id;

        $folder_and_procedure = DB::table('procedure_data')->where('folder_and_procedure_id', $id)->get();
        if ($folder_and_procedure) {
            foreach ($folder_and_procedure as $key => $procedureDataSingle) {

                $procedureData = ProcedureData::find($procedureDataSingle->id);
                
                $cloneProcedureData = $procedureData->replicate();
                $cloneProcedureData->folder_and_procedure_id = $newID;
                $cloneProcedureData->save();
                $newDataID = $cloneProcedureData->id;
            }
        }
        return redirect()->back(); 
    }

    // Rename Procedure/folder
    public function proc_fold_rename(Request $request)
    {
     	$id = $request->input('type_id');
        $name = $request->input('name');
        $description = $request->input('description');
        $oldData = DB::table('folder_and_procedure')->where('id', $id)->first();

     	$updateName = [
     		'name' => $name,
     		'description' => $description
     	];

    	$updated_name = DB::table('folder_and_procedure')->where('id', $id)->update($updateName);
    	if ($updated_name) {
            $depart = Department::find($oldData->department_id);

            $getDepartmentUsers = DB::table('user_department')->where('department_id', $oldData->department_id)->get();
            if ($getDepartmentUsers) {
                $title = \Lang::get('messages.procedure.rename', ['user' => Auth::user()->name, 'oldProcedureName' => $oldData->name, 'newProcedureName' => $name, 'departmentName' => $depart->name]);
                $body = \Lang::get('messages.procedure.rename', ['user' => Auth::user()->name, 'oldProcedureName' => '<a href="/procedure/'.$id.'">'.$oldData->name.'</a>', 'newProcedureName' => '<a href="/procedure/'.$id.'">'.$name.'</a>', 'departmentName' => '<a href="/departments/'.$oldData->department_id.'">'.$depart->name.'</a>']);
                $type = 'department';
                foreach ($getDepartmentUsers as $key => $getDepartmentUser) {
                    $this->addNotification($title, $body, $type, $oldData->department_id, $id, Auth::user()->id, $getDepartmentUser->user_id);
                }

            }
            return redirect()->back();
        } else {
			return redirect()->back()->with('error', 'Oops! Something went wrong');	
    	}
    }

	// Toggle Favourite folder/procedure
    public function toggle_favourite($type, $id)
    {
    	$user_id = Auth::user()->id;
    	$foundFavourite = DB::table('favourites')->where([['fold_proc_id', $id], ['user_id', $user_id]])->first();
    	if ($foundFavourite) {
    		if ($foundFavourite->status == '0') {
		    	$updateFavourite = DB::table('favourites')->where([['fold_proc_id', $id], ['user_id', $user_id]])->update(['status' => '1']);	
    		} else {
		    	$updateFavourite = DB::table('favourites')->where([['fold_proc_id', $id], ['user_id', $user_id]])->update(['status' => '0']);	
    		}
    		
    	} else {
	    	$insertFavourite = DB::table('favourites')->insert(['fold_proc_id' => $id, 'user_id' => $user_id, 'status' => '1']);
    	}

		return redirect()->back();
    }

    // Delete Procedure/Folder
    public function destroy_folder_procedure($type, $id)
    {
    	$updateDelete = [
    		'delete' => '1'
    	];
    	$updated_delete = DB::table('folder_and_procedure')->where('id', $id)->update($updateDelete);
    	if ($updated_delete) {
			return redirect()->back()->with('message', ucfirst($type).' has been succesfully deleted. <a class="btn btn-dark pull-right" style="position:relative; bottom:11px;" href="/'.$type.'/'.$id.'/undestroy">Undo Delete</a>');
    	} else {
			return redirect()->back()->with('error', 'Oops! Something went wrong');	
    	}

    }

    // Undo Delete
    public function undestroy_folder_procedure($type, $id)
    {
    	$updateDelete = [
    		'delete' => NULL
    	];

    	$updated_delete = DB::table('folder_and_procedure')->where('id', $id)->update($updateDelete);
    	
    	if ($updated_delete) {
			return redirect()->back()->with('message', ucfirst($type).' has been succesfully restored.');
    	} else {
			return redirect()->back()->with('error', 'Oops! Something went wrong');	
    	}
    }

    // View Folder inner Page
    public function folder_view($id)
    {
        $user = Auth::user();

    	$dataTypeContent = DB::table('folder_and_procedure')->where([['id', $id]])->first();
    	if ($dataTypeContent) {

            $userAccess =  DB::table('user_department')->where([['user_id', $user->id],['department_id', $dataTypeContent->department_id]])->first();

            if (!$userAccess && $user->role_id != '1') {
                return redirect('/');
            }

    		if ($dataTypeContent->delete != NULL) {
				return redirect('/departments/'.$dataTypeContent->department_id);
    		} else {
		    	return Voyager::view('vendor.voyager.departments.folder')->with(compact('dataTypeContent'));
    		}
    	} else {
			return redirect('/');
    	}
    }

	// Edit Procedure Page
    public function procedure_edit($id)
    {
        $user = Auth::user();
    	$dataTypeContent = DB::table('folder_and_procedure')->where('id', $id)->first();

    	if ($dataTypeContent) {
    		if ($dataTypeContent->delete != NULL) {
				return redirect('/departments/'.$dataTypeContent->department_id);
    		} else {
                $owner = User::find($dataTypeContent->owner);

                if ($dataTypeContent->owner != $user->id) {

                    $userAccess =  DB::table('user_department')->where([['user_id', $user->id],['department_id', $dataTypeContent->department_id]])->first();

                    if (!$userAccess && $user->role_id != '1') {
                        return redirect('/');
                    } else if(count($userAccess) > 0) {
                        return redirect('procedure/'.$id)->with('error', 'You are not the owner of the procedure, please make a request for ownership, or become an admin.');
                        // if ($userAccess->role == 'contributor') {
                        // } else {
                        //     return redirect('procedure/'.$id)->with('error', 'You are not the owner of the procedure, please make a request for ownership, or become an admin.');
                        // }
                    }

                } else {
                    $depart = Department::find($dataTypeContent->department_id);
                    $edit = '2';
                    if ($dataTypeContent->edit == '2') {
                        $edit = '3';
                    }
                    $updateEdit = [
                        'edit' => $edit
                    ];

                    DB::table('folder_and_procedure')->where('id', $id)->update($updateEdit);

    		    	$procedureContent = DB::table('procedure_data')->where([['folder_and_procedure_id', $dataTypeContent->id], ['status', 'active']])->orderBy('order', 'ASC')->get();

                    $submitRequestFound = DB::table('approval_requests')->where([['procedure_id', $dataTypeContent->id],['user_id', Auth::user()->id]])->first();
                    if ($submitRequestFound) {
                        if ($submitRequestFound->status != 'reject') {
                            $type = 'procedure';

                            $users = explode(',', $submitRequestFound->users);

                            $title = \Lang::get('messages.procedure.cancelRequest', ['user' => Auth::user()->name, 'status' => $submitRequestFound->status, 'procedureName' => $dataTypeContent->name, 'departmentName' => $depart->name]);
                            $body = \Lang::get('messages.procedure.cancelRequest', ['user' => Auth::user()->name, 'status' => $submitRequestFound->status, 'procedureName' => '<a href="/procedure/'.$dataTypeContent->id.'">'.$dataTypeContent->name.'</a>', 'departmentName' => '<a href="/departments/'.$dataTypeContent->department_id.'">'.$depart->name.'</a>']);
                            
                            if ($users) {
                                foreach ($users as $key => $user) {
                                    $publisher = User::find($user);
                                    $this->addNotification($title, $body, $type, NULL, $dataTypeContent->id, Auth::user()->id, $publisher->id);
                                }
                            }
                            DB::table('approval_requests')->where('id', $submitRequestFound->id)->delete();
                        }
                    }

    		    	return Voyager::view('vendor.voyager.departments.procedure')->with(compact('dataTypeContent', 'procedureContent'));

                }

    		}
    	} else {
			return redirect('/');
    	}
    }

	// dropzone Image upload
    public function imageupload(Request $request)
    {
        $getProcedureContent = DB::table('procedure_data')->where('id', $request->data_id)->first();
        if ($getProcedureContent->additional_data != NULL) {
            $jArr = json_decode($getProcedureContent->additional_data);
        }

		$image = $request->file('file');
        $filename = $image->getClientOriginalName();
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
        $uniqName = uniqid(time());
        $image->move(public_path('procedure_images'),$uniqName.'.'.$extension);
        return response()->json(['success'=>$uniqName.'.'.$extension, 'original_name' => $filename]);
    }

	// dropzone File upload
    public function attachupload(Request $request)
    {
		$image = $request->file('file');
        $filename = $image->getClientOriginalName();
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
        $uniqName = uniqid(time());
        $image->move(public_path('procedure_attachments'),$uniqName.'.'.$extension);
        return response()->json(['success'=>$uniqName.'.'.$extension, 'original_name' => $filename]);
    }

    // Download Attachment
    public function attachdownload($id)
    {
		$file = $id;
    	// $getData = DB::table('procedure_data')->where('id', $id)->first();
    	// if ($getData) {
    		$file = public_path(). "/procedure_attachments/".$file;
	        $name = basename($file);
	        return response()->download($file, $name);
    	// } else {
    	// 	return redirect()->back();
    	// }
    }

    // dropzone video upload
    public function attachvideo(Request $request)
    {
		$image = $request->file('file');
        $filename = $image->getClientOriginalName();
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
        $uniqName = uniqid(time());
        $image->move(public_path('procedure_videos'),$uniqName.'.'.$extension);
        return response()->json(['success'=>$uniqName.'.'.$extension, 'original_name' => $filename]);
    }


    // Add new procedure data
    public function addNewProcedureData(Request $request)
    {
        $procedure_id = $request->input('procedure_id');
        $type = $request->input('type');
        $order = $request->input('order');


        if ($type == 'BlockText') {
            $content = NULL;
        } else if($type == 'BlockImage') {
          $content = '<h3 align="center"> No image uploaded </h3>';
        } else if($type == 'BlockAttachment') {
          $content = '<h3 align="center"> No file uploaded </h3>';
        } else if($type == 'BlockProcedure') {
          $content = '<h3 align="center"> No procedure selected for embedding. </h3>';
        } else if($type == 'BlockVideo') {
          $content = '<h3 align="center"> No video uploaded </h3>';
        }

        $insertProcedureData = [
            'folder_and_procedure_id' => $procedure_id,
            'type' => $type,
            'status' => 'active',
            'expand' => 'in',
            'content' => $content,
            'order' => $order,
            'created_at' => Carbon::now()
        ];

        $updated_delete = DB::table('procedure_data')->insertGetId($insertProcedureData);
        return $updated_delete;

    }

    // Update procedure data
    public function updateProcedureData(Request $request)
    {
        $id = $request->input('id');
        $getProcedureData = DB::table('procedure_data')->where('id', $id)->first();
        $order = $request->input('order', $getProcedureData->order);
        $content = $request->input('content', $getProcedureData->content);
        $status = $request->input('status', $getProcedureData->status);
        $attach = $request->input('attach', $getProcedureData->attach);
        $parent_id = $request->input('parent_id', $getProcedureData->parent_id);
        $step = $request->input('step', $getProcedureData->step);
        $additional_data = $request->input('additional_data', $getProcedureData->additional_data);

        $updateProcedureData = [
            'order' => $order,
            'content' => $content,
            'status' => $status,
            'attach' => $attach,
            'user_id' => Auth::user()->id,
            'parent_id' => $parent_id,
            'step' => $step,
            'additional_data' => $additional_data
        ];

        $updated = DB::table('procedure_data')->where('id', $id)->update($updateProcedureData);
        return $updated;
    }

    // Mark/Unmark Step
    public function markStep(Request $request)
    {
        $id = $request->input('id');
        $step = $request->input('step');
        if ($step != '1') {
            $step = '1';
        } else {
            $step = NULL;
        }

        $markStep = [
            'step' => $step
        ];

        DB::table('procedure_data')->where('id', $id)->update($markStep);
        return $step;
    }

    // Clone Block
    public function cloneBlock(Request $request)
    {
        $id = $request->input('id');
        $procedureData = ProcedureData::find($id);
        
        $cloneBlock = $procedureData->replicate();
        $cloneBlock->save();
        $newID = $cloneBlock->id;
        return $newID;

    }

    // Change Status
    public function changeStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');

        $status = [
            'status' => $status
        ];
        $done = DB::table('procedure_data')->where('id', $id)->update($status);
        return $done;

    }

    // permenent Delete
    public function permenentDelete(Request $request)
    {
        $id = $request->input('id');
        $done = DB::table('procedure_data')->where('id', $id)->delete();

        return $done;

    }

    // Expand/Collapse
    public function expand(Request $request)
    {
        $id = $request->input('id');
        $getProcedureData = DB::table('procedure_data')->where('id', $id)->first();
        if ($getProcedureData->expand == 'in') {
            $expand = ['expand' => NULL];
        } else {
            $expand = ['expand' => 'in'];
        }
        $done = DB::table('procedure_data')->where('id', $id)->update($expand);

        return $done;
    }


    // Procedure Preview Page
    public function procedure_preview($id)
    {
        $user = Auth::user();
        $dataTypeContent = DB::table('folder_and_procedure')->where('id', $id)->first();

        if ($dataTypeContent) {
            if ($dataTypeContent->delete != NULL) {
                return redirect('/departments/'.$dataTypeContent->department_id);
            } else {

                $userAccess =  DB::table('user_department')->where([['user_id', $user->id],['department_id', $dataTypeContent->department_id]])->first();

                if (!$userAccess && $user->role_id != '1') {
                    return redirect('/');
                }

                $procedureContent = DB::table('procedure_data')->where([['folder_and_procedure_id', $dataTypeContent->id], ['status', 'active']])->orderBy('order', 'ASC')->get();
                return Voyager::view('vendor.voyager.departments.procedure_preview')->with(compact('dataTypeContent', 'procedureContent'));
            }
        } else {
            return redirect('/');
        }
    }

    // Procedure Publish directly 
    public function publish_directly($id)
    {
        $oldData = DB::table('folder_and_procedure')->where('id', $id)->first();
        $depart = Department::find($oldData->department_id);
        $owner = User::find($oldData->owner);
        
        $update = [
            'status' => '1',
            'edit' => '4'
        ];

        $type = 'department';
        if ($oldData->owner == Auth::user()->id && $oldData->edit == '1') {
            $title = \Lang::get('messages.procedure.ownerCreatePublish', ['user' => Auth::user()->name, 'procedureName' => $oldData->name, 'departmentName' => $depart->name]);
            $body = \Lang::get('messages.procedure.ownerCreatePublish', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$id.'">'.$oldData->name.'</a>', 'departmentName' => '<a href="/departments/'.$oldData->department_id.'">'.$depart->name.'</a>']);
        } else if ($oldData->owner != Auth::user()->id && $oldData->edit == '1') {
            $title = \Lang::get('messages.procedure.adminPublishProcedure', ['user' => $owner->name, 'procedureName' => $oldData->name, 'departmentName' => $depart->name, 'approvedBy' => Auth::user()->name]);
            $body = \Lang::get('messages.procedure.adminPublishProcedure', ['user' => $owner->name, 'procedureName' => '<a href="/procedure/'.$id.'">'.$oldData->name.'</a>', 'departmentName' => '<a href="/departments/'.$oldData->department_id.'">'.$depart->name.'</a>', 'approvedBy' => Auth::user()->name]);
        } else {
            $title = \Lang::get('messages.procedure.publishChanges', ['user' => Auth::user()->name, 'procedureName' => $oldData->name, 'departmentName' => $depart->name]);
            $body = \Lang::get('messages.procedure.publishChanges', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$id.'">'.$oldData->name.'</a>', 'departmentName' => '<a href="/departments/'.$oldData->department_id.'">'.$depart->name.'</a>']);
        }

        $getDepartmentUsers = DB::table('user_department')->where('department_id', $oldData->department_id)->get();
        if ($getDepartmentUsers) {
            foreach ($getDepartmentUsers as $key => $getDepartmentUser) {
                $this->addNotification($title, $body, $type, $oldData->department_id, $id, Auth::user()->id, $getDepartmentUser->user_id);
            }
        }

        $updatePublish = DB::table('folder_and_procedure')->where('id', $id)->update($update);

        $approval_requests = DB::table('approval_requests')->where([['procedure_id', $id], ['user_id', $oldData->owner]])->first();

        if ($approval_requests) {
            DB::table('approval_requests')->where([['id', $approval_requests->id]])->delete();
        }

        return redirect('/procedure/'.$id)->with('message', 'Procedure successfully published.');
    }

    // Procedure Reject 
    public function reject($id)
    {
        $oldData = DB::table('folder_and_procedure')->where('id', $id)->first();
        $depart = Department::find($oldData->department_id);
        $owner = User::find($oldData->owner);
        
        $update = [
            'status' => 'reject',
            'edit' => '2'
        ];

        $type = 'department';

        $updateReject = DB::table('folder_and_procedure')->where('id', $id)->update($update);

        $approval_requests = DB::table('approval_requests')->where([['procedure_id', $id], ['user_id', $oldData->owner]])->first();

        if ($approval_requests) {
            $reject = ['status' => 'reject'];
            DB::table('approval_requests')->where([['id', $approval_requests->id]])->update($reject);

            $title = \Lang::get('messages.procedure.reject', ['user' => Auth::user()->name, 'procedureName' => $oldData->name, 'departmentName' => $depart->name]);
            $body = \Lang::get('messages.procedure.reject', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$id.'">'.$oldData->name.'</a>', 'departmentName' => '<a href="/departments/'.$oldData->department_id.'">'.$depart->name.'</a>']);

            $this->addNotification($title, $body, $type, $oldData->department_id, NULL, Auth::user()->id, $oldData->owner);
        }

        return redirect('/procedure/'.$id)->with('error', 'Procedure rejected.');
    }




    // Procedure View Page
    public function procedure_view($id)
    {
        $user = Auth::user();
        $dataTypeContent = DB::table('folder_and_procedure')->where('id', $id)->first();

        if ($dataTypeContent) {
            if ($dataTypeContent->delete != NULL) {
                return redirect('/departments/'.$dataTypeContent->department_id);
            } else {

                $userAccess =  DB::table('user_department')->where([['user_id', $user->id],['department_id', $dataTypeContent->department_id]])->first();

                if (!$userAccess && $user->role_id != '1') {
                    return redirect('/');
                }

                $procedureContent = DB::table('procedure_data')->where([['folder_and_procedure_id', $dataTypeContent->id], ['status', 'active']])->orderBy('order', 'ASC')->get();
                return Voyager::view('vendor.voyager.departments.procedure_view')->with(compact('dataTypeContent', 'procedureContent'));
            }
        } else {
            return redirect('/');
        }
    }

    // Procedure View Page
    public function review_approval($id)
    {
        $user = Auth::user();
        $dataTypeContent = DB::table('folder_and_procedure')->where('id', $id)->first();

        if ($dataTypeContent) {
            if ($dataTypeContent->delete != NULL) {
                return redirect('/departments/'.$dataTypeContent->department_id);
            } else {

                $userAccess =  DB::table('user_department')->where([['user_id', $user->id],['department_id', $dataTypeContent->department_id]])->first();

                if (!$userAccess && $user->role_id != '1') {
                    return redirect('/');
                }

                $procedureContent = DB::table('procedure_data')->where([['folder_and_procedure_id', $dataTypeContent->id], ['status', 'active']])->orderBy('order', 'ASC')->get();

                $approval_requests = DB::table('approval_requests')->where([['procedure_id', $dataTypeContent->id], ['status', 'approval']])->first();

                if ($approval_requests) {
                    return Voyager::view('vendor.voyager.departments.review_approval')->with(compact('dataTypeContent', 'procedureContent', 'approval_requests'));
                } else {
                    return redirect('/procedure/'.$id)->with('error', 'This procedure is currently not under an approval process, please comment or suggest a tweak.');
                }

            }
        } else {
            return redirect('/');
        }
    }


    // Procedure View Page
    public function request_review($id)
    {
        $user = Auth::user();
        $dataTypeContent = DB::table('folder_and_procedure')->where('id', $id)->first();

        if ($dataTypeContent) {
            if ($dataTypeContent->delete != NULL) {
                return redirect('/departments/'.$dataTypeContent->department_id);
            } else {

                $userAccess =  DB::table('user_department')->where([['user_id', $user->id],['department_id', $dataTypeContent->department_id]])->first();

                if (!$userAccess && $user->role_id != '1') {
                    return redirect('/');
                }

                $procedureContent = DB::table('procedure_data')->where([['folder_and_procedure_id', $dataTypeContent->id], ['status', 'active']])->orderBy('order', 'ASC')->get();

                $approval_requests = DB::table('approval_requests')->where([['procedure_id', $dataTypeContent->id], ['status', 'review']])->first();

                if ($approval_requests) {
                    return Voyager::view('vendor.voyager.departments.review_request')->with(compact('dataTypeContent', 'procedureContent', 'approval_requests'));
                } else {
                    return redirect('/procedure/'.$id)->with('error', 'This procedure is currently not under an approval process, please comment or suggest a tweak.');
                }

            }
        } else {
            return redirect('/');
        }
    }

    // Add comment
    public function add_comment(Request $request)
    {
        $procedure_data_id = $request->input('procedure_data_id');
        $comment = $request->input('comment');

        $insertComment = [
            'procedure_data_id' => $procedure_data_id,
            'user_id' => Auth::user()->id,
            'comment' => $comment,
            'created_at' => Carbon::now()
        ];

        $insertedComment = DB::table('procedure_data_comment')->insertGetId($insertComment);

        $procedure_data = DB::table('procedure_data')->where('id', $procedure_data_id)->first();
        if ($procedure_data) {
            $oldData = DB::table('folder_and_procedure')->where('id', $procedure_data->folder_and_procedure_id)->first();
            if ($oldData) {
                $type = 'procedure';

                $title = \Lang::get('messages.procedure.comment', ['user' => Auth::user()->name, 'procedureName' => $oldData->name]);
                $body = \Lang::get('messages.procedure.comment', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$procedure_data->folder_and_procedure_id.'">'.$oldData->name.'</a>']);

                $getDepartmentUsers = DB::table('user_department')->where('department_id', $oldData->department_id)->get();
                if ($getDepartmentUsers) {
                    foreach ($getDepartmentUsers as $key => $getDepartmentUser) {
                        $this->addNotification($title, $body, $type, NULL, $procedure_data->folder_and_procedure_id, Auth::user()->id, $getDepartmentUser->user_id);
                    }
                }

            }
        }

        $date = date('F d, Y g:i');

        return response()->json(['comment_id' => $insertedComment, 'user_name'=> Auth::user()->name, 'date' => $date]);

    }

    // delete comment
    public function comment_destroy(Request $request)
    {
        $comment_id = $request->input('comment_id');
        $delete = DB::table('procedure_data_comment')->where('id', $comment_id)->delete();
        return 1;
    }

    // Take Ownership
    public function take_ownership($id)
    {
        $oldData = DB::table('folder_and_procedure')->where('id', $id)->first();
        $depart = Department::find($oldData->department_id);
        $edit = '2';
        if ($oldData->edit == '2') {
            $edit = '3';
        }
        $updateOwner = [
            'owner' => Auth::user()->id,
            'edit' => $edit
        ];

        DB::table('folder_and_procedure')->where('id', $id)->update($updateOwner);

        $type = 'procedure';

        $title = \Lang::get('messages.procedure.takeOverProcedure', ['user' => Auth::user()->name, 'procedureName' => $oldData->name, 'departmentName' => $depart->name]);
        $body = \Lang::get('messages.procedure.takeOverProcedure', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$id.'">'.$oldData->name.'</a>', 'departmentName' => '<a href="/departments/'.$oldData->department_id.'">'.$depart->name.'</a>']);

        $this->addNotification($title, $body, $type, $oldData->department_id, $id, Auth::user()->id, $oldData->owner);

        $type = 'department';
        $title = \Lang::get('messages.procedure.takeOwnership', ['user' => Auth::user()->name, 'procedureName' => $oldData->name, 'departmentName' => $depart->name]);
        $body = \Lang::get('messages.procedure.takeOwnership', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$id.'">'.$oldData->name.'</a>', 'departmentName' => '<a href="/departments/'.$oldData->department_id.'">'.$depart->name.'</a>']);

        $getDepartmentUsers = DB::table('user_department')->where('department_id', $oldData->department_id)->get();
        if ($getDepartmentUsers) {
            foreach ($getDepartmentUsers as $key => $getDepartmentUser) {
                $this->addNotification($title, $body, $type, $oldData->department_id, $id, Auth::user()->id, $getDepartmentUser->user_id);
            }
        }

        return redirect('/procedure/'.$id.'/edit')->with('message', 'You have succesfully taken ownership of this procedure.');

    }

    // Propose New Add/Edit
    public function propose_new(Request $request)
    {
        $procedure_id = $request->input('procedure_id');
        $procedure_data_id = $request->input('procedure_data_id');
        $type = $request->input('type');
        $content = $request->input('content');
        $user_id = Auth::user()->id;
        $foundPropose = DB::table('procedure_data')->where([['parent_id', '=', $procedure_data_id], ['user_id', '=', $user_id], ['status', '!=', 'active']])->first();
        $oldData = DB::table('procedure_data')->where('id', $procedure_data_id)->first();

        if ($foundPropose) {

            $insertProcedureData = [
                'content' => $content,
                'status' => 'pending'
            ];

            $updated = DB::table('procedure_data')->where('id', $foundPropose->id)->update($insertProcedureData);
            if ($type == 'BlockProcedure') {
                return response()->json(['id' => $foundPropose->id, 'status'=> 'updated']);
            }
            return 'update';
            
        } else {

            $insertProcedureData = [
                'folder_and_procedure_id' => $procedure_id,
                'type' => $type,
                'order' => $oldData->order,
                'content' => $content,
                'status' => 'pending',
                'user_id' => $user_id,
                'parent_id' => $oldData->id,
                'step' => $oldData->step,
                'expand' => $oldData->expand,
                'created_at' => Carbon::now()
            ];

            $updated = DB::table('procedure_data')->insertGetId($insertProcedureData);

            $getProcedure = DB::table('folder_and_procedure')->where('id', $oldData->folder_and_procedure_id)->first();
            if ($getProcedure) {

                $title = \Lang::get('messages.propose.change', ['user' => Auth::user()->name, 'procedureName' => $getProcedure->name]);
                $body = \Lang::get('messages.propose.change', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$oldData->folder_and_procedure_id.'">'.$getProcedure->name.'</a>']);
                $notifType = 'procedure';

                $this->addNotification($title, $body, $notifType, NULL, $oldData->folder_and_procedure_id, Auth::user()->id, $oldData->user_id);
            }

            if ($type == 'BlockProcedure') {
                return response()->json(['id' => $updated, 'status'=> 'add']);
            }
            return 'add';
        }
        
    }

    // check previous propose
    public function checkPropose(Request $request)
    {
        $parent_id = $request->input('parent_id');
        $user_id = Auth::user()->id;
        $foundPropose = DB::table('procedure_data')->where([['parent_id', $parent_id], ['user_id', '=', $user_id], ['status', '!=', 'active']])->first();
        $oldData = DB::table('procedure_data')->where('id', $parent_id)->first();


        if ($foundPropose) {
            return response()->json(['procedureContent' => $foundPropose->content, 'id' => $foundPropose->id, 'propose' => '1']);
        } else {
            return response()->json(['procedureContent' => $oldData->content, 'id' => $oldData->id, 'propose' => '0']);
        }
    }

    // check All Proposes 
    public function checkAllProposes(Request $request)
    {
        $user_id = Auth::user()->id;
        $parent_id = $request->input('parent_id');
        $type = $request->input('type');
        $foundProposes = DB::table('procedure_data')
        ->join('users', 'users.id', '=', 'procedure_data.user_id')
        ->select('procedure_data.*', 'users.*', 'procedure_data.id AS procedure_data_id')
        ->where([['procedure_data.parent_id', '=', $parent_id], ['procedure_data.user_id', '!=', $user_id], ['procedure_data.type', '=', $type]])
        ->whereRaw("(procedure_data.status LIKE '%pending%' OR procedure_data.status LIKE '%accept%' OR procedure_data.status LIKE '%update%')")
        ->get();

        if ($foundProposes) {
            return $foundProposes;
        } else {
            return NULL;
        }
    }

    // dropzone Image upload on propose change
    public function imageuploadchange(Request $request)
    {
        $type = $request->type;
        if ($type == 'BlockImage') {
            $path = 'procedure_images';
        } else if($type == 'BlockAttachment') {
            $path = 'procedure_attachments';
        } else if($type == 'BlockVideo') {
            $path = 'procedure_videos';
        }
        $proposeData = DB::table('procedure_data')->where([['parent_id', '=', $request->parent_id], ['status', '!=', 'active'], ['type', $type]])->first();
        $oldData = DB::table('procedure_data')->where('id',$request->parent_id)->first();
        $user_id = Auth::user()->id;

        $image = $request->file('file');
        $filename = $image->getClientOriginalName();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $uniqName = uniqid(time());
        $image->move(public_path($path),$uniqName.'.'.$extension);

        if ($proposeData) {
            $proposeData = $proposeData->id;
            $status = 'update';

        } else {

            $insertProcedureData = [
                'folder_and_procedure_id' => $oldData->folder_and_procedure_id,
                'type' => $type,
                'order' => $oldData->order,
                'status' => NULL,
                'user_id' => $user_id,
                'parent_id' => $oldData->id,
                'step' => $oldData->step,
                'expand' => $oldData->expand,
                'created_at' => Carbon::now()
            ];

            $updated = DB::table('procedure_data')->insertGetId($insertProcedureData);

            $getProcedure = DB::table('folder_and_procedure')->where('id', $oldData->folder_and_procedure_id)->first();
            if ($getProcedure) {

                $title = \Lang::get('messages.propose.change', ['user' => Auth::user()->name, 'procedureName' => $getProcedure->name]);
                $body = \Lang::get('messages.propose.change', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$oldData->folder_and_procedure_id.'">'.$getProcedure->name.'</a>']);
                $notifType = 'procedure';

                $this->addNotification($title, $body, $notifType, NULL, $oldData->folder_and_procedure_id, Auth::user()->id, $oldData->user_id);
            }

            $proposeData = $updated;
            $status = 'add';
        }

        return response()->json(['success'=>$uniqName.'.'.$extension, 'original_name' => $filename, 'procedure_data_id' => $proposeData, 'status' => $status]);
    }

    // send propose change 
    public function proposedChange(Request $request)
    {
        $procedure_data_id = $request->input('procedure_data_id');
        $getData = DB::table('procedure_data')->where('id',$procedure_data_id)->first();
        if ($getData) {
            $newStatus = ($getData->status == NULL) ? 'pending' : 'update';
            if($getData->user_id == Auth::user()->id) {
                $insertProcedureData = [
                    'status' => $newStatus,
                ];

                $updated = DB::table('procedure_data')->where('id',$procedure_data_id)->update($insertProcedureData);

                $getParent = DB::table('folder_and_procedure')->where('id', $getData->folder_and_procedure_id)->first();
                if ($getParent) {

                    $title = \Lang::get('messages.propose.change', ['user' => Auth::user()->name, 'procedureName' => $getParent->name]);
                    $body = \Lang::get('messages.propose.change', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$getData->folder_and_procedure_id.'">'.$getParent->name.'</a>']);
                    $notifType = 'procedure';

                    $this->addNotification($title, $body, $notifType, NULL, $getData->folder_and_procedure_id, Auth::user()->id, $getData->user_id);
                }
            }

        }
    }



    // Add Notification
    public function addNotification($title, $body, $type, $department_id, $procedure_id, $user_id, $assigned_to)
    {
        $insert = [
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'department_id' => $department_id,
            'procedure_id' => $procedure_id,
            'user_id' => $user_id,
            'assigned_to' => $assigned_to,
            'status' => 'unread',
            'created_at' => Carbon::now()
        ];
        $updated = DB::table('notification')->insert($insert);
    }


    // view all notifications
    public function notification()
    {

        $notif = DB::table('notification')->where([['assigned_to', '=', Auth::user()->id], ['created_at', '>=', Auth::user()->created_at]]);

        $dataTypeContent = $notif->orderBy('id', 'DESC')->get();

        return Voyager::view('vendor.voyager.departments.notification')->with(compact('dataTypeContent'));
    }

    // notification mark as read
    public function readNotification()
    {
        $read = [
            'status' => 'read'
        ];
        $notif = DB::table('notification')->where('assigned_to', Auth::user()->id)->update($read);
        return $notif;
    }

    // Submit for approval
    public function submit_for_approval(Request $request)
    {
        $user_id = Auth::user()->id;
        $procedure_id = $request->input('procedure_id');
        $comment = $request->input('comment');
        $users = $request->input('users');
        $status = $request->input('status');

        //already exists
        $foundRequest = DB::table('approval_requests')->where([['user_id', $user_id], ['procedure_id', $procedure_id]])->first();

        if ($foundRequest) {
            return redirect()->back()->with('error', 'You have already sent '.$status.' request! you will be notify for approval or denial');
        }

        // new submit
        $approval = [
            'user_id' => $user_id,
            'procedure_id' => $procedure_id,
            'comment' => $comment,
            'users' => implode(',', $users),
            'status' => $status,
            'created_at' => Carbon::now()

        ];

        $procedure = FolderProcedure::find($procedure_id);
        $department = Department::find($procedure->department_id);

        $addApproval = DB::table('approval_requests')->insert($approval);

        $type = 'procedure';

        if ($status == 'approval') {
            $title = \Lang::get('messages.procedure.submitApproval', ['user' => Auth::user()->name, 'procedureName' => $procedure->name, 'departmentName' => $department->name]);
            $body = \Lang::get('messages.procedure.submitApproval', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$procedure_id.'/review_approval">'.$procedure->name.'</a>', 'departmentName' => '<a href="/departments/'.$procedure->department_id.'">'.$department->name.'</a>']);
        } else if($status == 'review') {
            $title = \Lang::get('messages.procedure.requestApproval', ['user' => Auth::user()->name, 'procedureName' => $procedure->name, 'departmentName' => $department->name]);
            $body = \Lang::get('messages.procedure.requestApproval', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$procedure_id.'/request_review">'.$procedure->name.'</a>', 'departmentName' => '<a href="/departments/'.$procedure->department_id.'">'.$department->name.'</a>']);
        }
        
        if ($users) {
            foreach ($users as $key => $user) {
                $publisher = User::find($user);
                $this->addNotification($title, $body, $type, NULL, $procedure_id, Auth::user()->id, $publisher->id);
            }
        }

        return redirect('/procedure/'.$procedure_id)->with('message', 'You request has been sent! you will be notify for approval or denial');

    }

    // proposed Change Action
    public function actionOnChange(Request $request)
    {
        $procedure_data_id = $request->input('procedure_data_id');
        $status = $request->input('status');

        $get = DB::table('procedure_data')->where('id', $procedure_data_id)->first();
        $procedure = DB::table('folder_and_procedure')->where('id', $get->folder_and_procedure_id)->first();
        $type = 'procedure';

        $newStatus = [
            'status' => $status
        ];

        if ($status == 'publish') {
            $newStatus = [
                'content' => $get->content,
            ];

            $publishedNew = DB::table('procedure_data')->where('id', $get->parent_id)->update($newStatus);
            $deleteOld = DB::table('procedure_data')->where('id', $procedure_data_id)->delete();

            $title = \Lang::get('messages.propose.published', ['user' => Auth::user()->name, 'procedureName' => $procedure->name]);
            $body = \Lang::get('messages.propose.published', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$procedure->id.'">'.$procedure->name.'</a>']);

            $userAccess =  DB::table('user_department')->where([['department_id', $procedure->department_id]])->get();

            if ($userAccess) {
                foreach ($userAccess as $key => $user) {
                    $departmentUsers = User::find($user->user_id);
                    $this->addNotification($title, $body, $type, NULL, $procedure->id, Auth::user()->id, $departmentUsers->id);
                }
            }

            return response()->json(['status'=> $status, 'procedure' => $get->parent_id, 'content' => $get->content]);
        }

        $updateStatus = DB::table('procedure_data')->where('id', $procedure_data_id)->update($newStatus);
        if ($status == 'accept') {

            $title = \Lang::get('messages.propose.accepted', ['user' => Auth::user()->name, 'procedureName' => $procedure->name]);
            $body = \Lang::get('messages.propose.accepted', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$procedure->id.'">'.$procedure->name.'</a>']);

        } else if($status == 'reject') {

            $title = \Lang::get('messages.propose.reject', ['user' => Auth::user()->name, 'procedureName' => $procedure->name]);
            $body = \Lang::get('messages.propose.reject', ['user' => Auth::user()->name, 'procedureName' => '<a href="/procedure/'.$procedure->id.'">'.$procedure->name.'</a>']);

        }

        $this->addNotification($title, $body, $type, NULL, $procedure->id, Auth::user()->id, $get->user_id);
        
        return response()->json(['status'=>$status, 'procedure' => $get->parent_id]);

    }

    public function favourites_all()
    {
        $favourites = DB::table('favourites')
        ->join('folder_and_procedure', 'folder_and_procedure.id', '=', 'favourites.fold_proc_id')
        ->join('departments', 'folder_and_procedure.department_id', '=', 'departments.id')
        ->select('favourites.*', 'folder_and_procedure.*', 'folder_and_procedure.id AS folder_and_procedureID', 'folder_and_procedure.name AS folder_and_procedureName', 'favourites.status AS favourites_status', 'departments.name AS department_name', 'departments.id AS departmentID', 'folder_and_procedure.created_at AS folder_and_procedureCreateDate', 'folder_and_procedure.name AS folder_and_procedureUpdateDate')
        ->where([['favourites.status', '1'], ['favourites.user_id', Auth::user()->id]])
        ->orderBy('favourites.id', 'DESC')
        ->get();

        return Voyager::view('vendor.voyager.departments.favourites')->with(compact('favourites'));

    }
	

	
	// create GUID (General Uniquq ID)
	function getGUID(){
	    if (function_exists('com_create_guid')){
	        return com_create_guid();
	    }else{
	        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
	        $charid = strtoupper(md5(uniqid(rand(), true)));
	        $hyphen = chr(45);// "-"
	        $uuid = chr(123)// "{"
	            .substr($charid, 0, 8).$hyphen
	            .substr($charid, 8, 4).$hyphen
	            .substr($charid,12, 4).$hyphen
	            .substr($charid,16, 4).$hyphen
	            .substr($charid,20,12)
	            .chr(125);// "}"
	        return $uuid;
	    }
	}
	
}
