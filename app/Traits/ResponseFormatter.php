<?php

namespace App\Traits;

use App\Exceptions\Error;
use App\Helpers\Code;
use App\Helpers\Message;
use Throwable;

trait ResponseFormatter
{
  // use LogActivity;

  private bool $success;
  private int $code;
  private string $message;
  private ?string $error = null;
  private mixed $data = null;
  private ?int $perPage = null;
  private ?int $totalPage = null;
  private ?int $totalData = null;

  private function checkResponseCode(int|string $code): int
  {
    if ($code < 100 || $code > 599 || gettype($code) == 'string') {
      return Code::SERVICE_UNAVAILABLE;
    } else {
      return $code;
    }
  }

  public function response(): array
  {
    return [
      'success' => $this->getSuccess(),
      'code' => $this->getCode(),
      'message' => $this->getMessage(),
      'error' => $this->getError(),
      'data' => $this->getData(),
    ];
  }

  public function pagination(): array
  {
    return [
      'success' => $this->getSuccess(),
      'code' => $this->getCode(),
      'message' => $this->getMessage(),
      'error' => $this->getError(),
      'data' => $this->getData(),
      'per_page' => $this->perPage,
      'total_pages' => $this->totalPage,
      'total_data' => $this->totalData,
    ];
  }

  public function success(int|string $code, mixed $data, ?string $message = null): array
  {
    // $this->addToLog($message ?? Message::success, 'Success', request());
    $this->setSuccess(true);
    $this->setCode($this->checkResponseCode($code));
    $this->setData($data);
    $this->setMessage($message ?? Message::success);

    return $this->response();
  }

  public function paginateResponse(int|string $code, array $data, ?string $message = null): array
  {
    // $this->addToLog($message ?? Message::success, 'Success', request());
    $this->setSuccess(true);
    $this->setCode($this->checkResponseCode($code));
    $this->setData($data['data']);
    $this->setMessage($message ?? Message::success);
    $this->setPerPageResponse($data['per_page']);
    $this->setTotalPageResponse($data['total_pages']);
    $this->setTotalDataResponse($data['total_data']);

    return $this->pagination();
  }

  public function setTotalPageResponse(?int $totalPage): ?int
  {
    return $this->totalPage = $totalPage;
  }

  public function setTotalDataResponse(?int $totalData): ?int
  {
    return $this->totalData = $totalData;
  }

  public function setPerPageResponse(?int $perPage): ?int
  {
    return $this->perPage = $perPage;
  }

  public function error(Error|Throwable $error, bool $trace = true)
  {
    $this->setSuccess(false);
    $this->setCode($this->checkResponseCode($error->getCode()));
    $this->setData($this->getTrace($error, $trace));

    if ($error instanceof Error) {
      $this->setMessage($error->getMessage());
      $this->setError($error->getError());
    } else {
      $this->setMessage(Message::internalServerError);
      $this->setError($error->getMessage());
    }

    // $this->addToLog($this->getError() ?? $this->getMessage(), 'Error', request());
    return $this->response();
  }

  public function getTrace(Error|Throwable $data, bool $trace): mixed
  {
    if (!config('app.debug') || !$trace || $data instanceof Error) {
      return null;
    }

    return $data->getTrace();
  }

  public function setSuccess(bool $success): void
  {
    $this->success = $success;
  }

  public function setCode(int $code): void
  {
    $this->code = $code;
  }

  public function setMessage(string $message): void
  {
    $this->message = $message;
  }

  public function setError(?string $error): void
  {
    $this->error = $error;
  }

  public function setData(mixed $data): void
  {
    if (is_array($data)) {
      $this->data = collect($data);
    } else {
      $this->data = $data;
    }
  }

  public function getSuccess(): bool
  {
    return $this->success;
  }

  public function getCode(): int
  {
    return $this->code;
  }

  public function getMessage(): string
  {
    return $this->message;
  }

  public function getError(): ?string
  {
    return $this->error;
  }

  public function getData(): mixed
  {
    return $this->data;
  }
}
