<?php
return [
    /**
     * You can generate API keys here: https://developer.media.io.
     */
    'apiKey' => env('ONLINEUNICONVERT_API_KEY', ''),

    /**
     * Use the OnlineUniConvert Sanbox API (Defaults to false, which enables the Production API).
     */
    'sandbox' => env('ONLINEUNICONVERT_SANDBOX', false),
];
