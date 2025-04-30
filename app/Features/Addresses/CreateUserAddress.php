<?php

namespace App\Features\Addresses;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;

class CreateUserAddress
{
    /**
     * @OA\Post(
     *     path="/api/users/{userId}/addresses",
     *     summary="Create a new address for a user",
     *     tags={"Addresses"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"street","city","state","postal_code","country"},
     *             @OA\Property(property="street", type="string", example="123 Main St"),
     *             @OA\Property(property="city", type="string", example="New York"),
     *             @OA\Property(property="state", type="string", example="NY"),
     *             @OA\Property(property="postal_code", type="string", example="10001"),
     *             @OA\Property(property="country", type="string", example="USA"),
     *             @OA\Property(property="is_primary", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Address created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Address created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="street", type="string", example="123 Main St"),
     *                 @OA\Property(property="city", type="string", example="New York"),
     *                 @OA\Property(property="state", type="string", example="NY"),
     *                 @OA\Property(property="postal_code", type="string", example="10001"),
     *                 @OA\Property(property="country", type="string", example="USA"),
     *                 @OA\Property(property="is_primary", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function __invoke(Request $request, $userId): JsonResponse
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'street' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'postal_code' => 'required|string|max:20',
                'country' => 'required|string|max:100',
                'is_primary' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $data = $validator->validated();
            $data['user_id'] = $userId;

            // If this is set as primary address, reset other primary addresses
            if (isset($data['is_primary']) && $data['is_primary']) {
                Address::where('user_id', $userId)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            $address = Address::create($data);

            return response()->json([
                'message' => 'Address created successfully',
                'data' => $address,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create address',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}