<?php

namespace App\Exceptions;

use App\Http\Response\RedirectMessageResponse;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Wanjia\Common\Exceptions\AppException;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    public function unauthenticated($request, AuthenticationException $ex)
    {
        if (preg_match('@admin($|/)@', $request->path())) {
            return redirect('admin/login');
        }

        return abort(403, 'Forbidden');
    }


    /**
     * Report or log an exception.
     * 报告异常
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        if ($exception instanceof AppException) {
            wj_json_message($exception->getMessage(), $exception->getCode())->send();
            exit;
        } elseif ($exception instanceof RedirectMessageException) {
            if ($request->expectsJson()) {
                wj_json_message([
                    'msg'   => $exception->getMessage(),
                    'url'   => $exception->getUrl()
                ], 99302)->send();
            } else {
                (new RedirectMessageResponse($exception->getMessage()))
                    ->to($exception->getUrl())
                    ->delay($exception->getDelay())
                    ->send();
            }
            exit;
        }
        return parent::render($request, $exception);
    }

    protected function prepareResponse($request, Exception $e)
    {
        if (config('app.debug') && !$this->isHttpException($e)) {
            return $this->renderExceptionWithWhoops($e);
        }

        return parent::prepareResponse($request, $e);
    }

    protected function renderExceptionWithWhoops(Exception $e)
    {
        $whoops = app(\Whoops\Run::class);
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());

        return new \Illuminate\Http\Response(
            $whoops->handleException($e),
            $e->getStatusCode(),
            $e->getHeaders()
        );
    }

    /**
     * Render the given HttpException.
     *
     * @param  \Symfony\Component\HttpKernel\Exception\HttpException  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpException $e)
    {
        $status = $e->getStatusCode();

        $paths = collect(config('view.paths'));

        view()->replaceNamespace('errors', $paths->map(function ($path) {
            return "{$path}/errors";
        })->push(__DIR__.'/views')->all());

        if (view()->exists($view = "errors::{$status}")) {
            return response(view($view, ['exception' => $e]), $status, $e->getHeaders());
        }

        return $this->convertExceptionToResponse($e);
    }
}
