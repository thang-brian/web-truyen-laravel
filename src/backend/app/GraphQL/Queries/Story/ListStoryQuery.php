<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Story;

use Closure;
use GraphQL\GraphQL as GraphQLGraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\Middleware\ResolvePage;
use App\Models\Story;

class ListStoryQuery extends Query
{
    protected $attributes = [
        'name' => 'listStory',
        'description' => 'A query'
    ];

    protected $middleware = [
        ResolvePage::class,
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Story');
    }

    public function args(): array
    {
        return [
            'keyword' => [
                'name' => 'keyword',
                'type' =>  Type::string(),
            ],
            'keywordContent' => [
                'name' => 'keywordContent',
                'type' =>  Type::string(),
            ],
            'category_id' => [
                'name' => 'category_id',
                'type' => Type::int(),
                'rules' => ['exists:categories,id']
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::int(),
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
            ],
            'limit' => [
                'name' => 'limit',
                'type' => Type::int(),
            ]
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $searchKeyword = function ($query) use ($args) {
            if (isset($args['keyword'])) {
                $query->where('title', 'like', '%' . $args['keyword'] . '%');
            }
            if (isset($args['keywordContent'])) {
                $query->where('content', 'like', '%' . $args['keywordContent'] . '%');
            }
            if (isset($args['type'])) {
                $query->where('type', $args['type']);
            }
            if (isset($args['category_id'])) {
                $query->whereHas('categories', function ($searchCate) use ($args) {
                    $searchCate->where('id', $args['category_id']);
                });
            }
        };
        return Story::where($searchKeyword)
            ->with('categories')
            ->paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
