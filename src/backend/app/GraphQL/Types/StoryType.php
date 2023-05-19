<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Models\Story;

class StoryType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Story',
        'description' => 'A type',
        'model' => Story::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of story'
            ],
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Title of the story'
            ],
            'avatar' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Avatar of the story'
            ],
            'author' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Author of the story'
            ],
            'user_id' => [
                'type' => Type::int(),
                'description' => 'Id user of the story'
            ],
            'total_chapters' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Total number of chapters'
            ],
            'type' => [
                'type' => Type::nonNull(Type::int())
            ],
            'content' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Content of story'
            ],
            'categories' => [
                'type' => Type::listOf(GraphQL::type('Category')),
                'description' => 'List of categories'
            ],
            'chapters' => [
                'type' => Type::listOf(GraphQL::type('Chapter')),
                'description' => 'List of chapters'
            ]
        ];
    }
}
