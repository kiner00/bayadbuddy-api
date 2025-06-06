<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\PhoneNumberFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\TransientToken;
use Propaganistas\LaravelPhone\Rules\Phone;

class AuthController extends Controller
{
    protected $phoneFormatter;

    public function __construct(PhoneNumberFormatter $phoneFormatter)
    {
        $this->phoneFormatter = $phoneFormatter;
    }
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'mobile_number' => ['required', 'string', 'max:20', 'unique:users', new Phone('PH')],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'mobile_number' => $this->phoneFormatter->normalizeTo639($validated['mobile_number']),
            'password' => Hash::make($validated['password']),
            'subscription_status' => 'active',
            'sms_credits' => 30,
        ]);

        return response()->json([
            'message' => 'User registered successfully.',
            'data' => new UserResource($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'data' => new UserResource($user),
        ]);

    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token && !($token instanceof TransientToken)) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Successfully logged out.'
        ]);
    }
}
