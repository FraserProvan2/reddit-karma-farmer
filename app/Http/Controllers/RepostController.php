<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RedditAPI;

class RepostController extends Controller
{
    public function run()
    {
        // create Reddit API client
        $reddit_api = new RedditAPI;

        // find a sub reddit
        $sub_reddits = $reddit_api->get('/subreddits/mine/subscriber');
        $selected_subreddit = $sub_reddits->data->children[rand(0, 4)]->data->display_name;
        dump('/r/' . $selected_subreddit);

        // find a post
        $endpoint = '/r/' . $selected_subreddit . '/search';
        $query_string = http_build_query([
            'q' => $selected_subreddit, // query
            't' => 'year', // time
        ]);
        $request_url = $endpoint . '?' . $query_string;
        $results = $reddit_api->get($request_url);

        // look for link flair
        $selected_post = null;
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
            dump('failed to find link...');
            $this->run(); // rerun if no link found
        }
        dump('https://reddit.com' . $selected_post->permalink);

        // repost 
        $cloned_post = [
            'title' => $selected_post->title,
            'sr' => 'test', //$selected_post->subreddit,
            'url' => $selected_post->url,
            'kind' => 'link',
            // 'uh' => 'f0f0f0f0', 
        ];
        dump($cloned_post);
        $result = $reddit_api->post($cloned_post);
        
        if (!$result->success) {
            dump('failed to post... Here we go again');
            // $this->run(); // failure is NOT an option
        }

        dd($result->success);
    }
}
