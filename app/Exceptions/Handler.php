<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * A short code shown to the user and written to the log, so an error on screen
     * can be matched to its entry in storage/logs.
     */
    public static function reference(): string
    {
        if (! app()->bound('error.reference')) {
            app()->instance('error.reference', Str::upper(Str::random(8)));
        }

        return app('error.reference');
    }

    /**
     * @return array<string, mixed>
     */
    protected function context()
    {
        return array_merge(parent::context(), [
            'ref' => static::reference(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);
    }

    public function register(): void
    {
        // Expired form (CSRF token / session timed out): send the user back to the
        // form with what they typed instead of showing a "419 Page Expired" page.
        $this->renderable(function (HttpExceptionInterface $e, Request $request) {
            if ($e->getStatusCode() !== 419 || $request->expectsJson()) {
                return null;
            }

            $target = $request->user() ? url()->previous() : route('login');

            return redirect()->to($target)
                ->withInput($request->except([...$this->dontFlash, '_token', '_method']))
                ->with('error', 'Your session expired, so that action was not saved. Please try again.');
        });

        // Unexpected failure while saving a form: return to the form with the user's
        // input and a friendly message rather than a bare error page.
        $this->renderable(function (Throwable $e, Request $request) {
            if (config('app.debug')
                || $request->isMethod('GET')
                || $request->expectsJson()
                || $e instanceof HttpExceptionInterface
                || $e instanceof ValidationException
                || $e instanceof AuthenticationException
                || $e instanceof HttpResponseException) {
                return null;
            }

            return back()
                ->withInput($request->except([...$this->dontFlash, '_token', '_method']))
                ->with('error', 'Sorry, something went wrong and nothing was saved. Please try again. '
                    .'If it keeps happening, give this error code to your administrator: '.static::reference());
        });
    }
}
