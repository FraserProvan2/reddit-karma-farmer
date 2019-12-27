<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class RepostProcessTest extends TestCase
{
    // use RefreshDatabase;

    /** @test */
    public function find_subreddit_returns_subreddit()
    {
        $case = new TestCase;

        dd($case->hello());
    }

    /** @test */
    public function find_post_returns_a_post()
    {
        //
    }

    /** @test */
    public function shuffle_posts_shuffles_array()
    {
        //
    }

    /** @test */
    public function modify_title_modifies_as_expected()
    {
        //
    }
}
