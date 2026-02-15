<?php

use Database\Seeders\PageSeeder;

it('returns a successful response', function () {
    $this->seed(PageSeeder::class);

    $response = $this->get('/');

    $response->assertSuccessful();
});
