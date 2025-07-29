<?php

namespace App\Http\Hateos;

use Illuminate\Pagination\LengthAwarePaginator;

class HateosMeta
{
    public $offset = 0;

    public $limit = 0;
    public $previousPage = 1;
    public $currentPage = 1;

    public $nextPage = 1;
    public $lastPage = 1;
    public $total = 0;

    public $count = 0;

    public function __construct(LengthAwarePaginator $paginator)
    {
        $limit = $paginator->perPage();
        $total = $paginator->total();
        $count = $paginator->count();
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $previousPage = ($currentPage > 1) ? $currentPage -1 : null;
        $nextPage = ($currentPage < $lastPage) ? $currentPage +1 : null;

        $this->total = $total;
        $this->count = $count;
        $this->previousPage = $previousPage;
        $this->currentPage = $currentPage;
        $this->nextPage = $nextPage;
        $this->lastPage = $lastPage;
        $this->limit = $limit;
    }

    public function toArray(): array
    {
        return [
            "limit" => $this->limit,
            "total" => $this->total,
            "count" => $this->count,
            "previousPage" => $this->previousPage,
            "currentPage" => $this->currentPage,
            "nextPage" => $this->nextPage,
            "lastPage" => $this->lastPage,
        ];
    }
}