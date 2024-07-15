<?php

namespace App\Exceptions;

use App\Helpers\Code;
use App\Helpers\Message;
use Exception;

class Error extends Exception
{
  private ?string $error;

  public function __construct($code = Code::ERROR, string $message = Message::error, ?string $error = null, Exception $previous = null)
  {
    $this->setError($error);
    parent::__construct($message, $code, $previous);
  }

  public function getError(): ?string
  {
    return $this->error;
  }

  public function setError(?string $error): void
  {
    $this->error = $error;
  }
}
