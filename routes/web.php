<?php

Route::group(['prefix' => 'dev'], function () {
    
    Route::get('er/main/{entity}', 'ER\ERDiagramController@main');
    Route::get('er/xml/{entity}', 'ER\ERDiagramController@xml');

    Route::get( 'er/all.js', function(){

        $jss = ["oz.js",
            "config.js",
            "globals.js",
            "visual.js",
            "row.js",
            "table.js",
            "relation.js",
            "key.js",
            "rubberband.js",
            "map.js",
            "toggle.js",
            "io.js",
            "tablemanager.js",
            "rowmanager.js",
            "keymanager.js",
            "window.js",
            "options.js",
            "wwwsqldesigner.js"];

        $cont = '';
        foreach($jss as $js){
            $cont .= file_get_contents(app_path("Http/Controllers/ER/views/sqldesigner/js/{$js}"));
        }

        return response($cont, 200)
            ->header('Content-Type', 'application/javascript');
    });

    Route::get( 'er/{path}.{ext}', function( $path, $ext ){

        \Debugbar::disable();

        $header = [
            'js' => 'application/javascript',
            'css' => 'text/css',
            'xml' => 'application/xml',
            'gif' => 'image/gif',
            'png' => 'image/png',
        ];

        $path = app_path("Http/Controllers/ER/views/sqldesigner/{$path}.{$ext}");
        return response(file_get_contents($path), 200)
            ->header('Content-Type', $header[$ext]);

    })->where('path', '[/0-9A-Za-z]+')
        ->where('ext', '[A-Za-z]+');

});