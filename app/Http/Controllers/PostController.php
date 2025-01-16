<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

use App\Services\ImageService;

class PostController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService){
        $this->imageService = $imageService;
    }

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
        $operation = Post::where('id', $id)->delete();
        if ($operation) {
            return $this->sendResponse(200, 'Record deleted', '');
        }
        return $this->sendResponse(400, 'Record not found or delete failed', '');
    }

    public function getNextandPrev($current){
        $next = Post::where('id', '>', $current)
                ->orderBy('id', 'asc')
                ->first();

        $prev = Post::where('id', '<', $current)
                ->orderBy('id', 'desc')
                ->first();

        $data = [
            "next" => $next,
            "prev" => $prev
        ];

        return $this->sendResponse(200, 'Record retrive success', $data);
    }

    public function editPost($id){
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'tag' => 'required',
            'thumbnail' => 'required',
            'rtime' => 'required'
        ]);

        $post->update($validatedData);

        return $this->sendResponse(200,"Data update Success", '');
    }

    public function createPost(Request $request){
        $post_title = $request->input('title');
        $post_rtime = $request->input('rtime');
        $post_tag = $request->input('tag');
        $post_description = $request->input('description');
        $post_thumbnail = $request->file('thumbnail');
        $content = $request->input('content');

        $thumbnail_name = $post_thumbnail->getClientOriginalName();
        $image_store_status = '';
        if($request->hasFile('thumbnail')){
            $image_store_status = $this->imageService->imageUpload($post_thumbnail, 'thumbnail/');
        }

        $record = Post::create([
            'type' => 'normal',
            'title' => $post_title,
            'description' => $post_description,
            'tag' => $post_tag,
            'rtime' => $post_rtime,
            'content' => $content,
            'thumbnail' => $thumbnail_name,
        ]);

        return response()->json([
            'message' => $image_store_status
        ]);
    }
}
