<?php

//Request without token
Route::post('/create', 'UserController@create');
Route::post('/login', 'UserController@login');

//Request with token. (authUser -> Middleware [go to .../app/Http/cre/AuthUser.php])
Route::middleware(['authUser'])->group(function () {
    Route::get('/account', 'UserController@account');
    Route::patch('/update', 'UserController@update');
    Route::delete('/delete/{id}', 'UserController@delete');
});