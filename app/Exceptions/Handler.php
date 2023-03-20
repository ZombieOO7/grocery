<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Mail\ExceptionOccured;
use Mail;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use Symfony\Component\Debug\Exception\FlattenException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {

        if ($this->shouldReport($exception) || $exception->getMessage() !== "Unauthenticated.") {
            // $this->sendEmail($exception); // sends an email
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if (isset($request->segments()[0]) && $request->segments()[0] == 'api' &&  $request->header('Authorization') == NULL) {
            return response()->json([
                'data' => (object) [],
                'status' => false,
                'status_code' => 403,
                'message' => 'Your session has been expired please login again to continue',
            ]);
        }

        if (isset($request->segments()[0]) && $request->segments()[0] == 'api' &&  $request->user() == null) {
            return response()->json([
                'data' => (object) [],
                'status' => false,
                'status_code' => 403,
                'message' => 'Your session has been expired please login again to continue',
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);

        }
        if ($request->is('admin') || $request->is('admin/*')) {
            return redirect()->guest(route('admin.login'));
        }
        return redirect()->guest(route('login'));
    }

    public function sendEmail(Exception $exception)
    {
        try {
            $e = FlattenException::create($exception);
            $handler = new SymfonyExceptionHandler();
            $html = $handler->getHtml($e);
            $emails=explode(",",config('project.dev_email'));
            \Mail::to($emails)->send(new ExceptionOccured($html));
        } catch (Exception $ex) {
            dd($ex);
        }
    }
}
