<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageService
{   
    public function imageUpload($image, $location){

        $original_name = $image->getClientOriginalName();
        $path = $location . $original_name;

        if (Storage::disk('public')->exists($path)) {
            return response()->json([
                'message' => 'A file with the same name already exists.',
            ], 422);
        }

        $image->storeAs('images', $original_name, 'public');
        $url = url('storage/' . $path);

        return response()->json([
            'url' => $url
        ]);

    }
    public function imageDelete($path){
        $imagePath = $rpath;

        if (!$imagePath) {
            return response()->json(['message' => 'Image path is required'], 400);
        }

        $relativePath = str_replace(url('storage') . '/', '', $imagePath);

        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
            return response()->json(['message' => 'Image deleted successfully']);
        }
        return response()->json(['message' => 'Image not found'], 404);
    }
}
