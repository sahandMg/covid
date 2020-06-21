<?php

namespace App\Exceptions;

use App\Log;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        $log = new Log();
        $log->message = $exception->getMessage() == null ?
            'No Error --> '. $exception->getFile().' & Code = '.$exception->getCode().
            ' & IP --> '.$this->ipFinder():
            $exception->getMessage().' In --> '.$exception->getFile().
            ' At line --> '.$exception->getLine().' & IP --> '.$this->ipFinder();
        $log->save();
        parent::report($exception);
    }

    private function ipFinder(){

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            if(isset($_SERVER['REMOTE_ADDR'])){

                $ip = $_SERVER['REMOTE_ADDR'];
            }else{
                $ip = 'Unknown';
            }
        }
        return $ip;
}

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}
