<?php

namespace App\Traits;

use App\Helpers\Message;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

trait PaginationResponse
{

  public $per_page;
  public $total_pages;
  public $total_data;
  public $items;
  public $queryData;

  public function paginate(
    mixed $items,
    ?int $perPage,
    ?int $page = 1,
    Model $model = null,
    array $options = []
  ): self {
    if (gettype($items) == 'array') {
      $currentPage = LengthAwarePaginator::resolveCurrentPage();
      if (is_null($perPage)) {
        $this->setQueryData($model ? $model->hydrate($items) : $items);
        return $this;
      }
      $collection = new Collection($items);
      $currentPageResults = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();
      $items['results'] = new LengthAwarePaginator($currentPageResults, count($collection), $perPage, $page, $options);
      $data = $items['results']->setPath(request()->url());

      $this->setQueryData($model ? $model->hydrate($data->items()) : $data->items());
      $this->setPerPage($data->perPage());
      $this->setTotalPages($data->lastPage());
      $this->setTotalData($data->total());
    } else if (gettype($items) == 'object') {
      if (is_null($perPage)) {
        $data = $items->get();
        $this->setQueryData($data);
        $this->setTotalData(count($data));
        return $this;
      }

      $total = $items->count();
      $data = $items->limit($perPage)
        ->skip(($page - 1) * $perPage)
        ->get();

      $this->setQueryData($data);
      $this->setPerPage($perPage);
      $this->setTotalPages(ceil($total / $perPage));
      $this->setTotalData($total);
    }

    return $this;
  }

  public function setPerPage(int $per_page): self
  {
    $this->per_page = $per_page;
    return $this;
  }

  public function setTotalPages(int $total_pages): self
  {
    $this->total_pages = $total_pages;
    return $this;
  }

  public function setTotalData(int $total_data): self
  {
    $this->total_data = $total_data;
    return $this;
  }

  public function set(array $data): void
  {
    $this->items[] = $data;
  }

  public function setArray(array $data): self
  {
    $this->items = $data;
    return $this;
  }

  public function get(): array
  {
    return $this->items;
  }

  public function setQueryData(Collection | array | SupportCollection $data): self
  {
    $this->queryData = $data;
    return $this;
  }

  public function getQueryData(): Collection | array | SupportCollection
  {
    return $this->queryData;
  }

  public function toArray(int $code, ?string $message = null): array
  {
    return [
      'success' => true,
      'code' => $code,
      'message' => $message ?? Message::success,
      'data' => $this->items,
      'per_page' => $this->per_page,
      'total_pages' => $this->total_pages,
      'total_data' => $this->total_data
    ];
  }
}
