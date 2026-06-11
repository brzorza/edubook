<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Admin\SchoolStructureController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Teacher\LessonJournalController;
use App\Http\Controllers\Teacher\GradeController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Communication\MessageController;
use App\Http\Controllers\Communication\AnnouncementController;
use App\Http\Controllers\Analytics\AnalyticsController;

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::get('/change-password', [PasswordController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [PasswordController::class, 'changePassword'])->name('password.change.post');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    Route::get('/messages/search-users', [MessageController::class, 'searchUsers'])->name('messages.searchUsers');

    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [SchoolStructureController::class, 'index'])->name('admin.dashboard');
        Route::post('/admin/classes', [SchoolStructureController::class, 'storeClass'])->name('admin.classes.store');
        Route::post('/admin/subjects', [SchoolStructureController::class, 'storeSubject'])->name('admin.subjects.store');
        Route::post('/admin/teachers/assign', [SchoolStructureController::class, 'assignTeacher'])->name('admin.teachers.assign');
        Route::post('/admin/students/assign', [SchoolStructureController::class, 'assignStudent'])->name('admin.students.assign');
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::put('/admin/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::get('/analytics/logs', [AnalyticsController::class, 'logs'])->name('analytics.logs');
    });

    Route::middleware(['role:admin,nauczyciel,dyrekcja'])->group(function () {
        Route::get('/analytics', [AnalyticsController::class, 'dashboard'])->name('analytics.index');
    });

    Route::middleware(['role:nauczyciel'])->group(function () {
        Route::get('/teacher/journal', [LessonJournalController::class, 'index'])->name('teacher.journal.index');
        Route::get('/teacher/journal/create', [LessonJournalController::class, 'createLesson'])->name('teacher.journal.create');
        Route::post('/teacher/journal/store', [LessonJournalController::class, 'storeLesson'])->name('teacher.journal.store');
        Route::get('/teacher/lessons/{lesson}/grades', [GradeController::class, 'showLessonGrades'])->name('teacher.grades.lesson');
        Route::post('/teacher/grades', [GradeController::class, 'store'])->name('teacher.grades.store');
        Route::put('/teacher/grades/{grade}', [GradeController::class, 'update'])->name('teacher.grades.update');
        Route::delete('/teacher/grades/{grade}', [GradeController::class, 'destroy'])->name('teacher.grades.destroy');
    });

    Route::middleware(['auth', 'role:uczen,rodzic'])->group(function () {
        Route::get('/student/grades', [StudentDashboardController::class, 'grades'])->name('student.grades');
        Route::get('/student/attendance', [StudentDashboardController::class, 'attendance'])->name('student.attendance');
        Route::get('/student/timetable', [StudentDashboardController::class, 'timetable'])->name('student.timetable');
    });
});

Route::get('/', function () {
    return redirect()->route('login');
});