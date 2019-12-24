<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RedditAPI;

class RepostController extends Controller
{
    public $attempts = 0;

    public function run()
    {
        $this->attempts++; // keep track of progress
        if ($this->attempts > 30) {
            Log::error('RepostController: Failed 30 attempts');
            return response([
                'Error, failed after 30 attempts'
            ], 400);
        }

        Log::debug('RepostController: process start attempt #' . $this->attempts);
        
        // create Reddit API client
        $reddit_api = new RedditAPI;

        // find a sub reddit
        $sub_reddits = $reddit_api->get('/subreddits/mine/subscriber');
        $selected_subreddit = $sub_reddits->data->children[rand(0, count($sub_reddits->data->children) - 1)]->data->display_name;
        Log::debug('RepostController: chosen subreddit: /r/' . $selected_subreddit);

        // find a post
        $endpoint = '/r/' . $selected_subreddit . '/search';
        $query_string = http_build_query([
            'q' => $selected_subreddit, // query
            't' => 'year', // time
            'limit' => 100
        ]);
        $request_url = $endpoint . '?' . $query_string;
        $results = $reddit_api->get($request_url);

        // look for link flair
        $selected_post = null;
        shuffle($results->data->children);
        foreach($results->data->children as $post) {
            if (isset($post->data->post_hint) && 
                $post->data->post_hint === "link" && 
                $post->data->domain != "i.redd.it" &&
                $post->data->domain != "v.redd.it"
            ) {
                $selected_post = $post->data;
            }
        };  
        if (!isset($selected_post)) {
            Log::warning('RepostController: failed to find link...');
            return $this->run(); // rerun if no link found
        }
        Log::debug('RepostController: chosen post: https://reddit.com' . $selected_post->permalink);

        // repost 
        $cloned_post = [
            'title' => $selected_post->title,
            'sr' => $selected_post->subreddit,
            'url' => $selected_post->url,
            'kind' => 'link',
            // 'uh' => 'f0f0f0f0', 
        ];
        Log::debug('RepostController: cloned data:', $cloned_post);
        $result = $reddit_api->createPost($cloned_post);
        
        if (!$result->success) {
            Log::warning('RepostController: failed to post... here we go again...');
            return $this->run(); // failure is NOT an option
        }

        Log::debug('RepostController: post success');
        return response('success', 200);
    }
}
