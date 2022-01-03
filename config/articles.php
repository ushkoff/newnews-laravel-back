<?php

/*
|--------------------------------------------------------------------------
| Articles config
|--------------------------------------------------------------------------
*/

return [
    //
    // 'MAX_' - prefix with values to control entering data in requests.
    //
    'max_article_title' => env('MAX_ARTICLE_TITLE'),

    'max_article_content' => env('MAX_ARTICLE_CONTENT'),

    'max_article_refs' => env('MAX_ARTICLE_REFS'),

    // Time for user (after publishing article) to edit it, if it's needed
    'max_article_editing_minutes' => env('MAX_ARTICLE_EDITING_MINUTES', 240), // default: 4 hours
];
