<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Chapter;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\Middleware\ResolvePage;
use App\Models\Chapter;

class ListChapterQuery extends Query
{
    protected $attributes = [
        'name' => 'listChapter',
        'description' => 'A query'
    ];

    protected $middleware = [
        ResolvePage::class,
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Chapter');
    }

    public function args(): array
    {
        return [
            'story_id' => [
                'name' => 'story_id',
                'type' => Type::int(),
                'rules' => ['exists:stories,id']
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
        return Chapter::paginate($args['limit'], ['*'], 'page', $args['page']);
    }
}
