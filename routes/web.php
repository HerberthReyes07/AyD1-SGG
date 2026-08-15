<?php

use App\Http\Controllers\ClassAttendanceReportController;
use App\Http\Controllers\ClassEnrollmentController;
use App\Http\Controllers\ClassRatingController;
use App\Http\Controllers\ClassSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\GroupClassController;
use App\Http\Controllers\GroupClassReportController;
use App\Http\Controllers\GroupClassScheduleController;
use App\Http\Controllers\GuestPassController;
use App\Http\Controllers\MemberCalorieGoalController;
use App\Http\Controllers\MemberClassController;
use App\Http\Controllers\MemberMealController;
use App\Http\Controllers\MemberNutritionHistoryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberMembershipController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Trainer\AssignmentController;
use App\Http\Controllers\Trainer\CalorieGoalController as TrainerCalorieGoalController;
use App\Http\Controllers\Trainer\MeasurementController;
use App\Http\Controllers\Trainer\NutritionalObservationController;
use App\Http\Controllers\TrainerAssignmentController;
use App\Http\Controllers\TrainerClassController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated user
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Administrator
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Food catalog
    |--------------------------------------------------------------------------
    */

    Route::get('/foods', [FoodController::class, 'index'])
        ->name('foods.index');

    Route::get('/foods/create', [FoodController::class, 'create'])
        ->name('foods.create');

    Route::post('/foods', [FoodController::class, 'store'])
        ->name('foods.store');

    Route::get('/foods/{food}/edit', [FoodController::class, 'edit'])
        ->name('foods.edit');

    Route::put('/foods/{food}', [FoodController::class, 'update'])
        ->name('foods.update');

    Route::patch('/foods/{food}/toggle-status', [FoodController::class, 'toggleStatus'])
        ->name('foods.toggle-status');

    /*
    |--------------------------------------------------------------------------
    | Group class reports
    |--------------------------------------------------------------------------
    */

    Route::get('/group-classes/reports', [GroupClassReportController::class, 'index'])
        ->name('group-class-reports.index');

    Route::get(
        '/group-classes/attendance-report',
        [ClassAttendanceReportController::class, 'index']
    )->name('class-attendance-reports.index');

    /*
    |--------------------------------------------------------------------------
    | Group classes
    |--------------------------------------------------------------------------
    */

    Route::get('/group-classes', [GroupClassController::class, 'index'])
        ->name('group-classes.index');

    Route::get('/group-classes/create', [GroupClassController::class, 'create'])
        ->name('group-classes.create');

    Route::post('/group-classes', [GroupClassController::class, 'store'])
        ->name('group-classes.store');

    Route::get('/group-classes/{groupClass}/edit', [GroupClassController::class, 'edit'])
        ->name('group-classes.edit');

    Route::put('/group-classes/{groupClass}', [GroupClassController::class, 'update'])
        ->name('group-classes.update');

    Route::patch('/group-classes/{groupClass}/toggle-status', [GroupClassController::class, 'toggleStatus'])
        ->name('group-classes.toggle-status');

    /*
    |--------------------------------------------------------------------------
    | Group class schedules
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/group-classes/{groupClass}/schedules',
        [GroupClassScheduleController::class, 'index']
    )->name('group-class-schedules.index');

    Route::post(
        '/group-classes/{groupClass}/schedules',
        [GroupClassScheduleController::class, 'store']
    )->name('group-class-schedules.store');

    Route::put(
        '/group-classes/{groupClass}/schedules/{schedule}',
        [GroupClassScheduleController::class, 'update']
    )->name('group-class-schedules.update');

    Route::delete(
        '/group-classes/{groupClass}/schedules/{schedule}',
        [GroupClassScheduleController::class, 'destroy']
    )->name('group-class-schedules.destroy');

    /*
    |--------------------------------------------------------------------------
    | Group class sessions
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/group-classes/{groupClass}/sessions',
        [ClassSessionController::class, 'index']
    )->name('class-sessions.index');

    Route::post(
        '/group-classes/{groupClass}/sessions',
        [ClassSessionController::class, 'store']
    )->name('class-sessions.store');

    Route::patch(
        '/group-classes/{groupClass}/sessions/{session}/reschedule',
        [ClassSessionController::class, 'reschedule']
    )->name('class-sessions.reschedule');

    Route::patch(
        '/group-classes/{groupClass}/sessions/{session}/cancel',
        [ClassSessionController::class, 'cancel']
    )->name('class-sessions.cancel');



    /*
    |--------------------------------------------------------------------------
    | Employee management
    |--------------------------------------------------------------------------
    */
    Route::resource('employees', EmployeeController::class);

    Route::post(
        '/employees/{id}/activate',
        [EmployeeController::class, 'activate']
    )->name('employees.activate');

    /*
    |--------------------------------------------------------------------------
    | Member management
    |--------------------------------------------------------------------------
    */
    Route::resource('members', MemberController::class);

    Route::post(
        '/members/{id}/activate',
        [MemberController::class, 'activate']
    )->name('members.activate');

    /*
    |--------------------------------------------------------------------------
    | Trainer assignments
    |--------------------------------------------------------------------------
    */
    Route::get(
        'trainer-assignments/{trainerAssignment}/reassign',
        [TrainerAssignmentController::class, 'reassignCreate']
    )->name('trainer-assignments.reassign.create');

    Route::post(
        'trainer-assignments/{trainerAssignment}/reassign',
        [TrainerAssignmentController::class, 'reassignStore']
    )->name('trainer-assignments.reassign.store');

    Route::get(
        'trainer-assignments/history',
        [TrainerAssignmentController::class, 'history']
    )->name('trainer-assignments.history');

    Route::resource('trainer-assignments', TrainerAssignmentController::class);
});

/*
|--------------------------------------------------------------------------
| Receptionist
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth', 'role:receptionist'])->group(function () {
//     /*
//     |--------------------------------------------------------------------------
//     | Payments
//     |--------------------------------------------------------------------------
//     */
//     Route::resource('payments', PaymentController::class)
//         ->only(
//             ['index', 'create', 'store', 'show']
//         );
// 

/*
|--------------------------------------------------------------------------
| Administrator and receptionist
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,receptionist'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Guest passes
    |--------------------------------------------------------------------------
    */

    Route::get('/guest-passes', [GuestPassController::class, 'index'])
        ->name('guest-passes.index');

    Route::get('/guest-passes/create', [GuestPassController::class, 'create'])
        ->name('guest-passes.create');

    Route::post('/guest-passes', [GuestPassController::class, 'store'])
        ->name('guest-passes.store');

    /*
    |--------------------------------------------------------------------------
    | Class enrollments
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/class-sessions/{session}/enrollments',
        [ClassEnrollmentController::class, 'index']
    )->name('class-enrollments.index');

    Route::post(
        '/class-sessions/{session}/enrollments',
        [ClassEnrollmentController::class, 'store']
    )->name('class-enrollments.store');

    Route::patch(
        '/class-sessions/{session}/enrollments/{member}/cancel',
        [ClassEnrollmentController::class, 'cancel']
    )->name('class-enrollments.cancel');

    /*
    |--------------------------------------------------------------------------
    | Memberships
    |--------------------------------------------------------------------------
    */
    Route::resource('memberships', MembershipController::class)->only(['index', 'create', 'destroy']);
    Route::resource('payments', PaymentController::class)
    ->only(
        ['index', 'create', 'store', 'show']
    );
    Route::get(
        '/memberships/member/{memberId}',
        [MembershipController::class, 'memberMemberships']
    )->name('memberships.member');

    Route::get(
        '/memberships/{id}/{memberId}',
        [MembershipController::class, 'show']
    )->name('memberships.show');
});

/*
|--------------------------------------------------------------------------
| Member
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:member'])->group(function () {
    Route::get('/my-classes', [MemberClassController::class, 'index'])
        ->name('member-classes.index');

    Route::get('/my-classes/history', [MemberClassController::class, 'history'])
        ->name('member-classes.history');

    Route::post(
        '/my-classes/{session}/enroll',
        [MemberClassController::class, 'enroll']
    )->name('member-classes.enroll');

    Route::patch(
        '/my-classes/{session}/cancel',
        [MemberClassController::class, 'cancel']
    )->name('member-classes.cancel');

    Route::patch(
        '/my-classes/{session}/waitlist/cancel',
        [MemberClassController::class, 'cancelWaitlist']
    )->name('member-classes.waitlist.cancel');

    Route::post(
        '/my-classes/enrollments/{enrollment}/rating',
        [ClassRatingController::class, 'store']
    )->name('member-classes.rating.store');

    /*
    |--------------------------------------------------------------------------
    | Meal tracking
    |--------------------------------------------------------------------------
    */

    Route::get('/my-meals', [MemberMealController::class, 'index'])
        ->name('member-meals.index');

    Route::get('/my-meals/create', [MemberMealController::class, 'create'])
        ->name('member-meals.create');

    Route::post('/my-meals', [MemberMealController::class, 'store'])
        ->name('member-meals.store');

    Route::get('/my-meals/{meal}/edit', [MemberMealController::class, 'edit'])
        ->name('member-meals.edit');

    Route::put('/my-meals/{meal}', [MemberMealController::class, 'update'])
        ->name('member-meals.update');

    Route::delete('/my-meals/{meal}', [MemberMealController::class, 'destroy'])
        ->name('member-meals.destroy');

    /*
    |--------------------------------------------------------------------------
    | Calorie goal
    |--------------------------------------------------------------------------
    */

    Route::get('/my-calorie-goal', [MemberCalorieGoalController::class, 'edit'])
        ->name('calorie-goals.edit');

    Route::put('/my-calorie-goal', [MemberCalorieGoalController::class, 'update'])
        ->name('calorie-goals.update');

    /*
    |--------------------------------------------------------------------------
    | Nutrition history
    |--------------------------------------------------------------------------
    */

    Route::get('/my-nutrition-history', [MemberNutritionHistoryController::class, 'index'])
        ->name('nutrition-history.index');

    /*
    |--------------------------------------------------------------------------
    | Member membership
    |--------------------------------------------------------------------------
    */

    Route::get('/my-memberships', [MemberMembershipController::class, 'index'])
        ->name('member-memberships.index');
    Route::get('/my-memberships/{id}', [MemberMembershipController::class, 'show'])
        ->name('member-memberships.show');
    Route::post('/my-memberships/{id}/cancel', [MemberMembershipController::class, 'cancel'])
        ->name('member-memberships.cancel');
    Route::post('/my-membership/freeze', [MemberMembershipController::class, 'freeze'])
        ->name('member-membership.freeze');
});

/*
|--------------------------------------------------------------------------
| Trainer
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:trainer'])->group(function () {
    Route::get('/trainer/classes', [TrainerClassController::class, 'index'])
        ->name('trainer-classes.index');

    Route::get(
        '/trainer/classes/sessions/{session}',
        [TrainerClassController::class, 'show']
    )->name('trainer-classes.show');

    Route::patch(
        '/trainer/classes/sessions/{session}/start',
        [TrainerClassController::class, 'start']
    )->name('trainer-classes.start');

    Route::patch(
        '/trainer/classes/sessions/{session}/attendance',
        [TrainerClassController::class, 'updateAttendance']
    )->name('trainer-classes.attendance');

    Route::patch(
        '/trainer/classes/sessions/{session}/complete',
        [TrainerClassController::class, 'complete']
    )->name('trainer-classes.complete');

    // Ver socios asignados al entrenador
    Route::get(
        '/trainer/assignments',
        [AssignmentController::class, 'index']
    )->name('assignments.index');

    Route::get(
        '/trainer/assignments/history',
        [AssignmentController::class, 'history']
    )->name('assignments.history');

    Route::get(
        '/trainer/assignments/{trainerAssignment}',
        [AssignmentController::class, 'show']
    )->name('assignments.show');

    Route::post(
        '/trainer/assignments/{trainerAssignment}/measurements',
        [MeasurementController::class, 'store']
    )->name('assignments.measurements.store');

    Route::post(
        '/trainer/assignments/{trainerAssignment}/calorie-goal',
        [TrainerCalorieGoalController::class, 'store']
    )->name('assignments.calorie-goal.store');

    Route::post(
        '/trainer/assignments/{trainerAssignment}/nutritional-observations',
        [NutritionalObservationController::class, 'store']
    )->name('assignments.nutritional-observations.store');
});

/*
|--------------------------------------------------------------------------
| Authentication routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
