<?php

use App\Http\Middleware\CheckAppEnabled;
use App\Http\Middleware\ConvertDigitsToEnglish;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function (){
            $isDemo = filter_var(env('IS_DEMO', false), FILTER_VALIDATE_BOOLEAN);
            $isLocal = env('APP_ENV') === 'local';
            $domain = env('DOMAIN_ADDRESS');
            $demoName = env('APP_NAME','demo');
            if($isLocal){
                Route::middleware('web')
                    ->as('admin.')
                    ->prefix('admin')
                    ->domain($domain)
                    ->group(base_path('routes/web/admin.php'));
                Route::middleware('web')
                    ->as('client.')
                    ->prefix('app')
                    ->domain($domain)
                    ->group(base_path('routes/web/client.php'));
                Route::middleware('web')
                    ->prefix('web')
                    ->group(base_path('routes/web.php'));
            }
            else{
                Route::middleware('web')
                    ->as('admin.')
                    ->domain('toucan.borjix.ir/admin')
                    ->prefix('admin')
                    ->group(base_path('routes/web/admin.php'));
                Route::middleware('web')
                    ->as('client.')
                    ->domain('toucan.borjix.ir/app')
                    ->prefix('app')
                    ->group(base_path('routes/web/client.php'));
                Route::middleware('web')
                    ->domain('toucan.borjix.ir')
                    ->group(base_path('routes/web.php'));
            }
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append([
            ConvertDigitsToEnglish::class,
            CheckAppEnabled::class
        ]);
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'payment/callback',
        ]);
        $middleware->alias([
            'Sms' => App\Facades\Sms::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response) {
            if ($response->getStatusCode() === 419) {
                return back()->with([
                    'message' => 'The page expired, please try again.',
                ]);
            }

            return $response;
        });
    })->create();
