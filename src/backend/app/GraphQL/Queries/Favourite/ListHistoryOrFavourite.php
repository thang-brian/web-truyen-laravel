<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\Favourite;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\SelectFields;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\Middleware\ResolvePage;
use App\Constants\FavouriteConstants;
use App\Models\Favourite;

class ListHistoryOrFavourite extends Query
{
    /**
     * @param Favourite $favourite
     */
    public function __construct(protected Favourite $favourite)
    {
    }

    protected $attributes = [
        'name' => 'listHistoryOrFavourite',
        'description' => 'A query'
    ];

    protected $middleware = [
        ResolvePage::class,
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Favourite');
    }

    public function args(): array
    {
        return [
            'type_list' => [
                'name' => 'type_list',
                'type' => type::string(),
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
        $userId = auth()->user()->id ?? 1;

        $searchKeyword = function ($query) use ($args) {
            if (isset($args['type_list'])) {
                switch ($args['type_list']) {
                    case FavouriteConstants::TYPE_LIST['FAVOURITE']:
                        $query->where('type', FavouriteConstants::TYPE['FAVOURITE']);
                        break;
                    default:
                        $query->whereNotNull('chapter_id');
                        break;
                }
            }
        };

        return $this->favourite
            ->where('user_id', $userId)
            ->where($searchKeyword)
            ->with('story')
            ->paginate($args['limit'] ?? 5, ['*'], 'page', $args['page'] ?? 1);
    }
}
