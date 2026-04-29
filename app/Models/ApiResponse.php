<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiResponse extends Model
{
    public function __construct(
        int $status = 200,
        string $message = 'Success',
        array|object $data = [],
        array|object $errors
    ) {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;

        if($errors){
            $this->errors = $errors;
        }
    }

    public static function success(
        string $message,
        array|object $data
    ) {
        return new ApiResponse(
            200,
            $message,
            $data,
            []
        );
    }

    public static function internalError(
        string $message,
        array|object $errors
    ) {
        return new ApiResponse(
            500,
            $message,
            [],
            $errors
        );
    }

    public static function badRequest(
        string $message,
        array|object $errors
    ) {
        return new ApiResponse(
            400,
            $message,
            [],
            $errors
        );
    }

    public static function unauthorized(
        string $message,
        array|object $errors
    ) {
        return new ApiResponse(
            401,
            $message,
            [],
            $errors
        );
    }

    public static function forbidden(
        string $message,
        array|object $errors
    ) {
        return new ApiResponse(
            403,
            $message,
            [],
            $errors
        );
    }

    public static function notFound(
        string $message,
        array|object $errors
    ) {
        return new ApiResponse(
            404,
            $message,
            [],
            $errors
        );
    }
}
