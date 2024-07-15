<?php

namespace App\Traits;

trait ApiResponse
{
  /**
   * send a success response
   * 
   * @param mixed $result The data to send in the response.
   * @param string $message The message to send in the response.
   * @param int $code The HTTP status code for the response.
   * @param bool $isPaginated Indicates whether the result is paginated.
   * @return \illuminate\Http\JsonResponse
   */

  protected function sendResponse($result, $message, $code = 200, $isPaginated = false)
  {
    $response = [
      'success' => true,
      'message' => $message
    ];

    if ($result instanceof \Illuminate\Pagination\LengthAwarePaginator) {
      $response['data'] = $result->items();
      $response['meta'] = [
        'total' => $result->total(),
        'count' => $result->count(),
        'per_page' => $result->perPage(),
        'current_page' => $result->currentPage(),
        'total_pages' => $result->lastpage()
      ];
    } else {
      $response['data'] = isset($result['data']) ? $result['data'] : $result;
    }

    return response()->json($response, $code);
  }

  /**
   * Send an error response.
   * 
   * @param string $error The error message.
   * @param array $errorMessages Additional error messages.
   * @param int $code The HTTP status code for the response.
   * @return \Illuminate\Http\JsonResponse
   */

  protected function sendError($error, $errorMessages = [], $code = 400)
  {
    $response = [
      'success' => false,
      'message' => $error
    ];

    if (!empty($errorMessages)) {
      $response['data'] = $errorMessages;
    }
    return response()->json($response, $code);
  }

  protected function formatErrorMessages($errorMessages)
  {
    $customErrorMessages = array_map(function ($message) {
      if (strpos($message, 'No query results for model') !== false) {
        return 'oh tidak bisa';
      }

      return $message;
    }, $errorMessages);

    return $customErrorMessages;
  }
}
