<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\SupportMessage;

class SupportController extends Controller
{
    public function index()
    {
        return view('support');
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:100',
            'phone' => 'nullable|required_without:email|string|max:20',
            'email' => 'nullable|required_without:phone|email|max:100',
            'subject' => 'required|string|max:50',
            'message' => 'required|string|max:1000',
        ]);

        // Veritabanına kaydet
        $support = SupportMessage::create([
            'fullname' => $validated['fullname'],
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'user_id' => auth()->check() ? auth()->id() : null,
        ]);

        // E-posta gönderimini kaldırdım

        return back()->with('success', 'Mesajınız başarıyla iletildi!');
    }
} 