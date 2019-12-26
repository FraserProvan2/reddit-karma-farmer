<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\RepostProcess;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Load UI
     * 
     */
    public function loadUI() 
    {
        return view('home');
    }

    /**
     * Run repost bot
     * 
     */
    public function runBot()
    {
        $bot = new RepostProcess();
        return $bot->run();
    }
}
