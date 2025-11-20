<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

(require __DIR__ . '/web/home.php')();
(require __DIR__ . '/web/auth.php')();
(require __DIR__ . '/web/profile.php')();
(require __DIR__ . '/web/admin.php')();
(require __DIR__ . '/web/volcanoes.php')();
(require __DIR__ . '/web/dev.php')();
