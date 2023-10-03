<?php

declare(strict_types=1);

namespace App\Http\ViewData;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Contracts\DataObject;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * @template TData of DataObject
 */
class PaginatedViewData extends Data
{
    /**
     * @param  DataCollection<int, TData>  $items
     */
    public function __construct(
        public readonly int $total_items,
        public readonly int $total_pages,
        public readonly ?string $previous_page_url,
        public readonly ?string $next_page_url,
        #[DataCollectionOf(DataObject::class)]
        public readonly DataCollection $items,
    ) {
    }

    /**
     * @param  class-string<TData>  $dataClass
     */
    public static function fromPaginator(LengthAwarePaginator $paginator, string $dataClass): self
    {
        return new self(
            total_items: $paginator->total(),
            total_pages: $paginator->lastPage(),
            previous_page_url: $paginator->previousPageUrl(),
            next_page_url: $paginator->nextPageUrl(),
            items: $dataClass::collection($paginator->items() ?? []),
        );
    }
}
