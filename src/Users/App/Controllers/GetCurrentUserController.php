<?php

declare(strict_types=1);

namespace Lightit\Users\App\Controllers;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Container\Attributes\CurrentUser;

#[Group('Users')]
final readonly class GetCurrentUserController
{
    public function __invoke(
        #[CurrentUser] $user
    ): JsonResponse {
        return response()->json([
            'data' => $user,
        ]);
    }
}
