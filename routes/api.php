<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CatalogController;
use App\Http\Controllers\api\DocumentController;
use App\Http\Controllers\api\PaymentController;
use App\Http\Controllers\api\StudentController;
use App\Http\Controllers\api\TutorController;
use App\Http\Controllers\api\QrController;
use App\Http\Middleware\JWTValidation;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthController::class)->group(function(){
    $path = '/auth';
    Route::post($path . '/login', 'login');
    Route::post($path . '/register', 'register');
    Route::get($path . '/refresh-token', 'refresh_token')->middleware(JWTValidation::class);
    Route::get($path . '/get-modules', 'get_modules')->middleware(JWTValidation::class);
});

Route::controller(StudentController::class)->group(function(){
    $path = '/student';
    Route::get($path, 'index')->middleware(JWTValidation::class);
    Route::get($path . '/{uuid}', 'get_student_by_uuid');
    Route::post($path, 'store')->middleware(JWTValidation::class);
    Route::put($path, 'update')->middleware(JWTValidation::class);
    Route::delete($path . '/{id}', 'delete')->middleware(JWTValidation::class);
    Route::get($path . '/by-group/{groupId}', 'get_students_by_group')->middleware(JWTValidation::class);
});

Route::controller(TutorController::class)->group(function(){
    $path = '/tutor';
    Route::get($path . '/by-student/{studentId}', 'get_all_tutors_for_student')->middleware(JWTValidation::class);
    Route::get($path . '/by-id/{id}', 'get_tutor_by_id')->middleware(JWTValidation::class);
    Route::post($path, 'store_tutor')->middleware(JWTValidation::class);
    Route::put($path, 'update_tutor')->middleware(JWTValidation::class);
    Route::delete($path . '/{id}', 'delete_tutor')->middleware(JWTValidation::class);
});

Route::controller(CatalogController::class)->group(function(){
    $path = '/catalog';

    /**
     * Scholar years
     */
    $scholarYearPath = '/scholar-years';
    Route::get($path . $scholarYearPath, 'get_scholar_years')->middleware(JWTValidation::class);
    Route::post($path . $scholarYearPath, 'create_scholar_year')->middleware(JWTValidation::class);
    Route::put($path . $scholarYearPath, 'update_scholar_year')->middleware(JWTValidation::class);
    Route::delete($path . $scholarYearPath . '/{id}', 'delete_scholar_year')->middleware(JWTValidation::class);

    /**
     * Academic levels
     */
    $academicLevelPath = '/academic-levels';
    Route::get($path . $academicLevelPath, 'get_academic_levels')->middleware(JWTValidation::class);
    Route::post($path . $academicLevelPath, 'create_academic_level')->middleware(JWTValidation::class);
    Route::put($path . $academicLevelPath, 'update_academic_level')->middleware(JWTValidation::class);
    Route::delete($path . $academicLevelPath . '/{id}', 'delete_academic_level')->middleware(JWTValidation::class);

    /**
     * Groups
     */
    $groupPath = '/groups';
    Route::get($path . $groupPath, 'get_groups')->middleware(JWTValidation::class);
    Route::post($path . $groupPath, 'create_group')->middleware(JWTValidation::class);
    Route::put($path . $groupPath, 'update_group')->middleware(JWTValidation::class);
    Route::delete($path . $groupPath . '/{id}', 'delete_group')->middleware(JWTValidation::class);

    /**
     * Pay Concepts
     */
    $payConceptPath = '/pay-concepts';
    Route::get($path . $payConceptPath, 'get_pay_concepts')->middleware(JWTValidation::class);
    Route::post($path . $payConceptPath, 'create_pay_concept')->middleware(JWTValidation::class);
    Route::put($path . $payConceptPath, 'update_pay_concept')->middleware(JWTValidation::class);
    $pricesPath = '/prices';
    Route::get($path . $payConceptPath . $pricesPath . '/{id}', 'get_pay_concept_prices')->middleware(JWTValidation::class);
    Route::post($path . $payConceptPath . $pricesPath, 'create_pay_concept_price')->middleware(JWTValidation::class);
});

Route::controller(PaymentController::class)->group(function(){
    $paymentPath = '/payments';
    Route::get($paymentPath, 'get_payments_by_student_id')->middleware(JWTValidation::class);
    Route::post($paymentPath, 'save_payment');//->middleware(JWTValidation::class);
    Route::post($paymentPath . '/pending-payments', 'get_pending_payments_by_student_id');//->middleware(JWTValidation::class);
    Route::post($paymentPath . '/service-payments', 'get_all_service_payments_by_academic_level');//->middleware(JWTValidation::class);
});

Route::controller(QrController::class)->group(function(){
    $qrPath = '/qr';
    Route::get($qrPath, 'generate_qr_code');
});

Route::controller(DocumentController::class)->group(function(){
    $documentPath = '/documents';
    Route::get($documentPath . '/get-ticket/{folioTicket}', 'get_ticket');
    Route::get($documentPath . '/close-ticket/{selectedDay}', 'close_ticket');
    Route::get($documentPath . '/get-pending-payments-report', 'get_pending_payments_report');
});
