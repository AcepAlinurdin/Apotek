<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;

class FortifyServiceProvider extends ServiceProvider
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
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // [FIXED] Membungkus anonymous class di dalam sebuah Closure
        $this->app->singleton(LoginResponse::class, function ($app) {
            return new class implements LoginResponse {
                /**
                 * Membuat respons HTTP yang mewakili objek.
                 *
                 * @param  \Illuminate\Http\Request  $request
                 * @return \Symfony\Component\HttpFoundation\Response
                 */
                public function toResponse($request)
                {
                    $user = $request->user();

                    // Jika role adalah admin atau kepala apotek, arahkan ke Master Data
                    if ($user->hasRole('admin') || $user->hasRole('kepala apotek')) {
                        return redirect()->intended(route('obat.master.index'));
                    }

                    // Jika role adalah apoteker, arahkan ke halaman Penjualan
                    if ($user->hasRole('apoteker')) {
                        return redirect()->intended(route('penjualan.index'));
                    }

                    // Jika tidak punya role spesifik, arahkan ke dashboard bawaan
                    return redirect()->intended(config('fortify.home'));
                }
            };
        });
    }
}
