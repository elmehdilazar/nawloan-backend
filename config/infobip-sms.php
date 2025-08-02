<?php

/**
 * This is config for Infobip SMS.
 *
 * @see https://dev.infobip.com/send-sms/single-sms-message
 */
return [
    'from'     => env('INFOBIP_FROM', 'Nawloan'),
    'username' => env('INFOBIP_USERNAME', 'nawloan'),
    'password' => env('INFOBIP_PASSWORD'),
];
