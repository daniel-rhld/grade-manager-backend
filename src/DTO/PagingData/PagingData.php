<?php

namespace App\DTO\PagingData;

class PagingData
{
    public int $page;
    public readonly int $perPage;
    public readonly int $totalItems;
    public readonly int $totalPages;
    public array $data;

    public function __construct(
        int $page,
        int $perPage,
        int $totalItems,
        int $totalPages,
        array $data
    )
    {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->totalItems = $totalItems;
        $this->totalPages = $totalPages;
        $this->data = $data;
    }

    public function toJson(): array
    {
        return [
            'page' => $this->page,
            'per_page' => $this->perPage,
            'total_items' => $this->totalItems,
            'total_pages' => $this->totalPages,
            'data' => array_map(
                callback: static fn ($elem) => $elem->toJson(),
                array: $this->data
            )
        ];
    }

}