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
use App\Models\Favourite;

class DetailChapterQuery extends Query
{
    /**
     * @param Chapter $chapter
     * @param Favourite $favourite
     */
    public function __construct(
        protected Chapter $chapter,
        protected Favourite $favourite
    ) {
    }

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
            ],
            'story_id' => [
                'name' => 'story_id',
                'type' => Type::int(),
                'rules' => ['required', 'exists:stories,id']
            ]
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $userId = auth()->user()->id ?? 1;
        $this->favourite->updateOrCreate(
            [
                'user_id' => $userId,
                'story_id' => $args['story_id'],
            ],
            [
                'chapter_id' => $args['id'],
            ]
        );

        return $this->chapter->findOrFail($args['id']);
    }
}
