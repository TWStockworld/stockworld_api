<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Mail;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendmail()
    {
        Mail::raw('測試郵件', function ($message) {
            $message->from('root@bakerychu.com', 'root');
            $message->to('likeyou600@gmail.com', 'bakery')->subject('網域發信');
        });
    }
}
