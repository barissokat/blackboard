<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::resource('projects', 'ProjectController');


Route::post('projects/{project}/tasks', ['as' => 'tasks.store', 'uses' => 'ProjectTaskController@store']);
Route::patch('projects/{project}/tasks/{task}', ['as' => 'tasks.update', 'uses' => 'ProjectTaskController@update']);

Route::post('projects/{project}/invitations', ['as' => 'invitations.store', 'uses' => 'ProjectInvitationController@store']);

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
