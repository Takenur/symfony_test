<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ResponseTrait
{


    /**
     * return response template
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @param bool $success
     * @return JsonResponse
     */
    protected function response(mixed $data = [], mixed $message = null, int $code = 200,bool $success=true): JsonResponse
    {

        $responseData = [
            'success' => $success,
            'message' =>$message,
            'data' => $data,
        ];

        return new JsonResponse($responseData, $code);
    }
}