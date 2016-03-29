<?php

use App\Commands\EmailConvert;

Route::get('/', function() {
    return Redirect::to('latest');
});

Route::get('latest', function() {
    $files = File::files(base_path() . '/resources/emailcontent/');
    $filename = $files[count($files) - 2];
    $filename = explode('/', $filename);
    $filename = end($filename);

    return Bus::dispatch(
        new EmailConvert($filename)
    );
});

Route::get('welcome', function() {
    return Bus::dispatch(
        new EmailConvert('welcome.md')
    );
});
