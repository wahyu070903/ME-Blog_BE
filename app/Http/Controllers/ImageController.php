<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{   
    public function imageUpload(Request $request){
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $original_name = $request->file('image')->getClientOriginalName();
        $path = 'images/' . $original_name;

        if (Storage::disk('public')->exists($path)) {
            return response()->json([
                'message' => 'A file with the same name already exists.',
            ], 422);
        }

        $request->file('image')->storeAs('images', $original_name, 'public');
        $url = url('storage/' . $path);

        return response()->json([
            'url' => $url
        ]);

    }
    public function imageDelete(Request $request){
        $imagePath = $request->input('imagePath');

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
