<?php

return [
    'hash_key' => env('AUDIT_HASH_KEY'),
    'lock_name' => env('AUDIT_HASH_LOCK', 'airportops_audit_chain'),
    'lock_wait' => (int) env('AUDIT_HASH_LOCK_WAIT', 10),
];
