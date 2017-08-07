<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', '\App\Http\Controllers\Auth\LoginController@currentUser');

Route::middleware('guest')->post('/authenticate', [
	'as'	=>	'get-authenticated-user',
	'uses'	=>	'\App\Http\Controllers\Auth\LoginController@authenticate'
]);

Route::middleware('guest')->get('/tasks/public', [
	'as' => 'get-public-tasks',
	'uses' => '\App\Http\Controllers\TaskController@allPublicTasks'
]);

Route::middleware('guest')->post('/register', [
	'as' => 'register-user',
	'uses' => '\App\Http\Controllers\Auth\RegisterController@store'
]);

Route::middleware('guest')->get('/login/github', '\App\Http\Controllers\Auth\LoginController@redirectToProvider');
Route::middleware('guest')->get('/login/github/callback', '\App\Http\Controllers\Auth\LoginController@handleProviderCallback');

Route::group(['prefix' => 'users', 'middleware' => 'auth:api'], function (){
	Route::get('/searchUsername/{username}', [
		'as'	=>	'search-username',
		'uses'	=>	'\App\Http\Controllers\Auth\LoginController@searchUsername'
	]);
	Route::get('/searchName/{name}', [
		'as'	=>	'search-name',
		'uses'	=>	'\App\Http\Controllers\Auth\LoginController@searchName'
	]);
	Route::get('/searchEmail/{email}', [
		'as'	=>	'search-email',
		'uses'	=>	'\App\Http\Controllers\Auth\LoginController@searchEmail'
	]);
	Route::post('/change/password', [
		'as' => 'change-password',
		'uses' => '\App\Http\Controllers\Auth\LoginController@changePassword'
	]);
	Route::post('/upload/avatar', [
		'as' => 'upload-avatar',
		'uses' => '\App\Http\Controllers\Auth\LoginController@avatar'
	]);
	Route::get('/logout', [
		'as' => 'logout',
		'uses' => '\App\Http\Controllers\Auth\LoginController@destroy'
	]);
});

Route::group(['prefix' => 'tasks', 'middleware' => 'auth:api'], function (){
	Route::post('/create', [
		'as'	=>	'create-task',
		'uses'	=>	'\App\Http\Controllers\TaskController@store'
	]);
	Route::get('/mytasks', [
		'as' => 'get-my-tasks',
		'uses' => '\App\Http\Controllers\TaskController@myTasks'
	]);
	Route::get('/incomplete', [
		'as' => 'get-incomplete-tasks',
		'uses' => '\App\Http\Controllers\TaskController@allIncompleteTasks'
	]);
	Route::get('/task/{name}', [
		'as' => 'get-task',
		'uses' => '\App\Http\Controllers\TaskController@getTask'
	]);
	Route::post('/task/{task}/private', [
		'as' => 'mark-private-task',
		'uses' => '\App\Http\Controllers\TaskController@markPrivate'
	]);
	Route::post('/task/{task}/completed', [
		'as' => 'mark-completed-task',
		'uses' => '\App\Http\Controllers\TaskController@markCompleted'
	]);
	Route::delete('/delete/{task}', [
		'as' => 'delete-task',
		'uses' => '\App\Http\Controllers\TaskController@destroy'
	]);
	Route::post('/task/{task}/toggle', [
		'as'	=>	'toggle-task',
		'uses'	=>	'\App\Http\Controllers\TaskController@toggleStatus'
	]);
	Route::post('/attach/file', [
		'as' => 'attach-file',
		'uses' => '\App\Http\Controllers\TaskController@attach'
	]);
});

Route::group(['prefix' => 'invitations', 'middleware' => 'auth:api'], function (){
	Route::post('/invite', [
		'as' => 'invite',
		'uses' => '\App\Http\Controllers\InvitationController@invite'
	]);
	Route::post('/accept', [
		'as' => 'accept-invitation',
		'uses' => '\App\Http\Controllers\InvitationController@acceptInvitation'
	]);
	Route::post('/reject', [
		'as' => 'reject-invitation',
		'uses' => '\App\Http\Controllers\InvitationController@rejectInvitation'
	]);
	Route::get('/invitation/{inv}', [
		'as' => 'get-invitation',
		'uses' => '\App\Http\Controllers\InvitationController@getInvitation'
	]);
});

Route::group(['prefix' => 'following', 'middleware' => 'auth:api'], function (){
	Route::post('/task/follow', [
		'as'	=>	'follow-task',
		'uses'	=>	'\App\Http\Controllers\FollowController@follow'
	]);
	Route::post('/task/unfollow', [
		'as'	=>	'unfollow-task',
		'uses'	=>	'\App\Http\Controllers\FollowController@unfollow'
	]);
	Route::post('/followedtasks', [
		'as' => 'get-followed-tasks',
		'uses' => '\App\Http\Controllers\FollowController@followedTasks'
	]);
});