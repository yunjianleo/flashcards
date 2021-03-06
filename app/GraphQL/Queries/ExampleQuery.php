<?php

namespace App\GraphQL\Queries;

use App\Facades\ExampleFacade;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\Validator;
use Nuwave\Lighthouse\Exceptions\ValidationException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class ExampleQuery
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $validator = Validator::make($args, [
            'phrase' => ['required', 'string', 'max:255'],
            'source' => ['required', 'string', 'exists:languages,locale'],
            'target' => ['required', 'string', 'exists:languages,locale'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $examples = ExampleFacade::example($args['phrase'], $args['source'], $args['target']);

        return $examples ?? [];
    }
}
