<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function sendResponse($code, $message, $data){
        return response()->json([
            'status'    => $code,
            'message'   => $message,
            'data'      => $data
        ]);
    }
    public function getHome(){
        $size = 14;
        $range = Post::latest()->skip(1)->take($size);
        $data = $range->get();

        if(!$data){
            return $this->sendResponse(400, "Data retrival failed", null);
        }
        return $this->sendResponse(200, "Data retrival success", $data);
    }
    public function getLatest(){
        $size = 6;
        $latest = Post::orderBy('created_at', 'desc')->limit($size)->get();
        if(!$latest){
            return $this->sendResponse(400, "Data retrival failed", null);
        }
        return $this->sendResponse(200, "Data retrival success", $latest);
    }
    public function getRecent(){
        $recent = Post::latest()->first();

        if(!$recent){
            return $this->sendResponse(400, "Data retrival failed", null);
        }
        return $this->sendResponse(200, "Data retrival success", $recent);
    }
    public function getByTag($tag){
        $size = 3;
        $tag_code = [
            100 => 'TECHNOLOGY',
            101 => 'ELECTRINICS',
            102 => 'MECHANICS',
            103 => 'COMPUTER ENG'
        ];

        if(array_key_exists($tag, $tag_code)){
            $latestbyTag = Post::where('tag', $tag_code[$tag])->latest();
            $data = $latestbyTag->take($size)->get();
        }else{
            return $this->sendResponse(400, "Data retrival failed", null);
        }
        
        return $this->sendResponse(200, "Data retrival success", $data);
    }

}
