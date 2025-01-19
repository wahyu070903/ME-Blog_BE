<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Exception;

class ImageService
{   
    public function imageUpload($image, $location){
        try{
            $original_name = $image->getClientOriginalName();
            $path = $location . $original_name;

            if (Storage::disk('public')->exists($path)) {
                return [
                    "success" => false,
                    "message" => "A file with the same name already exists."
                ];
            }

            $image->storeAs($location, $original_name, 'public');
            $url = url('storage/' . $path);

            return [
                "success" => true,
                "url" => $url,
            ];

        }catch(Exception $e){
            return [
                "success" => false,
                "message" => e->getMessage(),
            ];
        }
    }
    public function imageDelete($path){
        try{
            $imagePath = $path;

            if (!$imagePath) {
                return [
                    "success" => false,
                    "message" => "Image path is required",
                ];
            }

            $relativePath = str_replace(url('storage') . '/', '', $imagePath);

            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
                return [
                    "success" => true,
                    "message" => "Image deleted successfully",
                ];
            }

            return [
                "success" => false,
                "message" => "Image not found",
            ];

        }catch(Exception $e){
            return [
                "success" => false,
                "message" => e->getMessage(),
            ];
        }
    }
}
