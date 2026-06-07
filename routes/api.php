<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CatalogController;
use App\Http\Controllers\api\DashboardController;
use App\Http\Controllers\api\DocumentController;
use App\Http\Controllers\api\EnrollmentController;
use App\Http\Controllers\api\PaymentController;
use App\Http\Controllers\api\PromotionConfigController;
use App\Http\Controllers\api\ScholarshipController;
use App\Http\Controllers\api\StudentController;
use App\Http\Controllers\api\TicketController;
use App\Http\Controllers\api\TutorController;
use App\Http\Controllers\api\QrController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\WithdrawalController;
use App\Http\Middleware\JWTValidation;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthController::class)->group(function(){
    $path = '/auth';
    Route::post($path . '/login', 'login')->middleware('throttle.login');
    Route::post($path . '/register', 'register');
    Route::post($path . '/logout', 'logout')->middleware(JWTValidation::class);
    Route::get($path . '/refresh-token', 'refresh_token')->middleware(JWTValidation::class);
    Route::get($path . '/get-modules', 'get_modules')->middleware(JWTValidation::class);
});

Route::controller(StudentController::class)->group(function(){
    $path = '/student';
    Route::get($path, 'index')->middleware(JWTValidation::class);
    Route::get($path . '/{uuid}', 'get_student_by_uuid');
    Route::post($path, 'store')->middleware([JWTValidation::class, 'permission:Alumnos,Crear']);
    Route::put($path, 'update')->middleware([JWTValidation::class, 'permission:Alumnos,Editar']);
    Route::delete($path . '/{id}', 'delete')->middleware([JWTValidation::class, 'permission:Alumnos,Eliminar']);
    Route::get($path . '/by-group/{groupId}', 'get_students_by_group')->middleware(JWTValidation::class);
});

Route::controller(TutorController::class)->group(function(){
    $path = '/tutor';
    Route::get($path, 'index')->middleware(JWTValidation::class);
    Route::get($path . '/by-student/{studentId}', 'get_all_tutors_for_student')->middleware(JWTValidation::class);
    Route::get($path . '/by-id/{id}', 'get_tutor_by_id')->middleware(JWTValidation::class);
    Route::post($path, 'store_tutor')->middleware([JWTValidation::class, 'permission:Tutores,Crear']);
    Route::put($path, 'update_tutor')->middleware([JWTValidation::class, 'permission:Tutores,Editar']);
    Route::delete($path . '/{id}', 'delete_tutor')->middleware([JWTValidation::class, 'permission:Tutores,Eliminar']);
});

Route::controller(CatalogController::class)->group(function(){
    $path = '/catalog';

    $scholarYearPath = '/scholar-years';
    Route::get($path . $scholarYearPath, 'get_scholar_years')->middleware(JWTValidation::class);
    Route::post($path . $scholarYearPath, 'create_scholar_year')->middleware([JWTValidation::class, 'permission:Catálogos,Crear']);
    Route::put($path . $scholarYearPath, 'update_scholar_year')->middleware([JWTValidation::class, 'permission:Catálogos,Editar']);
    Route::delete($path . $scholarYearPath . '/{id}', 'delete_scholar_year')->middleware([JWTValidation::class, 'permission:Catálogos,Eliminar']);

    $academicLevelPath = '/academic-levels';
    Route::get($path . $academicLevelPath, 'get_academic_levels')->middleware(JWTValidation::class);
    Route::post($path . $academicLevelPath, 'create_academic_level')->middleware([JWTValidation::class, 'permission:Catálogos,Crear']);
    Route::put($path . $academicLevelPath, 'update_academic_level')->middleware([JWTValidation::class, 'permission:Catálogos,Editar']);
    Route::delete($path . $academicLevelPath . '/{id}', 'delete_academic_level')->middleware([JWTValidation::class, 'permission:Catálogos,Eliminar']);

    $groupPath = '/groups';
    Route::get($path . $groupPath, 'get_groups')->middleware(JWTValidation::class);
    Route::post($path . $groupPath, 'create_group')->middleware([JWTValidation::class, 'permission:Catálogos,Crear']);
    Route::put($path . $groupPath, 'update_group')->middleware([JWTValidation::class, 'permission:Catálogos,Editar']);
    Route::delete($path . $groupPath . '/{id}', 'delete_group')->middleware([JWTValidation::class, 'permission:Catálogos,Eliminar']);

    $payConceptPath = '/pay-concepts';
    Route::get($path . $payConceptPath, 'get_pay_concepts')->middleware(JWTValidation::class);
    Route::post($path . $payConceptPath, 'create_pay_concept')->middleware([JWTValidation::class, 'permission:Catálogos,Crear']);
    Route::put($path . $payConceptPath, 'update_pay_concept')->middleware([JWTValidation::class, 'permission:Catálogos,Editar']);
    $pricesPath = '/prices';
    Route::get($path . $payConceptPath . $pricesPath . '/{id}', 'get_pay_concept_prices')->middleware(JWTValidation::class);
    Route::post($path . $payConceptPath . $pricesPath, 'create_pay_concept_price')->middleware([JWTValidation::class, 'permission:Catálogos,Crear']);

    // Clonar conceptos al nuevo ciclo
    Route::post($path . $payConceptPath . '/clone', 'clone_pay_concepts')->middleware([JWTValidation::class, 'permission:Catálogos,Crear']);
});

Route::controller(PaymentController::class)->group(function(){
    $paymentPath = '/payments';
    Route::get($paymentPath, 'get_payments_by_student_id')->middleware(JWTValidation::class);
    Route::post($paymentPath, 'save_payment')->middleware([JWTValidation::class, 'permission:Pagos,Cobrar']);
    Route::post($paymentPath . '/pending-payments', 'get_pending_payments_by_student_id')->middleware(JWTValidation::class);
    Route::post($paymentPath . '/service-payments', 'get_all_service_payments_by_academic_level')->middleware(JWTValidation::class);
    Route::get($paymentPath . '/daily-income', 'daily_income')->middleware(JWTValidation::class);
    Route::get($paymentPath . '/promotion/check', 'check_promotion')->middleware(JWTValidation::class);
});

Route::controller(TicketController::class)->group(function(){
    $path = '/tickets';
    Route::post($path . '/cancel', 'cancel')->middleware([JWTValidation::class, 'permission:Pagos,Cobrar']);
    Route::get($path . '/cancelled', 'cancelled')->middleware(JWTValidation::class);
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

Route::controller(DashboardController::class)->group(function(){
    $path = '/dashboard';
    Route::get($path . '/summary', 'summary')->middleware(JWTValidation::class);
    Route::get($path . '/account-statement', 'accountStatement')->middleware(JWTValidation::class);
});

Route::controller(UserController::class)->group(function(){
    Route::get('/users', 'index')->middleware(JWTValidation::class);
    Route::post('/users', 'store')->middleware([JWTValidation::class, 'permission:Usuarios,Crear']);
    Route::put('/users', 'update')->middleware([JWTValidation::class, 'permission:Usuarios,Editar']);
    Route::delete('/users/{id}', 'delete')->middleware([JWTValidation::class, 'permission:Usuarios,Editar']);
    Route::get('/roles', 'get_roles')->middleware(JWTValidation::class);
});

Route::controller(ScholarshipController::class)->group(function(){
    $path = '/scholarships';
    Route::get($path, 'index')->middleware(JWTValidation::class);
    Route::post($path, 'store')->middleware(JWTValidation::class);
    Route::put($path, 'update')->middleware(JWTValidation::class);
    Route::delete($path . '/{id}', 'delete')->middleware(JWTValidation::class);
});

Route::controller(PromotionConfigController::class)->group(function(){
    $path = '/promotion-config';
    Route::get($path, 'index')->middleware(JWTValidation::class);
    Route::post($path, 'store')->middleware([JWTValidation::class, 'permission:Pronto Pago,Crear']);
    Route::put($path, 'update')->middleware([JWTValidation::class, 'permission:Pronto Pago,Editar']);
    Route::delete($path . '/{id}', 'delete')->middleware([JWTValidation::class, 'permission:Pronto Pago,Eliminar']);
});

Route::controller(WithdrawalController::class)->group(function(){
    $path = '/withdrawals';
    Route::get($path, 'index')->middleware(JWTValidation::class);
    Route::post($path, 'store')->middleware([JWTValidation::class, 'permission:Bajas,Crear']);
    Route::put($path . '/reactivate/{id}', 'reactivate')->middleware([JWTValidation::class, 'permission:Bajas,Reactivar']);
});

Route::controller(EnrollmentController::class)->group(function(){
    $path = '/enrollment';
    Route::post($path . '/preview', 'preview')->middleware([JWTValidation::class, 'permission:Reinscripción,Ver']);
    Route::post($path . '/promote-batch', 'promoteBatch')->middleware([JWTValidation::class, 'permission:Reinscripción,Ejecutar']);
    Route::post($path . '/promote-student', 'promoteStudent')->middleware([JWTValidation::class, 'permission:Reinscripción,Ejecutar']);
    Route::get($path . '/history', 'history')->middleware(JWTValidation::class);

    $gradePath = '/grade-promotions';
    Route::get($gradePath, 'getGradePromotions')->middleware(JWTValidation::class);
    Route::post($gradePath, 'storeGradePromotion')->middleware([JWTValidation::class, 'permission:Reinscripción,Configurar Grados']);
    Route::put($gradePath, 'updateGradePromotion')->middleware([JWTValidation::class, 'permission:Reinscripción,Configurar Grados']);
    Route::delete($gradePath . '/{id}', 'deleteGradePromotion')->middleware([JWTValidation::class, 'permission:Reinscripción,Configurar Grados']);
});
