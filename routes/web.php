<?php

use Illuminate\Support\Facades\Route;


Route::get('/', 'AuthController@index');
Route::get('/add_leads', 'LeadsListController@leads');
