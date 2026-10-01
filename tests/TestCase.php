<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function admin(): User
    {
        return User::factory()->admin()->create();
    }

    protected function staff(): User
    {
        return User::factory()->create();
    }
}
