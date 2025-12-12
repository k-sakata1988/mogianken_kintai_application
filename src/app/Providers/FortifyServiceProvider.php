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
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\Hash;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::authenticateUsing(function ($request) {
            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && \Hash::check($request->password, $user->password)) {
                if (!$user->hasVerifiedEmail()) {
                    // 未認証ならメール誘導画面へ
                    session()->flash('verify_notice', 'メール認証を完了してください。');
                    return null; // 認証失敗扱いにしてリダイレクトさせる
                }
                return $user;
            }
        });
        Fortify::registerView(function(){
            return view('auth.register');
        });
        Fortify::loginview(function(){
            return view('auth.login');
        });

        RateLimiter::for('login', function(Request $request){
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });
    }
}
