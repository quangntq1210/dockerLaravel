<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\UnauthorizedException;
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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     * @param mixed $request
     * @param Throwable $exception
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($exception);
        }

        return parent::render($request, $exception);
    }

    private function handleApiException(Throwable $exception)
    {
        // 401 - Unauthorized
        if ($exception instanceof UnauthorizedException) {
            return response()->json([
                'success' => false,
                'message' => __('message.unauthorized'),
            ], 401);
        }

        // 401 - Authentication error
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'message' => __('message.authentication_error'),
            ], 401);
        }

        // 403 - Forbidden
        if ($exception instanceof HttpException && $exception->getStatusCode() === 403) {
            return response()->json([
                'success' => false,
                'message' => __('message.forbidden'),
            ], 403);
        }

        // 404 - Model::findOrFail() of Laravel
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => __('message.resource_not_found'),
            ], 404);
        }

        // 404 - Resource not found
        if ($exception instanceof ResourceNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 404);
        }

        // 422 - Validation error
        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => __('message.validation_failed'),
                'errors' => $exception->errors(),
            ], 422);
        }

        // 500 - Database error
        if ($exception instanceof QueryException) {
            Log::error('Database error', [
                'message' => $exception->getMessage(),
                'sql'     => $exception->getSql(),
                'bindings' => $exception->getBindings(),
            ]);
            return response()->json([
                'success' => false,
                'message' => __('message.error_occurred'),
            ], 500);
        }

        // 500 - Unexpected error
        Log::error('Unexpected error', ['exception' => $exception]);
        return response()->json([
            'success' => false,
            'message' => __('message.error_occurred'),
        ], 500);
    }
}

//     /**
//      * @param \Illuminate\Http\Request $request
//      * @param \Throwable $exception
//      * @return \Symfony\Component\HttpFoundation\Response
//      *
//      * @throws \Throwable
//      */
//     public function render($request, Throwable $exception)
//     {
//         if ($this->requestWantsJsonApi($request)) {
//             return $this->renderJsonException($request, $exception);
//         }

//         return parent::render($request, $exception);
//     }

//     /**
//      * JSON thống nhất cho API / AJAX (không override shouldReturnJson của framework).
//      *
//      * @param \Illuminate\Http\Request $request
//      * @return bool
//      */
//     protected function requestWantsJsonApi(Request $request)
//     {
//         return $request->expectsJson()
//             || $request->is('api/*')
//             || $request->ajax();
//     }

//     /**
//      * @param \Illuminate\Http\Request $request
//      * @param \Throwable $e
//      * @return \Illuminate\Http\JsonResponse
//      */
//     protected function renderJsonException(Request $request, Throwable $e): JsonResponse
//     {
//         if ($e instanceof ValidationException) {
//             return $this->jsonError(
//                 $e->getMessage() ?: __('message.validation_failed'),
//                 422,
//                 $e->errors()
//             );
//         }

//         if ($e instanceof AuthenticationException) {
//             return $this->jsonError(__('message.unauthenticated'), 401);
//         }

//         if ($e instanceof AuthorizationException) {
//             return $this->jsonError(__('message.forbidden'), 403);
//         }

//         if ($e instanceof ModelNotFoundException) {
//             return $this->jsonError($this->modelNotFoundMessage($e), 404);
//         }

//         if ($e instanceof ResourceNotFoundException) {
//             return $this->jsonError($e->getMessage(), $e->getStatusCode());
//         }

//         if ($e instanceof NotFoundHttpException) {
//             return $this->jsonError(__('message.not_found'), 404);
//         }

//         if ($e instanceof TokenMismatchException) {
//             return $this->jsonError(__('message.session_expired'), 419);
//         }

//         if ($e instanceof HttpException) {
//             $status = $e->getStatusCode();
//             $message = $e->getMessage();
//             if ($message === '') {
//                 $message = __('message.error_occurred');
//             }

//             return $this->jsonError($message, $status);
//         }

//         if ($e instanceof QueryException) {
//             report($e);

//             return $this->jsonError(__('message.error_occurred'), 500);
//         }

//         if (config('app.debug')) {
//             return $this->jsonError(
//                 $e->getMessage(),
//                 500,
//                 [
//                     'exception' => get_class($e),
//                     'file' => $e->getFile(),
//                     'line' => $e->getLine(),
//                 ]
//             );
//         }

//         report($e);

//         return $this->jsonError(__('message.error_occurred'), 500);
//     }

//     /**
//      * @param \Illuminate\Database\Eloquent\ModelNotFoundException $e
//      * @return string
//      */
//     protected function modelNotFoundMessage(ModelNotFoundException $e)
//     {
//         $basename = class_basename($e->getModel());

//         if ($basename === 'Notification') {
//             return __('message.notification_not_found');
//         }

//         if ($basename === 'Campaign') {
//             return __('message.campaign_not_found');
//         }

//         if ($basename === 'User') {
//             return __('message.user_not_found');
//         }

//         return __('message.resource_not_found');
//     }

//     /**
//      * Cùng cấu trúc với ApiResponseTrait::error().
//      *
//      * @param string $message
//      * @param int $status
//      * @param array|null $errors
//      * @return \Illuminate\Http\JsonResponse
//      */
//     protected function jsonError(string $message, int $status = 400, ?array $errors = null): JsonResponse
//     {
//         return response()->json([
//             'success' => false,
//             'code' => $status,
//             'message' => $message,
//             'errors' => $errors,
//         ], $status);
//     }
// }
