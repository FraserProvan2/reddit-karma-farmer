<?php

namespace App;

use Illuminate\Support\Facades\Log;
use App\RedditAPI;

class RepostProcess
{
    private $reddit_api;
    private $sub_reddits;
    private $attempts = 0;

    /**
     * A recursive algorithm to pick a sub reddit, search for a 
     * post and repost the post. 
     * 
     * @return void
     */
    public function run()
    {
        $this->reddit_api = new RedditAPI;

        $this->attempts++; // keep track of progress
        $attempts_max = 20;
        if ($this->attempts > $attempts_max) {
            Log::error('RepostController: Failed ' . $attempts_max . ' attempts');
            return response([
                'status' => 'error',
                'message' => 'Failed after ' . $attempts_max . ' attempts'
            ]);
        }
        Log::debug('RepostController: process start attempt #' . $this->attempts);

        $subreddit = $this->findSubReddit();
        Log::debug('RepostController: chosen subreddit: /r/' . $subreddit);

        $selected_post = $this->findPost($subreddit);
        if (!$selected_post) {
            Log::warning('RepostController: failed to find link...');
            return $this->run(); // rerun if no link found
        }
        Log::debug('RepostController: chosen post: https://reddit.com' . $selected_post->permalink);

        $result = $this->repostPost($selected_post);
        if (count($result->json->errors) > 0) {
            Log::warning('RepostController: failed to post: ' . json_encode($result->json->errors));
            return $this->run(); // failure is NOT an option
        }
        Log::debug('RepostController: post success: ' . json_encode($result->json->data->url));

        return response([
            'status' => 'success',
            'message' => 'Successfully reposted on attempt #' . $this->attempts
        ]);
    }

    /*------------------------------------------------------------------------
  | Encapsulated logic methods
  |-------------------------------------------------------------------------*/

    /**
     * Finds a Subreddit the accounts is subscribed to
     * 
     * @return string subreddit name
     */
    private function findSubReddit()
    {
        if (!isset($this->sub_reddits)) {
            $this->sub_reddits = $this->reddit_api->get('/subreddits/mine/subscriber');
        }
        // randomly pick by index
        $chosen_sub_reddit = $this->sub_reddits->data->children[rand(0, count($this->sub_reddits->data->children) - 1)];

        return $chosen_sub_reddit->data->display_name;
    }

    /**
     * Finds a post using subreddit as search query
     * 
     * @param string subreddit name
     * @return object|bool Post Object or false
     */
    private function findPost(string $subreddit)
    {
        $sites = ['imgur.com', 'gfycat.com'];

        // find a post
        $endpoint = '/r/' . $subreddit . '/search';
        $query_string = http_build_query([
            'q' => 'site:' . $sites[rand(0, count($sites) - 1)], // search by link referer
            't' => 'year', // time
            'limit' => 100,
            'type' => 'link',
            'restrict_sr' => 1
        ]);
        $request_url = $endpoint . '?' . $query_string;
        $results = $this->reddit_api->get($request_url);

        // look for link flair
        $this->shufflePosts($results->data->children);
        foreach ($results->data->children as $post) {
            if (isset($post->data->post_hint) && $post->data->post_hint === "link") {
                return $post->data;
            }
        };

        return false;
    }

    /**
     * Reposts the post
     * 
     * @param object 'post' object
     * @return object response
     */
    private function repostPost(object $post)
    {
        $cloned_post = [
            'title' => $this->modifyTitle($post->title),
            'sr' => $post->subreddit,
            'url' => $this->webifyUrl($post->url) . '?utm=rkf-23',
            'kind' => 'link',
            // 'uh' => 'f0f0f0f0', 
        ];

        Log::debug('RepostController: cloned data:', $cloned_post);

        return $this->reddit_api->createPost($cloned_post);
    }

    /*------------------------------------------------------------------------
  | Helper functions
  |-------------------------------------------------------------------------*/

    /**
     * Shuffle posts array 
     * 
     * @param array posts
     * @return array shuffled posts
     */
    private function shufflePosts(array $posts)
    {
        for ($roll = rand(1, 4); $roll > 0; $roll--) {
            $shuffled_posts = shuffle($posts);
        }

        return $shuffled_posts;
    }

    /**
     * Modify title in order to avoid duplication checks
     * 
     * @param string title
     * @return string modified title
     */
    private function modifyTitle(string $title)
    {
        $title = strtolower($title);
        $title = ucfirst($title);
        // $title = str_replace(' ', '  ', $title);

        return ' ' . $title;
    }

    /**
     * Add 'wwww' to url if not there
     * 
     * @param string url
     * @return string modified url
     */
    private function webifyUrl(string $url)
    {
        if (!strpos($url, 'www')) {
            $exploded_url = explode('//', $url);
            return $exploded_url[0] . '//www.' . $exploded_url[1];
        }

        return $url;
    }
}
