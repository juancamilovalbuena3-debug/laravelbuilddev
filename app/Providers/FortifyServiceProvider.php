<?php
namespace App\Providers;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());
            $blockKey = 'login_blocked_'.$throttleKey;
            $attemptsKey = 'login_attempts_'.$throttleKey;

            // Si está bloqueado, calcular tiempo restante y redirigir
            if (Cache::has($blockKey)) {
                $remainingSeconds = Cache::get($blockKey . '_expires') - time();
                $remainingMinutes = max(1, ceil($remainingSeconds / 60));
                return redirect()->route('login')->withErrors([
                    'email' => 'Demasiados intentos fallidos. Tu acceso está bloqueado por '.$remainingMinutes.' minuto(s). Intenta de nuevo más tarde.',
                ]);
            }

            // Contar intentos
            $attempts = Cache::get($attemptsKey, 0) + 1;
            Cache::put($attemptsKey, $attempts, now()->addMinutes(5));

            // Al tercer intento fallido, bloquear 5 minutos
            if ($attempts >= 3) {
                Cache::put($blockKey, true, now()->addMinutes(5));
                Cache::put($blockKey . '_expires', time() + 300, now()->addMinutes(5));
                Cache::forget($attemptsKey);
                return redirect()->route('login')->withErrors([
                    'email' => 'Has superado el número máximo de intentos. Tu acceso está bloqueado por 5 minutos.',
                ]);
            }

            return Limit::perMinute(3)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
