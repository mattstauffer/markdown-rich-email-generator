<?php

use App\Commands\EmailConvert;

Route::get('/', function() {
    return Redirect::to('test');
});

Route::get('test', function() {
    return Bus::dispatch(
        new EmailConvert('welcome.md')
    );
});
