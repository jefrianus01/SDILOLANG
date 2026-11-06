<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicGalleryController;
use App\Http\Controllers\PublicNewsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherAttendanceAdminController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Website publik
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/profil-sekolah', [HomeController::class, 'profile'])->name('school.profile');
Route::get('/berita', [PublicNewsController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [PublicNewsController::class, 'show'])->name('news.show');
Route::get('/galeri', [PublicGalleryController::class, 'index'])->name('gallery.index');

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Area terautentikasi
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'password.changed'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');

    /*
    | Absensi guru (mandiri)
    */
    Route::get('/absensi-saya', [TeacherAttendanceController::class, 'index'])->name('teacher-attendance.index');
    Route::post('/absensi-saya/masuk', [TeacherAttendanceController::class, 'checkIn'])->name('teacher-attendance.check-in');
    Route::post('/absensi-saya/pulang', [TeacherAttendanceController::class, 'checkOut'])->name('teacher-attendance.check-out');
    Route::post('/absensi-saya/izin', [TeacherAttendanceController::class, 'requestPermission'])->name('teacher-attendance.permission');

    /*
    | Data guru (Kepala Sekolah / Super Admin)
    */
    Route::prefix('guru')->name('teachers.')->group(function () {
        Route::get('/', [TeacherController::class, 'index'])->middleware('can:viewAny,App\Models\Teacher')->name('index');
        Route::get('/create', [TeacherController::class, 'create'])->middleware('can:create,App\Models\Teacher')->name('create');
        Route::post('/', [TeacherController::class, 'store'])->middleware('can:create,App\Models\Teacher')->name('store');
        Route::get('/import', [TeacherController::class, 'importForm'])->middleware('can:import,App\Models\Teacher')->name('import.form');
        Route::post('/import', [TeacherController::class, 'import'])->middleware('can:import,App\Models\Teacher')->name('import');
        Route::get('/template', [TeacherController::class, 'template'])->middleware('can:import,App\Models\Teacher')->name('template');
        Route::get('/export/excel', [TeacherController::class, 'exportExcel'])->middleware('can:viewAny,App\Models\Teacher')->name('export.excel');
        Route::get('/export/pdf', [TeacherController::class, 'exportPdf'])->middleware('can:viewAny,App\Models\Teacher')->name('export.pdf');
        Route::get('/{teacher}/edit', [TeacherController::class, 'edit'])->middleware('can:update,teacher')->name('edit');
        Route::put('/{teacher}', [TeacherController::class, 'update'])->middleware('can:update,teacher')->name('update');
        Route::delete('/{teacher}', [TeacherController::class, 'destroy'])->middleware('can:delete,teacher')->name('destroy');
        Route::post('/{teacher}/reset-password', [TeacherController::class, 'resetPassword'])->middleware('can:resetPassword,teacher')->name('reset-password');
    });

    /*
    | Master data
    */
    Route::get('/master/jenis-guru', [TeacherTypeController::class, 'index'])->name('teacher-types.index');
    Route::post('/master/jenis-guru', [TeacherTypeController::class, 'store'])->name('teacher-types.store');
    Route::put('/master/jenis-guru/{teacherType}', [TeacherTypeController::class, 'update'])->name('teacher-types.update');
    Route::delete('/master/jenis-guru/{teacherType}', [TeacherTypeController::class, 'destroy'])->name('teacher-types.destroy');

    Route::get('/tahun-ajaran', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::post('/tahun-ajaran', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::put('/tahun-ajaran/{academicYear}', [AcademicYearController::class, 'update'])->name('academic-years.update');
    Route::post('/tahun-ajaran/{academicYear}/aktifkan', [AcademicYearController::class, 'activate'])->name('academic-years.activate');
    Route::delete('/tahun-ajaran/{academicYear}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');

    Route::get('/kelas', [ClassroomController::class, 'index'])->name('classrooms.index');
    Route::get('/kelas/{classroom}/edit', [ClassroomController::class, 'edit'])->middleware('can:assignHomeroom,App\Models\Classroom')->name('classrooms.edit');
    Route::put('/kelas/{classroom}', [ClassroomController::class, 'update'])->middleware('can:assignHomeroom,App\Models\Classroom')->name('classrooms.update');

    /*
    | Data siswa
    */
    Route::prefix('siswa')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->middleware('can:viewAny,App\Models\Student')->name('index');
        Route::get('/create', [StudentController::class, 'create'])->middleware('can:create,App\Models\Student')->name('create');
        Route::post('/', [StudentController::class, 'store'])->middleware('can:create,App\Models\Student')->name('store');
        Route::get('/import', [StudentController::class, 'importForm'])->middleware('can:import,App\Models\Student')->name('import.form');
        Route::post('/import', [StudentController::class, 'import'])->middleware('can:import,App\Models\Student')->name('import');
        Route::get('/template', [StudentController::class, 'template'])->middleware('can:import,App\Models\Student')->name('template');
        Route::get('/export/excel', [StudentController::class, 'exportExcel'])->middleware('can:viewAny,App\Models\Student')->name('export.excel');
        Route::get('/export/pdf', [StudentController::class, 'exportPdf'])->middleware('can:viewAny,App\Models\Student')->name('export.pdf');
        Route::get('/promote', [StudentController::class, 'promoteForm'])->middleware('can:promote,App\Models\Student')->name('promote.form');
        Route::post('/promote', [StudentController::class, 'promote'])->middleware('can:promote,App\Models\Student')->name('promote');
        Route::get('/{student}/edit', [StudentController::class, 'edit'])->middleware('can:update,student')->name('edit');
        Route::put('/{student}', [StudentController::class, 'update'])->middleware('can:update,student')->name('update');
        Route::delete('/{student}', [StudentController::class, 'destroy'])->middleware('can:delete,student')->name('destroy');
    });

    /*
    | Absensi siswa
    */
    Route::get('/absensi-siswa', [StudentAttendanceController::class, 'index'])->name('student-attendance.index');
    Route::get('/absensi-siswa/kelas/{classroom}', [StudentAttendanceController::class, 'show'])->name('student-attendance.show');
    Route::post('/absensi-siswa/kelas/{classroom}', [StudentAttendanceController::class, 'store'])->name('student-attendance.store');

    /*
    | Absensi guru (rekap & persetujuan)
    */
    Route::get('/absensi-guru', [TeacherAttendanceAdminController::class, 'index'])->middleware('can:viewAny,App\Models\TeacherAttendance')->name('teacher-attendance.admin');
    Route::get('/absensi-guru/persetujuan', [TeacherAttendanceAdminController::class, 'approvals'])->middleware('can:approve,App\Models\TeacherAttendance')->name('teacher-attendance.approvals');
    Route::post('/absensi-guru/{attendance}/setujui', [TeacherAttendanceAdminController::class, 'approve'])->middleware('can:approve,App\Models\TeacherAttendance')->name('teacher-attendance.approve');
    Route::post('/absensi-guru/{attendance}/tolak', [TeacherAttendanceAdminController::class, 'reject'])->middleware('can:approve,App\Models\TeacherAttendance')->name('teacher-attendance.reject');

    /*
    | Rekap
    */
    Route::get('/rekap/guru', [ReportController::class, 'teacherRecap'])->middleware('can:viewAny,App\Models\TeacherAttendance')->name('reports.teacher');
    Route::get('/rekap/guru/pdf', [ReportController::class, 'teacherRecapPdf'])->middleware('can:viewAny,App\Models\TeacherAttendance')->name('reports.teacher.pdf');
    Route::get('/rekap/guru/excel', [ReportController::class, 'teacherRecapExcel'])->middleware('can:viewAny,App\Models\TeacherAttendance')->name('reports.teacher.excel');
    Route::get('/rekap/kelas/{classroom}', [ReportController::class, 'classRecap'])->middleware('can:view,classroom')->name('reports.class');
    Route::get('/rekap/kelas/{classroom}/pdf', [ReportController::class, 'classRecapPdf'])->middleware('can:view,classroom')->name('reports.class.pdf');
    Route::get('/rekap/kelas/{classroom}/excel', [ReportController::class, 'classRecapExcel'])->middleware('can:view,classroom')->name('reports.class.excel');

    /*
    | Pengaturan
    */
    Route::get('/pengaturan', [SettingController::class, 'edit'])->middleware('can:manage,App\Models\Setting')->name('settings.edit');
    Route::put('/pengaturan', [SettingController::class, 'update'])->middleware('can:manage,App\Models\Setting')->name('settings.update');

    /*
    | Konten website
    */
    Route::prefix('admin/berita')->name('admin.news.')->group(function () {
        Route::get('/', [AdminNewsController::class, 'index'])->middleware('can:viewAny,App\Models\News')->name('index');
        Route::get('/create', [AdminNewsController::class, 'create'])->middleware('can:create,App\Models\News')->name('create');
        Route::post('/', [AdminNewsController::class, 'store'])->middleware('can:create,App\Models\News')->name('store');
        Route::get('/{news}/edit', [AdminNewsController::class, 'edit'])->middleware('can:update,news')->name('edit');
        Route::put('/{news}', [AdminNewsController::class, 'update'])->middleware('can:update,news')->name('update');
        Route::delete('/{news}', [AdminNewsController::class, 'destroy'])->middleware('can:delete,news')->name('destroy');
    });

    Route::prefix('admin/galeri')->name('admin.gallery.')->group(function () {
        Route::get('/', [AdminGalleryController::class, 'index'])->middleware('can:viewAny,App\Models\Gallery')->name('index');
        Route::get('/create', [AdminGalleryController::class, 'create'])->middleware('can:create,App\Models\Gallery')->name('create');
        Route::post('/', [AdminGalleryController::class, 'store'])->middleware('can:create,App\Models\Gallery')->name('store');
        Route::get('/{gallery}/edit', [AdminGalleryController::class, 'edit'])->middleware('can:update,gallery')->name('edit');
        Route::put('/{gallery}', [AdminGalleryController::class, 'update'])->middleware('can:update,gallery')->name('update');
        Route::delete('/{gallery}', [AdminGalleryController::class, 'destroy'])->middleware('can:delete,gallery')->name('destroy');
    });

    /*
    | Super admin
    */
    Route::get('/pengguna', [UserController::class, 'index'])->middleware('can:viewAny,App\Models\User')->name('users.index');
    Route::post('/pengguna', [UserController::class, 'store'])->middleware('can:viewAny,App\Models\User')->name('users.store');
    Route::post('/pengguna/{user}/reset-password', [UserController::class, 'resetPassword'])->middleware('can:resetPassword,user')->name('users.reset-password');
    Route::post('/pengguna/{user}/toggle-active', [UserController::class, 'toggleActive'])->middleware('can:toggleActive,user')->name('users.toggle-active');

    Route::get('/log-aktivitas', [AuditLogController::class, 'index'])->middleware('can:viewAny,App\Models\AuditLog')->name('audit.index');
});
