<?php

declare(strict_types=1);

namespace App\Http\ApiData;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

/**
 * @template TData of Data
 */
class PaginatedApiData extends Data
{
    /**
     * @param  DataCollection<int, TData>  $items
     */
    public function __construct(
        public readonly int $total_items,
        public readonly int $total_pages,
        public readonly ?string $previous_page_url,
        public readonly ?string $next_page_url,
        /**
         * @var Collection<int, TData>
         */
        public readonly Collection $items,
    ) {}

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
            items: collect($paginator->items())->map(fn (mixed $item) => $dataClass::from($item)),
        );
    }
}
