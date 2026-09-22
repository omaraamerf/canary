<?php

$cloudinaryUrl = parse_url((string) env('CLOUDINARY_URL', '')) ?: [];

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'cloudinary' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME', $cloudinaryUrl['host'] ?? null),
        'api_key' => env('CLOUDINARY_API_KEY', $cloudinaryUrl['user'] ?? null),
        'api_secret' => env('CLOUDINARY_API_SECRET', $cloudinaryUrl['pass'] ?? null),
        'folder' => env('CLOUDINARY_FOLDER', 'canary/birds'),
        'upload_timeout' => env('CLOUDINARY_UPLOAD_TIMEOUT', 120),
        'max_images' => env('CLOUDINARY_MAX_IMAGES', 8),
        'max_videos' => env('CLOUDINARY_MAX_VIDEOS', 3),
        'image_max_kb' => env('CLOUDINARY_IMAGE_MAX_KB', 10240),
        'video_max_kb' => env('CLOUDINARY_VIDEO_MAX_KB', 51200),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
