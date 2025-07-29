<?php

namespace App\Http\Criteria;

use Illuminate\Validation\Rule;

class Criteria
{
    const PAGE = 1;
    const LIMIT = 15;

    public ?int $page;
    public ?int $limit;
    public ?string $orderBy = 'asc';
    public ?string $sortBy;
    public ?array $fields;
    public function __construct(array $data)
    {
        $page = $data['page'] ?? self::PAGE;
        if (intval($page) < 1) {$page = 1;}

        $this->page = $page;
        $this->limit = $data['limit'] ?? self::LIMIT;
        $this->orderBy = $data['order_by'] ?? 'created_at';
        $this->sortBy = $data['sort_by'] ?? 'asc';
        $this->fields = isset($data['fields'])
            ? array_filter(array_map('trim', is_array($data['fields']) ? $data['fields']: explode(',', $data['fields'])))
            : null;
    }

    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'limit' => $this->limit,
            'order_by' => $this->orderBy,
            'sort_by' => $this->sortBy,
            'fields' => $this->fields,
        ];
    }

    public function rules($validFields = []): array
    {
        return [
            'page' => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:100',
            'sort_by' => [
                'nullable',
                Rule::in(['asc', 'desc']),
            ],
            'fields' => 'nullable|array',
            'fields.*' => [
                'string',
                'max:50',
                Rule::in($validFields),
            ],
            'order_by' => [
                'nullable',
                'string',
                'max:50',
                Rule::in($validFields),
            ]
        ];

    }
}