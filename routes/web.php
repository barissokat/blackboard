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

Route::get('projects', ['as' => 'projects.index', 'uses' => 'ProjectController@index']);
Route::get('projects/create', ['as' => 'projects.create', 'uses' => 'ProjectController@create']);
Route::post('projects', ['as' => 'projects.store', 'uses' => 'ProjectController@store']);
Route::get('projects/{project}', ['as' => 'projects.show', 'uses' => 'ProjectController@show']);
Route::get('projects/{project}/edit', ['as' => 'projects.edit', 'uses' => 'ProjectController@edit']);
Route::patch('projects/{project}', ['as' => 'projects.update', 'uses' => 'ProjectController@update']);
Route::delete('projects/{project}', ['as' => 'projects.destroy', 'uses' => 'ProjectController@destroy']);


Route::post('projects/{project}/tasks', ['as' => 'tasks.store', 'uses' => 'ProjectTaskController@store']);
Route::patch('projects/{project}/tasks/{task}', ['as' => 'tasks.update', 'uses' => 'ProjectTaskController@update']);

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
