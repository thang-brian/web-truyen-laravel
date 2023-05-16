<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Chapter;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Models\Chapter;

class DetailChapterQuery extends Query
{
    protected $attributes = [
        'name' => 'detailChapter',
        'description' => 'A query'
    ];

    public function type(): Type
    {
        return GraphQL::type('Chapter');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
                'rules' => ['required', 'exists:chapters,id']
            ]
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Chapter::findOrFail($args['id']);
    }
}
