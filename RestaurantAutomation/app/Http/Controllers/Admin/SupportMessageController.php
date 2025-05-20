<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportMessage;

class SupportMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportMessage::query();
        if ($request->filled('fullname')) {
            $query->where('fullname', 'like', '%' . $request->fullname . '%');
        }
        if ($request->filled('subject')) {
            $query->where('subject', $request->subject);
        }
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }
        $messages = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->all());
        $subjects = ['Şikayet', 'Talep', 'Bilgi', 'Diğer'];
        return view('Dashboard.support_messages', compact('messages', 'subjects'));
    }

    public function show($id)
    {
        $message = SupportMessage::findOrFail($id);
        return view('Dashboard.support_message_detail', compact('message'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => 'required|string',
        ]);
        $message = SupportMessage::findOrFail($id);
        $message->admin_reply = $request->admin_reply;
        $message->save();

        // Bildirim oluştur (kullanıcıya)
        if ($message->user_id) {
            $customer = \App\Models\Customer::where('user_id', $message->user_id)->first();
            if ($customer) {
                \App\Models\Notification::create([
                    'customer_id' => $customer->id,
                    'type' => 'genel',
                    'message' => 'Destek talebinize cevap verildi: "' . mb_strimwidth($request->admin_reply, 0, 100, '...') . '"',
                    'status' => 'okunmadı',
                ]);
            }
        }

        return redirect()->route('admin.support-messages.show', $id)->with('success', 'Cevabınız kaydedildi.');
    }
} 