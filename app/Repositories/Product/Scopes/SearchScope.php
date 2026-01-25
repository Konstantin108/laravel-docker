<?php

declare(strict_types=1);

namespace App\Repositories\Product\Scopes;

use App\Conditions\SearchCondition;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

final readonly class SearchScope
{
    public function __construct(private ?string $search = null) {}

    /**
     * @param  Builder<Product>  $builder
     */
    public function __invoke(Builder $builder): void
    {
        if (! SearchCondition::isSatisfiedBy($this->search)) {
            return;
        }

        $search = '%'.$this->search.'%';

        $builder->where(function (Builder $builder) use ($search): void {
            $builder->where('name', 'like', $search)
                ->orWhere('description', 'like', $search)
                ->orWhere('price', 'like', $search);
        });
    }
}
