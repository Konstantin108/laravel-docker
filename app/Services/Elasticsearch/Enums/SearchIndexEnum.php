<?php

declare(strict_types=1);

namespace App\Services\Elasticsearch\Enums;

use App\Models\Contracts\SearchableContract;
use App\Services\Elasticsearch\Exceptions\SearchIndexException;
use Illuminate\Database\Eloquent\Model;

enum SearchIndexEnum: string
{
    case USERS = 'users';

    // case PRODUCTS = 'products';

    /**
     * @return class-string<Model&SearchableContract>
     *
     * @throws SearchIndexException
     */
    public function getModel(): string
    {
        return config('elasticsearch.search_index_models.'.$this->value)
            ?? throw SearchIndexException::doesNotExist($this->value);
    }

    /**
     * @return class-string
     *
     * @throws SearchIndexException
     */
    public function getModelService(): string
    {
        return config('elasticsearch.model_services.'.$this->value)
            ?? throw SearchIndexException::doesNotExist($this->value);
    }
}
