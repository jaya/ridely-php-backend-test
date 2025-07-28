<?php

namespace App\Http\Criteria;

use Illuminate\Validation\Rule;

class Criteria
{
    public ?int $offset;
    public ?int $limit;
    public ?string $orderBy;
    public ?string $sortBy;
    public ?array $fields;

    public function __construct(array $data)
    {
        $this->offset = $data['offset'] ?? null;
        $this->limit = $data['limit'] ?? null;
        $this->orderBy = $data['order_by'] ?? 'created_at';
        $this->sortBy = $data['sort_by'] ?? 'desc';
        $this->fields = isset($data['fields'])
            ? array_filter(array_map('trim', explode(',', $data['fields'])))
            : null;
    }

    public function toArray(): array
    {
        return [
            'offset' => $this->offset,
            'limit' => $this->limit,
            'order_by' => $this->orderBy,
            'sort_by' => $this->sortBy,
            'fields' => $this->fields,
        ];
    }

    public function rules(): array
    {
        return [
            'offset' => 'nullable|integer|min:0',
            'limit' => 'nullable|integer|min:1|max:100',
            'order_by' => 'nullable|string|max:50',
            'sort_by' => [
                'nullable',
                Rule::in(['asc', 'desc']),
            ],
            'fields' => 'nullable|array',
//            'fields.*' => 'string|max:50'
        ];

    }
}