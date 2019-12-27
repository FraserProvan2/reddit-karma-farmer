<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Seeds and signs in
     *
     * @return void
     */
    public function signIn()
    {
        return 1;
        // $this->artisan("db:seed");

        // $this->actingAs(User::find(1))
        //     ->assertAuthenticated();
    }
}
