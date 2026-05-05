<?php


use Illuminate\Support\Facades\Log;

function dlog($title, $data = null)
{
    Log::info($title, [
        'data' => $data
    ]);
}