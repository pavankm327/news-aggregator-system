<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    
    /**
     * Return a successful response with the provided data and message.
     *
     * @param mixed $result The data to be returned in the response.
     * @param string $message The message to be included in the response.
     * @return \Illuminate\Http\JsonResponse The JSON response with success status.
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $result,
        ];
  
        return response()->json($response, 200);
    }

    /**
     * Return an error response with the provided message, data, and error code.
     *
     * @param string $message The error message to be included in the response.
     * @param mixed $data Additional data (optional) to be included in the error response.
     * @param int $code The HTTP status code for the error (default is 404).
     * @return \Illuminate\Http\JsonResponse The JSON error response.
     */
    public function sendError($message, $data = [], $code = 404)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
