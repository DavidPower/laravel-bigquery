<?php

use Illuminate\Support\Facades\Storage;

return [

    'notification_emails' => [
        'david@davidpower.com'
    ],

    'drip_account_id' => env('DRIP_ACCOUNT_ID', '9317564'),

    'bq_dataset_name' => env('BQ_DATASET_NAME', 'cgc'),

    'bq_events_table_name' => env('BQ_EVENTS_TABLE_NAME', 'events'),

    'drip_api_token' => env('DRIP_API_TOKEN'),

    'drip_api_url' => env('DRIP_API_URL'),

//    'google_app_cred' => env('GOOGLE_APPLICATION_CREDENTIALS', Storage::get('file.json')),

    'license_types' => [
        '11075408' => 'NEW - 5 GRAMS PER DAY (10 minutes, no price set)',
        '11075412' => 'NEW - 10 GRAMS PER DAY (10 minutes, no price set)',
        '10477420' => 'NEW - 15 GRAMS PER DAY (10 minutes, no price set)',
        '10477423' => 'NEW - 20 GRAMS PER DAY (10 minutes, no price set)',
        '10477433' => 'NEW - 30 GRAMS PER DAY (10 minutes, no price set)',
        '15830875' => 'NEW - 40 GRAMS PER DAY (10 minutes, no price set)',
        '10477435' => 'NEW - 50 GRAMS PER DAY (10 minutes, no price set)',
        '10304899' => 'NEW - 60g GRAMS PER DAY (10 minutes, no price set)',
        '15830884' => 'NEW - 70g GRAMS PER DAY (10 minutes, no price set)',
        '10477396' => 'NEW - 80g GRAMS PER DAY (10 minutes, no price set)',
        '10477404' => 'NEW - 95 GRAMS PER DAY (10 minutes, no price set)',
        '23994289' => 'RENEWAL - 5 GRAMS PER DAY (10 minutes, no price set)',
        '23994306' => 'RENEWAL - 10 GRAMS PER DAY (10 minutes, no price set)',
        '23994338' => 'RENEWAL - 15 GRAMS PER DAY (10 minutes, no price set)',
        '23994347' => 'RENEWAL - 20 GRAMS PER DAY (10 minutes, no price set)',
        '23994355' => 'RENEWAL - 30 GRAMS PER DAY (10 minutes, no price set)',
        '23994363' => 'RENEWAL - 40 GRAMS PER DAY (10 minutes, no price set)',
        '23994374' => 'RENEWAL - 50 GRAMS PER DAY (10 minutes, no price set)',
        '23994388' => 'RENEWAL - 60g GRAMS PER DAY (10 minutes, no price set)',
        '23994395' => 'RENEWAL - 70g GRAMS PER DAY (10 minutes, no price set)',
        '23994427' => 'RENEWAL - 80g GRAMS PER DAY (10 minutes, no price set)',
        '23994447' => 'RENEWAL - 95 GRAMS PER DAY (10 minutes, no price set)',
    ],
];
