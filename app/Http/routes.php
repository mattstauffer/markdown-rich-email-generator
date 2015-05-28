<?php

use App\Commands\EmailConvert;

Route::get('/', 'WelcomeController@index');

Route::get('test', function() {
    return Bus::dispatch(
        new EmailConvert('2015-05-28.md')
    );
});
