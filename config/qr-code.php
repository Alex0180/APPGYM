<?php

return [
    /*
    |--------------------------------------------------------------------------
    | QR Code Configuration
    |--------------------------------------------------------------------------
    */

    'format' => 'png',
    'generate' => 'gd', // <- aquí pones GD en vez de Imagick
    'size' => 300,
    'margin' => 2,
    'errorCorrection' => 'H',
    'encoding' => 'UTF-8',
    'backEnd' => 'gd', // <- usar GD en lugar de Imagick



    
];
