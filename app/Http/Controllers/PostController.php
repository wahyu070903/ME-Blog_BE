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

    public function editPost(Request $request, $id){
        $post = Post::find($id);
        
        if(!$post) {
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }

        $validator = $request->validate([
            'title' => 'required|max:500',
            'description' => 'required|max:1000',
            'tag' => 'required|max:50',
            'rtime' => 'required|max:6',
            'content' => 'required',
            'thumbnail' => 'nullable|file|image|mimes:jpg,jpeg,png,gif|max:5120',  // 5Mb
        ]);

        $post_title = $request->input('title');
        $post_rtime = $request->input('rtime');
        $post_tag = $request->input('tag');
        $post_description = $request->input('description');
        $post_thumbnail = $request->file('thumbnail');
        $content = $request->input('content');

        $thumbnail_name = "";

        if($request->hasFile("thumbnail")){
            $thumbnail_name = $post_thumbnail->getClientOriginalName();
            $old_thumbnail_name = Post::where("id", $id)->value("thumbnail");
            
            $delete_status = $this->imageService->imageDelete("thumbnail/" . $old_thumbnail_name);
            if(!$delete_status["success"]){
                return response()->json([
                    "message" => $delete_status["message"],
                ], 422);
            }

            $upload_status = $this->imageService->imageUpload($post_thumbnail, "thumbnail/");
            if(!$upload_status["success"]){
                return response()->json([
                    "message" => $upload_status["message"],
                ], 422);
            }
        }else{
            $thumbnail_name = $post->value("thumbnail");
        }
        
        $post->update([
            "title" => $post_title,
            "description" => $post_description,
            "tag" => $post_tag,
            "rtime" => $post_rtime,
            "content" => $content,
            "thumbnail" => $thumbnail_name,
        ]);

        return response()->json([
            "message" => "Post updated successfully"
        ],200);
    }

    public function createPost(Request $request){
        $validator = $request->validate([
            'title' => 'required|max:500',
            'description' => 'required|max:1000',
            'tag' => 'required|max:50',
            'rtime' => 'required|max:6',
            'content' => 'required',
            'thumbnail' => 'required|file|image|mimes:jpg,jpeg,png,gif|max:5120',  // 5Mb
        ]);

        $post_title = $request->input('title');
        $post_rtime = $request->input('rtime');
        $post_tag = $request->input('tag');
        $post_description = $request->input('description');
        $post_thumbnail = $request->file('thumbnail');
        $content = $request->input('content');

        $thumbnail_name = $post_thumbnail->getClientOriginalName();

        if($request->hasFile('thumbnail')){
            $image_store_status = $this->imageService->imageUpload($post_thumbnail, 'thumbnail/');
            if(!$image_store_status["success"]){
                return response()->json([
                    "message" => $image_store_status["message"],
                ],422);
            }
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
            'message' => "New Post created",
        ],200);
    }
}
