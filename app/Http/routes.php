<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
    Route::any('login', 'Mes\LoginController@login');
    Route::get('code', 'Mes\LoginController@code');
//    Route::any('index','Mes\IndexController@index');

    Route::get('send_mail', 'Mes\IndexController@send_email');
});

Route::group(['middleware' => ['web','mes.login'],'namespace'=>'Mes'], function () {
    //
    Route::get('contacts','IndexController@contacts');
    Route::any('index','IndexController@index');
    Route::any('logout','LoginController@logout');
    Route::get('home','SameController@home');
    Route::any('search_work_order','SameController@search_work_order');
    Route::any('new_work_order','SameController@new_work_order');
    Route::get('create_run_card','SameController@create_run_card');
    Route::get('dismantle_run_card','SameController@dismantle_run_card');
    Route::get('run_card_on_line','SameController@run_card_on_line');
    Route::get('run_card_close','SameController@run_card_close');
    Route::get('station_wip','SameController@station_wip');
    Route::any('create_lot/{work_order}','SameController@create_lot');
    Route::any('create_lot_list/{work_order}','SameController@create_lot_list');
    Route::any('new_lots/{work_order}','SameController@new_lots');
    Route::any('dismantle_lot/{lot_id}','SameController@dismantle_lot');
    Route::any('ajax_sel_flow','SameController@ajax_sel_flow');

    Route::any('prod_mgt/{make_area}','ProductMGTController@prod_mgt');
    
    Route::get('test','SameController@test');
});

