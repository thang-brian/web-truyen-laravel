<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\Favourite;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\SelectFields;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Models\Favourite;
use App\Constants\FavouriteConstants;

class AddStoryFavourite extends Mutation
{
    /**
     * @param Favourite $favourite
     */
    public function __construct(protected Favourite $favourite)
    {
    }

    protected $attributes = [
        'name' => 'addStoryFavourite',
        'description' => 'A mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('Favourite');
    }

    public function args(): array
    {
        return [
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
        $favouriteAdd = $this->favourite->updateOrCreate(
            [
                'user_id' => $userId,
                'story_id' => $args['story_id'],
            ],
            [
                'type' => FavouriteConstants::TYPE['FAVOURITE']
            ]
        );
        return $favouriteAdd;
    }
}
