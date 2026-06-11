<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function dashboard(Request $request)
    {
        $schoolClasses = SchoolClass::orderBy('name')->get();
        $selectedClassId = $request->input('school_class_id');

        $classStats = [];
        $studentStats = [];

        if ($selectedClassId) {
            $students = User::where('school_class_id', $selectedClassId)->where('rola', 'uczen')->get();

            foreach ($students as $student) {
                $avg = Grade::where('student_id', $student->id)
                    ->select(DB::raw('SUM(value * weight) / SUM(weight) as average'))
                    ->first()->average;

                $totalAttendance = Attendance::where('student_id', $student->id)->count();
                $presentAttendance = Attendance::where('student_id', $student->id)->whereIn('status', ['Ob', 'Sp'])->count();
                
                $attendancePercentage = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100, 2) : 100;

                $studentStats[] = [
                    'name' => "{$student->nazwisko} {$student->imie}",
                    'average' => $avg ? round($avg, 2) : '-',
                    'attendance' => $attendancePercentage
                ];
            }
        } else {
            foreach ($schoolClasses as $class) {
                $studentIds = User::where('school_class_id', $class->id)->where('rola', 'uczen')->pluck('id');

                $avg = Grade::whereIn('student_id', $studentIds)
                    ->select(DB::raw('SUM(value * weight) / SUM(weight) as average'))
                    ->first()->average;

                $totalAttendance = Attendance::whereIn('student_id', $studentIds)->count();
                $presentAttendance = Attendance::whereIn('student_id', $studentIds)->whereIn('status', ['Ob', 'Sp'])->count();
                
                $attendancePercentage = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100, 2) : 100;

                $classStats[] = [
                    'id' => $class->id,
                    'name' => $class->name,
                    'average' => $avg ? round($avg, 2) : '-',
                    'attendance' => $attendancePercentage
                ];
            }
        }

        return view('analytics.dashboard', compact('schoolClasses', 'selectedClassId', 'classStats', 'studentStats'));
    }

    public function logs(Request $request)
    {
        if (auth()->user()->rola !== 'admin') {
            abort(403);
        }

        $search = $request->input('search');

        $logs = SystemLog::with('user')
            ->when($search, function($query) use ($search) {
                $query->where('action', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q) use ($search) {
                          $q->where('nazwisko', 'like', "%{$search}%");
                      });
            })
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('analytics.logs', compact('logs', 'search'));
    }
}