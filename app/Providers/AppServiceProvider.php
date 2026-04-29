<?php

namespace App\Providers;

use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\CatalogRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Interfaces\QrRepositoryInterface;
use App\Interfaces\StudentRepositoryInterface;
use App\Interfaces\TutorRepositoryInterface;
use App\Repositories\AuthRepository;
use App\Repositories\CatalogRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\QrRepository;
use App\Repositories\StudentRepository;
use App\Repositories\TutorRepository;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StudentRepositoryInterface::class, function($app){
            return new StudentRepository();
        });
        $this->app->bind(CatalogRepositoryInterface::class, function($app){
            return new CatalogRepository();
        });
        $this->app->bind(TutorRepositoryInterface::class, function($app){
            return new TutorRepository();
        });
        $this->app->bind(AuthRepositoryInterface::class, function($app){
            return new AuthRepository();
        });
        $this->app->bind(PaymentRepositoryInterface::class, function($app){
            return new PaymentRepository();
        });
        $this->app->bind(QrRepositoryInterface::class, function($app){
            return new QrRepository();
        });
        $this->app->bind(DocumentRepository::class, function($app){
            return new DocumentRepository();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
