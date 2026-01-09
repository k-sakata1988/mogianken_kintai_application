<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Http\Responses\LoginResponse as CustomLoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
        $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * ログイン処理 + バリデーション
         */
        Fortify::authenticateUsing(function (Request $request) {

            Validator::make($request->all(), [
                'email' => ['required', 'email'],
                'password' => ['required', 'min:8'],
            ], [
                'email.required' => 'メールアドレスを入力してください',
                'email.email' => 'メールアドレスを正しく入力してください',
                'password.required' => 'パスワードを入力してください',
                'password.min' => 'パスワードは8文字以上で入力してください',
            ])->validate();

            $user = User::where('email', $request->email)->first();

            // 認証失敗
            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => 'メールアドレスまたはパスワードが正しくありません。',
                ]);
            }

            // メール未認証
            if (! $user->is_admin && ! $user->hasVerifiedEmail()) {
                throw ValidationException::withMessages([
                    'email' => 'メール認証を完了してください。',
                ]);
            }

            // 管理者ログイン画面から来ているのに一般ユーザーだった場合
            if ($request->login_type === 'admin' && ! $user->is_admin) {
                throw ValidationException::withMessages([
                    'email' => '管理者アカウントではありません。',
                ]);
            }

            return $user;
        });

        /**
         * ログイン・登録画面
         */
        Fortify::loginView(function () {
            return view('user.auth.login');
        });

        Fortify::registerView(function () {
            return view('user.auth.register');
        });

        /**
         * レートリミット
         */
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by(
                $request->email.$request->ip()
            );
        });
    }
}
