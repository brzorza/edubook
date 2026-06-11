<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->input('search');

        $receivedMessages = Message::where('recipient_id', $userId)
            ->with('sender')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('sender', function ($q) use ($search) {
                    $q->where('nazwisko', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->get();

        $sentMessages = Message::where('sender_id', $userId)
            ->with('recipient')
            ->orderByDesc('created_at')
            ->get();

        $schoolClasses = SchoolClass::orderBy('name')->get();

        return view('communication.messages.index', compact('receivedMessages', 'sentMessages', 'schoolClasses', 'search'));
    }

    public function searchUsers(Request $request)
    {
        $search = $request->input('q');
        if (!$search) {
            return response()->json([]);
        }

        $users = User::where('id', '!=', Auth::id())
            ->where(function ($query) use ($search) {
                $query->where('nazwisko', 'like', "%{$search}%")
                      ->orWhere('imie', 'like', "%{$search}%");
            })
            ->select('id', 'imie', 'nazwisko', 'rola')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $userId = Auth::id();

        if ($request->filled('school_class_id')) {
            $validated = $request->validate([
                'school_class_id' => 'required|exists:school_classes,id',
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            $students = User::where('school_class_id', $validated['school_class_id'])->get();

            foreach ($students as $student) {
                Message::create([
                    'sender_id' => $userId,
                    'recipient_id' => $student->id,
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                ]);
            }

            return redirect()->back()->with('success', 'Wiadomość została wysłana masowo do klasy.');
        }

        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Message::create([
            'sender_id' => $userId,
            'recipient_id' => $validated['recipient_id'],
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return redirect()->back()->with('success', 'Wiadomość została wysłana.');
    }

    public function markAsRead(Message $message)
    {
        if ($message->recipient_id === Auth::id() && !$message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return response()->json(['status' => 'success']);
    }
}