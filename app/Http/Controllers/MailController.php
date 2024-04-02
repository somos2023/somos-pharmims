<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SimpleMail;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendSampleMail()
    {
        Mail::to('cm.jobet@gmail.com')->send(new SimpleMail('Sample MAIL'));
        return response()->json(['message' => 'success']);
    }
}
