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
use App\Models\Story;

class DetailStoryQuery extends Query
{
    /**
     * @param Story $story
     */
    public function __construct(protected Story $story)
    {
    }

    protected $attributes = [
        'name' => 'detailStory',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::type('Story');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
                'rules' => ['required', 'exists:stories,id']
            ]
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->story->with('categories')->findOrFail($args['id']);
    }
}
