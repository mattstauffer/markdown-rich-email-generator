<?php

use App\EmailConverter;

Route::get('/', function() {
    return Redirect::to('latest');
});

Route::get('latest', function() {
    $files = File::files(base_path() . '/resources/emailcontent/');
    $filename = $files[count($files) - 2];
    $filename = explode('/', $filename);
    $filename = end($filename);

    return app(EmailConverter::class)->convertFile($filename);
});

Route::get('welcome', function() {
    return app(EmailConverter::class)->convertFile('welcome.md');
});
