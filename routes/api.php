<?php

use App\Http\Controllers\Admin\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/live/quick-count', [VoteController::class, 'votesCount'])->name('api.live-count');
