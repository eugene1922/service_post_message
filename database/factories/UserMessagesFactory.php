<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserMessage::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween(0, 1000),
        'message' => $faker->sentence()
    ];
});
