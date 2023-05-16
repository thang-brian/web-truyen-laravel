<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;
use App\Models\Chapter;

class ChapterType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Chapter',
        'description' => 'A type',
        'model' => Chapter::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'description' => 'ID of chapter'
            ],
            'title' => [
                'type' => Type::string(),
                'description' => 'Title of the chapter'
            ],
            'content' => [
                'type' => Type::string(),
                'description' => 'Content of the chapter'
            ],
            'story_id' => [
                'type' => Type::int(),
                'description' => 'Id story of the chapter'
            ],
            'number_order' => [
                'type' => Type::int(),
                'description' => 'Number order of the chapter'
            ]
        ];
    }
}
