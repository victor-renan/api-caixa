<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    ResetPassword::createUrlUsing(function (User $user, string $token) {
      return env('VITE_APP_NAME') . "/reset-password?token=$token";
    });
  }
}
