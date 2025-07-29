<?php

namespace App\Http\Hateos;

use Illuminate\Pagination\LengthAwarePaginator;

class HateosLinks
{
    public $self = "";

    public $next = "";

    public $previous = "";

    public $first = "";

    public $last = "";

    public function __construct(LengthAwarePaginator $paginator)
    {
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $self = $paginator->url($currentPage);
        $next = $paginator->nextPageUrl();
        $previous = $paginator->previousPageUrl();
        $first = $paginator->url(1);
        $last = $paginator->url($lastPage);

        $this->self = [
            "href" => $self,
            "rel" => HateosLinkHttpVerbsEnum::GET->rel(),
            "method" => HateosLinkHttpVerbsEnum::GET->method(),
        ];
        $this->next = [
            "href" => $next,
            "rel" => HateosLinkHttpVerbsEnum::GET->rel(),
            "method" => HateosLinkHttpVerbsEnum::GET->method(),
        ];
        $this->previous = [
            "href" => $previous,
            "rel" => HateosLinkHttpVerbsEnum::GET->rel(),
            "method" => HateosLinkHttpVerbsEnum::GET->method(),
        ];
        $this->first = [
            "href" => $first,
            "rel" => HateosLinkHttpVerbsEnum::GET->rel(),
            "method" => HateosLinkHttpVerbsEnum::GET->method(),
        ];
        $this->last = [
            "href" => $last,
            "rel" => HateosLinkHttpVerbsEnum::GET->rel(),
            "method" => HateosLinkHttpVerbsEnum::GET->method(),
        ];
    }

    public function toArray(): array
    {

        return [
            "self" => $this->self,
            "next" => $this->next,
            "previous" => $this->previous,
            "first" => $this->first,
            "last" => $this->last,
        ];
    }
}