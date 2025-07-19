<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\Contact;
use App\Rules\ReCaptcha;
use Illuminate\Http\Request;
use Mail;

class ContactController extends Controller
{
    public function sendMail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'message' => 'required|string',
            'recaptcha_token' => ['required', new ReCaptcha()],
        ], [
            'email.required' => 'El campo email es obligatorio.',
            'email.email' => 'El formato del email es inválido.',
            'name.required' => 'El campo nombre es obligatorio.',
            'message.required' => 'El campo mensaje es obligatorio.',
            'recaptcha_token.required' => 'La verificación de reCAPTCHA es obligatoria.',
        ]);

        $email = $request->input('email');
        $name = $request->input('name');
        $msg = $request->input('message');

        Mail::to($email)->send(new Contact($email, $name, $msg));

        return response()->json([
            'message' => 'Gracias por tu mensaje, nos pondremos en contacto contigo pronto.'
        ], 200);
    }
}
