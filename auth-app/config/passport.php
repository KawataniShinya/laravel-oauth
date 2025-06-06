<?php

return [
    'token_expires_days' => (int) env('PASSPORT_TOKEN_EXPIRES_DAYS', 15),
    'refresh_token_expires_days' => (int) env('PASSPORT_REFRESH_TOKEN_EXPIRES_DAYS', 30),
];
