<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     // return view('welcome');

// });


Route::group(['prefix' => '/'], function () {
    Voyager::routes();
});

/******************************************************* custom routes *************************************************************/

Route::group(['middleware' => 'auth'], function() {

	// my profile edit page
	Route::get('/my-profile', function () {
	    return view('vendor.voyager.users.my-profile');
	});

	// my profile edit page
	Route::get('/users/{id}/reset_password', 'Custom\CustomController@reset_password_send');
	//Add user to department
	Route::post('/add-user-to-department', 'Custom\CustomController@add_user_to_department');
	//Delete user to department
	Route::get('/departments/{department_id}/remove-user/{user_id}', 'Custom\CustomController@delete_user_to_department');
	// Rename Department
	Route::post('/departments/{department_id}/rename', 'Custom\CustomController@rename_department');
	// Add foler in department
	Route::post('/folders', 'Custom\CustomController@add_folder_procedure');
	// Add procedure in department
	Route::post('/procedures', 'Custom\CustomController@add_folder_procedure');
	// Duplicate Procedure
	Route::get('/procedures/{id}/duplicate', 'Custom\CustomController@duplicate_procedure');
	// Rename Procedure/Folder 
	Route::post('/proc_fold_rename', 'Custom\CustomController@proc_fold_rename');
	// Toggle Favourite folder/procedure
	Route::get('/{type}/{id}/toggle-favorite', 'Custom\CustomController@toggle_favourite');
	// Delete Procedure/ Folder
	Route::get('/{type}/{id}/destroy', 'Custom\CustomController@destroy_folder_procedure');
	// Undo Delete Procedure/ Folder
	Route::get('/{type}/{id}/undestroy', 'Custom\CustomController@undestroy_folder_procedure');
	// View foler in department
	Route::get('/browse/folder/{id}', 'Custom\CustomController@folder_view');
	// Edit Procedure Page
	Route::get('/procedure/{id}/edit', 'Custom\CustomController@procedure_edit');
	// dropzone Image upload
	Route::post('/dropzone/imageupload', 'Custom\CustomController@imageupload');
	// dropzone File upload
	Route::post('/dropzone/attachupload', 'Custom\CustomController@attachupload');
	// download Attachment
	Route::get('/block-attachments/{id}/download', 'Custom\CustomController@attachdownload');
	// dropzone video upload
	Route::post('/dropzone/attachvideo', 'Custom\CustomController@attachvideo');
	// Add new procedure data
	Route::post('/procedure/addNewProcedureData', 'Custom\CustomController@addNewProcedureData');
	// Update procedure data
	Route::post('/procedure/updateProcedureData', 'Custom\CustomController@updateProcedureData');
	// Mark/Unmark Step
	Route::post('/procedure/markStep', 'Custom\CustomController@markStep');
	// Clone Block
	Route::post('/procedure/cloneBlock', 'Custom\CustomController@cloneBlock');
	// Change Status
	Route::post('/procedure/changeStatus', 'Custom\CustomController@changeStatus');
	// permenent Delete
	Route::post('/procedure/permenentDelete', 'Custom\CustomController@permenentDelete');
	// Expand/Collapse
	Route::post('/procedure/expand', 'Custom\CustomController@expand');
	// Procedure Publish directly 
	Route::get('/procedure/{id}/publish_directly', 'Custom\CustomController@publish_directly');
	// Preview Procedure Page
	Route::get('/procedure/{id}/preview', 'Custom\CustomController@procedure_preview');
	// View Procedure Page
	Route::get('/procedure/{id}', 'Custom\CustomController@procedure_view');
	// Add comment
	Route::post('/procedure/add_comment', 'Custom\CustomController@add_comment');
	// Delete comment
	Route::post('/comment/comment_destroy', 'Custom\CustomController@comment_destroy');
	// take Ownership of procedure
	Route::get('/procedure/{id}/take_ownership', 'Custom\CustomController@take_ownership');
	// Propose New Add/Edit
	Route::post('/procedure/propose_new', 'Custom\CustomController@propose_new');
	// check previous propose
	Route::post('/procedure/checkPropose', 'Custom\CustomController@checkPropose');
    // check All Proposes 
	Route::post('/procedure/checkAllProposes', 'Custom\CustomController@checkAllProposes');
	// dropzone Image upload change on propose
	Route::post('/dropzone/imageuploadchange', 'Custom\CustomController@imageuploadchange');
	// dropzone Attachment upload change on propose
	Route::post('/dropzone/attachuploadchange', 'Custom\CustomController@attachuploadchange');
	// Notifications
	Route::get('/notification', 'Custom\CustomController@notification');
	// mark Read Notification
	Route::post('/notification/readNotification', 'Custom\CustomController@readNotification');
	// Submit for approval
	Route::post('/procedure/submit_for_approval', 'Custom\CustomController@submit_for_approval');
	// proposed Change Submit
	Route::post('/proposedChange', 'Custom\CustomController@proposedChange');
	// proposed Change Action
	Route::post('/actionOnChange', 'Custom\CustomController@actionOnChange');
	// review approval page
	Route::get('/procedure/{id}/review_approval', 'Custom\CustomController@review_approval');
	// Procedure Reject 
	Route::get('/procedure/{id}/reject', 'Custom\CustomController@reject');
	// Submit for review
	Route::post('/procedure/ask_for_review', 'Custom\CustomController@submit_for_approval');
	// review request page
	Route::get('/procedure/{id}/request_review', 'Custom\CustomController@request_review');
	// favourite page
	Route::get('/favourites', 'Custom\CustomController@favourites_all');

	

});






