<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('cart:move-abandoned')
    ->everyFiveMinutes();

Schedule::command('cart:send-abandoned-emails')
    ->everyTenMinutes();

