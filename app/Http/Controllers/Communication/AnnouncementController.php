<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (in_array($user->rola, ['admin', 'dyrekcja'])) {
            $announcements = Announcement::with('schoolClasses')
                ->orderByDesc('created_at')
                ->get();
        } else {
            $announcements = Announcement::where('target_all', true)
                ->orWhereHas('schoolClasses', function ($query) use ($user) {
                    $query->where('school_classes.id', $user->school_class_id);
                })
                ->orderByDesc('created_at')
                ->get();
        }

        $schoolClasses = SchoolClass::orderBy('name')->get();

        return view('communication.announcements.index', compact('announcements', 'schoolClasses'));
    }

    public function store(Request $request)
    {
        if (!in_array(Auth::user()->rola, ['admin', 'dyrekcja'])) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target' => 'required|string',
            'school_classes' => 'nullable|array',
            'school_classes.*' => 'exists:school_classes,id',
        ]);

        $targetAll = $validated['target'] === 'all';

        $announcement = Announcement::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'target_all' => $targetAll,
        ]);

        if (!$targetAll && isset($validated['school_classes'])) {
            $announcement->schoolClasses()->sync($validated['school_classes']);
        }

        return redirect()->back()->with('success', 'Ogłoszenie zostało opublikowane.');
    }

    public function destroy(Announcement $announcement)
    {
        if (!in_array(Auth::user()->rola, ['admin', 'dyrekcja'])) {
            abort(403);
        }

        $announcement->delete();
        return redirect()->back()->with('success', 'Ogłoszenie zostało usunięte.');
    }
}