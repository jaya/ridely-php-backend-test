<?php

namespace App\Http\Hateos;

use Illuminate\Pagination\LengthAwarePaginator;

class HateosMetadata
{
    protected HateosMeta $meta;

    protected HateosLinks $links;


    public function __construct(LengthAwarePaginator $paginator)
    {
        $this->meta = new HateosMeta($paginator);
        $this->links = new HateosLinks($paginator);
    }

    public function meta()
    {
        return $this->meta;
    }

    public function links()
    {
        return $this->links;
    }

}