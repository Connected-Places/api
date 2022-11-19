<?php

declare(strict_types=1);

namespace App\Search\ElasticSearch;

use App\Models\Service;
use App\Models\Taxonomy;
use App\Models\Collection;
use App\Contracts\QueryBuilder;
use App\Search\SearchCriteriaQuery;

class CollectionCategoryQueryBuilder extends ElasticsearchQueryBuilder implements QueryBuilder
{
    public function __construct()
    {
        $this->esQuery = [
            'query' => [
                'function_score' => [
                    'query' => [
                        'bool' => [
                            'should' => [],
                            'filter' => [],
                        ],
                    ],
                    'functions' => [
                        [
                            'field_value_factor' => [
                                'field' => 'score',
                                'missing' => 1,
                                'modifier' => 'ln1p',
                            ],
                        ],
                    ],
                    'boost_mode' => 'multiply',
                ],
            ],
        ];

        $this->filterPath = 'query.function_score.query.bool.filter';
    }

    public function build(SearchCriteriaQuery $query, int $page = null, int $perPage = null): array
    {
        $page = page($page);
        $perPage = per_page($perPage);

        $this->applyFrom($page, $perPage);
        $this->applySize($perPage);
        $this->applyStatus(Service::STATUS_ACTIVE);
        $this->applyCategory($query->getCategories()[0]);

        return $this->esQuery;
    }

    protected function applyStatus(string $status): void
    {
        $this->addFilter('status', $status);
    }

    protected function applyCategory(string $categorySlug): void
    {
        $category = Collection::query()
            ->with('taxonomies')
            ->where('slug', '=', $categorySlug)
            ->first();

        $this->addFilter('collection_categories', $category->getAttribute('name'));

        $category->taxonomies->each(function (Taxonomy $taxonomy): void {
            $this->esQuery['query']['function_score']['query']['bool']['should'][] = [
                'term' => [
                    'taxonomy_categories.keyword' => $taxonomy->getAttribute('name'),
                ],
            ];
        });
    }
}
