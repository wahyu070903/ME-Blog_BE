<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    public function sendVerification(Request $request){
    
        return response()->json([
            "message" => $request->user(),
        ], 200);
    }
    public function verificationHandler(EmailVerificationRequest $request){
        $request->fullfill();
    }
}
