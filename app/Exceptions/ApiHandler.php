<?php
/**
 * Created by PhpStorm.
 * User: Alaa
 * Date: 07-Aug-17
 * Time: 1:26 PM
 */

namespace App\Exceptions;

use App\Http\Controllers\ApiController;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ApiHandler extends Handler
{
    protected $apiController;
    protected $container;

    /**
     * ApiHandler constructor.
     */
    public function __construct()
    {
        $this->apiController = new ApiController();
        parent::__construct($this->container);
    }

    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    public function render($request, Exception $exception)
    {
        if($exception instanceof ValidationException || $exception instanceof UnprocessableEntityHttpException)
            return $this->apiController->setStatusCode(422)->respondWithMessage('Sorry, validation failed!');

        if($exception instanceof NotFoundHttpException || $exception instanceof ModelNotFoundException)
            return $this->apiController->respondNotFound();

        if($exception instanceof BadRequestHttpException)
            return $this->apiController->setStatusCode(400)->respondWithMessage('Invalid Request!');

        if($exception instanceof AuthorizationException)
            return $this->apiController->respondForbidden();

        if($exception instanceof MethodNotAllowedHttpException)
            return $this->apiController->setStatusCode(405)->respondWithMessage('Method not allowed!');

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->apiController->respondUnAuthenticated('You\'re not authorized, You have to login!');
    }
}