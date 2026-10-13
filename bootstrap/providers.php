<?php

use App\Providers\AppServiceProvider;
use App\Providers\FirebaseEmailService;
use App\Providers\FirebaseServiceProvider;

return [
    AppServiceProvider::class,
    FirebaseEmailService::class,
    FirebaseServiceProvider::class,
];
