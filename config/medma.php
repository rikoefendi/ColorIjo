<?php

return [
    'path' => 'medma/images',
    'watermark' => 'public/img/watermark.png',
    'size' => [
        'large' => [
            'auto' => false,
            'w' => 800,
            'h' => 650
            ],
        'medium' => [
            'auto' => false,
            'w' => 720,
            'h' => 480
            ],
        'small' => [
            'auto' => false,
            'w' => 480,
            'h' => 360
            ],
    ],
    'compress_quality' => 60,
    'canvas' => true,
];
