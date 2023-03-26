<?php
namespace App\Pagination;
use Illuminate\Pagination\LengthAwarePaginator;
class CustomPagination extends LengthAwarePaginator
{
    /**
     * Get the instance as an array.
     *
     * This can be structured however you want and overrides
     * the function in LengthAwarePaginator.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'data' => $this->items->toArray(),
            'meta' => [
                'current_page' => $this->currentPage(),
                'from' => $this->firstItem(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'to' => $this->lastItem(),
                'total' => $this->total(),
                // 'first_page_url' => $this->url(1),
                // 'last_page_url' => $this->url($this->lastPage()),
                // 'links' => $this->linkCollection()->toArray(),
                // 'next_page_url' => $this->nextPageUrl(),
                // 'path' => $this->path(),
                // 'prev_page_url' => $this->previousPageUrl(),
            ],
        ];
    }
}
