<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     *  @OA\Post(
     *     path="/api/v1/user/register",
     *     summary="Creating a new user.",
     *     tags={"User"},
     *     security={{"bearerAuth": ""}},
     *
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="email", type="string"),
     *                      @OA\Property(property="password", type="string"),
     *                      @OA\Property(property="role", type="string"),
     *                 )
     *             },
     *              example= {
     *                      "name": "Peter",
     *                      "email": "peter@mail.com",
     *                      "password": "password",
     *                      "role": "inventory-manager"
     *                  }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="user", type="object",
     *                      @OA\Property(property="id", type="integer", example="1"),
     *                      @OA\Property(property="name", type="string", example="Peter"),
     *                      @OA\Property(property="email", type="string", example="peter@mail.com")
     *                  )
     *              ),
     *         )
     *     )
     * )
     *
     * @param RegisterUser $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function register(RegisterUser $request)
    {
        $this->authorize('create', User::class);

        $data = $request->only(['name', 'email', 'password', 'role']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);

        return response()->json(['data' => ['user' => $user]], 201);
    }
}
