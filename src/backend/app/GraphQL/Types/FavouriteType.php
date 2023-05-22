<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL; 
use GraphQL\Type\Definition\Type;
use App\Models\Favourite;

class FavouriteType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Favourite',
        'description' => 'A type',
        'model' => Favourite::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'description' => 'ID of favourite',
            ],
            'user_id' => [
                'type' => Type::int(),
                'description' => 'ID of user'
            ],
            'story_id' => [
                'type' => Type::int(),
                'description' => 'ID of story'
            ],
            'chapter_id' => [
                'type' => Type::int(),
                'description' => 'ID of chapter'
            ],
            'type' => [
                'type' => Type::int(),
                'description' => 'Type of favorite'
            ],
            'story' => [
                'type' => GraphQL::type('Story'),
            ]
        ];
    }
}
