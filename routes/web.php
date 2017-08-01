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

/*Route::get('/', function () {
    return view('welcome');
});*/

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => 'web'], function () {

Route::get('/', 'JobboardController@home');

    Route::get('/contact', 'JobboardController@contact');
    Route::get('/job-board', 'JobboardController@jobboard');
    Route::post('/job-search', 'JobboardController@jobsearch');
    Route::get('/job-details/{id}', 'JobboardController@jobdetails');
    Route::post('/sendcontactmail', 'JobboardController@sendcontactmail');

    Route::get('/candidates', 'JobboardController@candidates');
    Route::get('/candidate-details/{id}', 'JobboardController@candidatedetails');
    Route::post('/candidate-search', 'JobboardController@candidatesearch');

});



Route::group(['middleware' => ['web','auth']], function () {

    Route::get('/activate/{jobid}', 'JobboardController@activate');
    Route::get('/activateresume/{resumeid}', 'JobboardController@activateresume');

    Route::get('/jobs-posted', 'JobboardController@jobsposted');
    Route::get('/resumes-posted', 'JobboardController@resumesposted');
    Route::get('/jobs-applied', 'JobboardController@jobsapplied');

    Route::get('/apply/{jobid}', 'JobboardController@apply');
    Route::get('/post-job', 'JobboardController@postjob');
    Route::get('/post-resume', 'JobboardController@postresume');
    Route::post('/post-resume', 'JobboardController@saveresumepost');
    Route::post('/post-job', 'JobboardController@savejobpost');

    Route::get('/edit-resume/{id}', 'JobboardController@editresume');
    Route::get('/edit-job/{id}', 'JobboardController@editjob');

    Route::post('/edit-resume', 'JobboardController@updateresumepost');
    Route::post('/edit-job', 'JobboardController@updatejobpost');
    Route::get('/home', 'JobboardController@home');
    Route::get('/job-applications/{id}', 'JobboardController@jobapplications');


});

