<?php

declare(strict_types=1);

namespace App\Repositories\Product\Contracts;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryContract
{
    /**
     * @return Collection<int, Product>
     */
    public function getAllProducts(?string $search = null, ?int $limit = null): Collection;
}
