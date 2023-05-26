<?php

namespace ZaghloulSoft\LaravelAuthorization\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

trait Response
{

    public $statusCodeSuccess = 200;
    public $statusCodeBadRequest = 400;
    public $statusCodeToManyRequests = 429;
    public $statusCodeUnAuthorized = 401;
    public $statusCodeForbidden = 403;
    public $statusCodeNotFound = 404;
    public $statusCodeUnAvailable = 503;
    public $statusCodeServerTimeOut = 504;



    public function error(string $message,int $status) : HttpResponseException
    {
        throw new HttpResponseException(response()->json([
            'status'=> false,
            'message'=> $message,
        ],$status));
    }

    public function success(string $message) : JsonResponse
    {
        return response()->json([
            'status'=> true,
            'message'=> $message,
        ],$this->statusCodeSuccess);
    }

    public function data(string $message,$data) : JsonResponse
    {
        return response()->json([
            'status'=> true,
            'message'=> $message,
            'data'=> $data,
        ],$this->statusCodeSuccess);
    }

}
