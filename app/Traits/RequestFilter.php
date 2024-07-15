<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

trait RequestFilter
{
  protected $defaultFilter = [];
  protected $boolFilterFields = [];
  protected $likeFilterFields = [];

  public function getLimit(): ?int
  {
    return request()->get('limit');
  }

  public function getPage(): ?int
  {
    // from datatables
    if (request()->get('start') !== null) {
      return request()->get('start') / $this->getLimit() + 1;
    }

    // from laravel
    return request()->get('page');
  }

  public function getCari(): ?string
  {
    return request()->get('cari');
  }

  public function getQuery(string $query): ?string
  {
    return request()->get($query);
  }

  public function setDefaultFilter(array $filter)
  {
    $this->defaultFilter = $filter;
  }

  public function filter($query, $filters = [])
  {

    if (empty($filters) && empty($this->defaultFilter)) {
      return $query;
    }

    foreach ($this->defaultFilter as $key => $value) {
      if (empty($filters[$key])) {
        $filters[$key] = $value;
      }
    }

    if (isset($this->defaultFilter['id_unit']) && $this->getIdUnitCache() === null) {
      $filters['id_unit'] = $this->defaultFilter['id_unit'];
    } else if (isset($filters['id_unit']) && $this->getIdUnitCache() !== null) {
      $filters['id_unit'] = $this->getIdUnitCache();
    }

    $tableName = DB::getTablePrefix() . $query->from;

    if (!empty($this->likeFilterFields)) {
      $query->where(function ($query) use ($tableName) {
        foreach ($this->likeFilterFields as $likeFilter) {
          if (strpos($likeFilter, '.') !== false) {
            $tableName = explode('.', $likeFilter)[0];
            $likeFilter = explode('.', $likeFilter)[1];
          }
          $query->orWhere($tableName . '.' . $likeFilter, 'like', '%' . $this->getCari() . '%');
        }
      });
    }

    foreach ($filters as $field => $value) {
      if (is_array($value)) {
        $query->whereIn($tableName . '.' . $field, $value);
      } else {
        $query->when($value, function ($query, $value) use ($tableName, $field) {
          if ($value !== 'all') {
            if (strpos($field, '.') !== false) {
              $tableName = explode('.', $field)[0];
              $field = explode('.', $field)[1];
            }
            return $query->where($tableName . '.' . $field, $value);
          }
        });
      }
    }


    return $query;
  }
}
