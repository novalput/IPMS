<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewProjectController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\IssuesController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/register/success', [RegisterController::class, 'registerSuccess'])->name('register.success');

// Protected routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // New Project
    Route::get('/new_project', [NewProjectController::class, 'index'])->name('new_project.index');
    
    // Direct access ke halaman tanpa project (tampilkan list project)
    Route::get('/description', [ProjectController::class, 'deskripsiIndex'])->name('description');
    Route::get('/progress', [ProjectController::class, 'progressIndex'])->name('progress');
    Route::get('/activities', [ProjectController::class, 'aktivitasIndex'])->name('activities');
    Route::get('/issues', [ProjectController::class, 'isuIndex'])->name('issues');
    Route::get('/documentation', [ProjectController::class, 'dokumentasiIndex'])->name('documentation');

    // Export is available to administrators and approved project users.
    Route::get('/export', [ExportController::class, 'index'])->name('export')->middleware('role:admin,user');
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
        Route::get('/password', [ProfileController::class, 'editPassword'])->name('password');
        Route::post('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    
    // Search
    Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
    
    // Period
    Route::prefix('period')->name('period.')->group(function () {
        Route::delete('/{period}/destroy', [PeriodController::class, 'destroy'])->name('destroy');
        Route::post('/{period}/cancel-submit', [PeriodController::class, 'cancelSubmit'])->name('cancel-submit');
        Route::get('/{period}/progress-data', [ProjectController::class, 'getProgressData'])->name('progress-data');
    });
    
    //Completed Periods Routes
    Route::get('/completed-periods', [App\Http\Controllers\CompletedPeriodController::class, 'index'])->name('completed-periods.index');
    Route::post('/completed-periods/{period}/reactivate', [App\Http\Controllers\CompletedPeriodController::class, 'reactivate'])->name('completed-periods.reactivate');
    

    // Project
    Route::prefix('project')->name('project.')->group(function () {
        // PAST PROJECT
        Route::get('/create-past', [ProjectController::class, 'createPast'])->name('create-past');
        Route::post('/store-past', [ProjectController::class, 'storePast'])->name('store-past');
        
        // CORE
        Route::get('/create', [ProjectController::class, 'create'])->name('create');
        Route::post('/store', [ProjectController::class, 'store'])->name('store');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
        Route::get('/{project}/edit/{period?}', [ProjectController::class, 'edit'])->name('edit');
        Route::put('/{project}/update/{period?}', [ProjectController::class, 'update'])->name('update');
        Route::post('/{project}/approval', [ProjectController::class, 'approval'])->name('approval');
        Route::post('/{project}/approve', [ProjectController::class, 'approve'])->name('approve');
        Route::post('/{project}/reject', [ProjectController::class, 'reject'])->name('reject');

        // PROJECT VIEW PAGES
        Route::get('/{project}/description', [ProjectController::class, 'description'])->name('description');
        Route::get('/{project}/progress', [ProjectController::class, 'progress'])->name('progress');
        Route::get('/{project}/activities', [ProjectController::class, 'activities'])->name('activities');
        Route::get('/{project}/issues', [ProjectController::class, 'issues'])->name('issues');
        Route::get('/{project}/documentation', [ProjectController::class, 'documentation'])->name('documentation');

        // DOCUMENTATION (MODEL BINDING)
        Route::prefix('{project}/documentation')->name('documentation.')->group(function () {
            Route::get('/photos', [DocumentationController::class, 'getPhotos'])->name('photos');
            Route::get('/{documentation}/photo', [DocumentationController::class, 'getPhoto'])->name('photo');
            Route::get('/{documentation}/download', [DocumentationController::class, 'download'])->name('download');
            Route::post('/upload', [DocumentationController::class, 'upload'])->name('upload');
            Route::post('/{documentation}/replace', [DocumentationController::class, 'replacePhoto'])->name('replace');
            Route::post('/{documentation}/update', [DocumentationController::class, 'update'])->name('update');
            Route::post('/{documentation}/delete', [DocumentationController::class, 'delete'])->name('delete');
        });

        // DOCUMENTATION ALT (NO MODEL BINDING)
        Route::prefix('{projectId}/documentation/{documentationId}')->group(function () {
            Route::get('/photo-alt', [DocumentationController::class, 'getPhotoAlternative'])->name('documentation.photo.alternative');
            Route::get('/download-alt', [DocumentationController::class, 'downloadAlternative'])->name('documentation.download.alt');
            Route::post('/replace-alt', [DocumentationController::class, 'replacePhotoAlternative'])->name('documentation.replace.alt');
            Route::post('/update-alt', [DocumentationController::class, 'updateAlternative'])->name('documentation.update.alt');
            Route::post('/delete-alt', [DocumentationController::class, 'deleteAlternative'])->name('documentation.delete.alt');
        });
    });

    // Tracking
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking');

    // Export routes
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/', [ExportController::class, 'index'])->name('index');
        Route::post('/download', [ExportController::class, 'export'])->name('download');
        Route::get('/periods', [ExportController::class, 'getPeriods'])->name('periods');
        Route::post('/preview', [ExportController::class, 'preview'])->name('preview');
    });

    
    // User management routes
    Route::group(['middleware' => function ($request, $next) {
            if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
                abort(403, 'Akses ditolak.');
            }
            return $next($request);
        }], function () {
            Route::get('/user-management', [UserManagementController::class, 'index'])->name('user.management');
            Route::get('/user-management/create', [UserManagementController::class, 'create'])->name('user.management.create');
            Route::post('/user-management', [UserManagementController::class, 'store'])->name('user.management.store');
            Route::get('/user-management/{user}/edit', [UserManagementController::class, 'edit'])->name('user.management.edit');
            Route::put('/user-management/{user}', [UserManagementController::class, 'update'])->name('user.management.update');
            Route::delete('/user-management/{user}', [UserManagementController::class, 'destroy'])->name('user.management.destroy');
            Route::post('/user-management/{user}/approve', [UserManagementController::class, 'approve'])->name('user.management.approve');
            Route::post('/user-management/{user}/reject', [UserManagementController::class, 'reject'])->name('user.management.reject');
        });
});

// Dropdown data (AJAX)
Route::get('/dropdown/projects', [App\Http\Controllers\ProjectController::class, 'dropdownProjects'])->name('dropdown.projects');
Route::get('/dropdown/periods/{project_id}', [App\Http\Controllers\PeriodController::class, 'dropdownPeriods'])->name('dropdown.periods');

// Copy template (AJAX)
Route::get('/project/copy-template/{project_id}', [App\Http\Controllers\ProjectController::class, 'copyTemplate'])->name('project.copyTemplate');
Route::get('/period/copy-template/{period_id}', [App\Http\Controllers\PeriodController::class, 'copyTemplate'])->name('period.copyTemplate');
