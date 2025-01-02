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
        /* 
        Get all data required by homepage
        If this function used all remain function is not used
        */
        
        $feedLength = 14;
        $featuredLength = 6;

        $feed = Post::latest()->skip(1)->take($feedLength)->get();
        $latest = Post::latest()->first();
        $featured = Post::orderBy('created_at', 'desc')->limit($featuredLength)->get();
        $tag_technology = Post::latest()->where('tag', 'TECHNOLOGY')->take(3)->get();
        $tag_electronic = Post::latest()->where('tag','ELECTRONICS')->take(3)->get();
        $counter = Post::count();

        if(!$feed && !$latest && !$featured){
            return $this->sendResponse(400, "Data retrival failed", null);
        }
        $wrapper = [
            'latest' => $latest,
            'featured' => $featured,
            'feed' => $feed,
            'postcount' => $counter,
            'tag_technology' => $tag_technology,
            'tag_electronic' => $tag_electronic
        ];

        return $this->sendResponse(200, "Data retrival success", $wrapper);
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
    public function paginate($start){
        $post = Post::orderBy('id', 'asc')
                ->skip($start - 1)
                ->take(10)->get();
        
        return $this->sendResponse(200, "Data retrival success", $post);
    }
    public function count(){
        $counter = Post::count();
        return $this->sendResponse(200,"Data retrival success", $counter);
    }
    public function getById($id){
        $data = Post::where('id', $id)->get();
        return $this->sendResponse(200,"Data retrival success", $data);
    }
    public function deleteById($id){
        $query = Post::where('id', $id)->delete();
        return $this->sendResponse(200, "Data deletion success",'');
    }
}
