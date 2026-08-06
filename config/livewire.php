<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Temporary File Uploads
    |--------------------------------------------------------------------------
    |
    | Livewire handles file uploads by storing them in a temporary directory
    | before they are saved to their final destination.
    |
    */

    'temporary_file_upload' => [
        'disk' => 'public',
        'rules' => ['file', 'max:512000'], // Allow up to 500MB for audio uploads
        'directory' => 'livewire-tmp',
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4', 'mov', 'avi', 'wmv', 'mp3', 'm4a', 'aac', 'ogg', 'webm',
        ],
        'max_upload_time' => 120,
    ],

];
