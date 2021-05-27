<?php
return [
    /**
     * You can generate API keys here: https://developer.media.io.
     */
    'apiKey' => env('ONLINEUNICONVERTER_API_KEY', ''),

    /**
     * Use the OnlineUniConverter Sanbox API (Defaults to false, which enables the Production API).
     */
    'sandbox' => env('ONLINEUNICONVERTER_SANDBOX', false),
];
