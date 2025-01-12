<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Services\ImageService;

class EditorController extends Controller
{   
    protected $imageService;

    public function __construct(ImageService $imageService){
        $this->imageService = $imageService;
    }
    public function imageUpload(Request $request){
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        return $this->imageService->imageUpload($request->file('image'), 'images/');
    }
    public function imageDelete(Request $request){
        $imagePath = $request->input('imagePath');

        return $this->imageService->imageDelete($imagePath);
    }
}
