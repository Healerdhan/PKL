<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

trait Validation
{
  abstract public function validate(): void;

  public bool $is_valid = true;
  public string $error = '';
  public int $code;

  public function validateData(array $data, array $rules, ?array $messages = []): self
  {
    $validator = Validator::make($data, $rules, $messages);

    if ($validator->fails()) {
      $this->setValidatorFails(false, Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors()->first());
    }

    return $this;
  }

  public function isValid(): bool
  {
    return $this->is_valid;
  }

  public function getCode(): int
  {
    return $this->code;
  }

  public function getError(): string
  {
    return $this->error;
  }

  public function setValidatorFails(bool $is_valid, int $code, string $error): void
  {
    $this->is_valid = $is_valid;
    $this->error = $error;
    $this->code = $code;
  }
}
