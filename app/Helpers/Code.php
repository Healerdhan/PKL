<?php

namespace App\Helpers;

class Code
{
  public const SUCCESS = 200;
  public const ERROR = 400;
  public const POST_SUCCESS = 201;

  public const UNAUTHORIZED = 401;
  public const FORBIDDEN = 403;
  public const NOT_FOUND = 404;
  public const VALIDATION_ERROR = 422;
  public const SERVER_ERROR = 500;
  public const SERVICE_UNAVAILABLE = 503;
  public const METHOD_NOT_ALLOWED = 405;
  public const CONFLICT = 409;
  public const TOO_MANY_ATTEMPTS = 429;
}
