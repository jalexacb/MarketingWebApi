<?php

use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('private-channel.{id}', function ($user) {
//     return true;
// });

$broadcast = app(Illuminate\Contracts\Broadcasting\Broadcaster::class);

$broadcast->channel('private-channel.{id}', function ($user, $id) {
            return true;
        });
// $broadcast = app(Illuminate\Contracts\Broadcasting\Broadcaster::class);

// $broadcast->channel('private-channel.{id}', function ($user, $id) {
//         return true;
//     });