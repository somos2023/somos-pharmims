<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginAttempt;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|string|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()
            ]
        ]);

        /** @var \App\Models\User $user */
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);
        $token = $user->createToken('main')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function saveLoginAttempt(Request $request){
        

    }

    public function getLoginAttempt(Request $request){

        
    }

    public function login(Request $request)
    {
        if($request->role == 'admin'){
            $request['role'] = 1;
        } else if($request->role == 'staff'){
            $request['role'] = 2;
        } else {
            $request['role'] = 3;
        }
        
        $credentials = $request->validate([
            'email' => 'required|email|string|exists:users,email',
            'role' => 'required',
            'password' => [
                'required',
            ],
            'remember' => 'boolean'
        ]);
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);
       

        $login_attempt = LoginAttempt::query()
        ->join('users', 'login_attempts.email', '=', 'users.email')
        ->where('login_attempts.email', $request->email)
        ->whereDate('login_attempts.created_at', now()->toDateString()) 
        ->where('login_attempts.deleted_flag', '=', 0)
        ->where('login_attempts.count', '>=', 8)
        ->where('users.role_id', '!=', 1)
        ->latest()
        ->select('login_attempts.*') // Use 'select()' to specify the columns to retrieve
        ->first();

        if (!$login_attempt) {

            $la_resource = LoginAttempt::where('email', $request->email)
            ->whereDate('created_at', now()->toDateString())
            ->where('deleted_flag', '=', '0') // Adding condition to check for today's date
            ->latest()
            ->first();



            if($la_resource){
                $la_resource['disabled'] = $la_resource['disabled'] == 0 ? false : true;
                unset($la_resource['deleted_flag']);
                unset($la_resource['created_at']);
                unset($la_resource['updated_at']);
            } else {
                $la_resource = [
                    'email' => $request->email,
                    'disabled' => false,
                    'count' => 0,
                    'countdown' => 0,
                ];
            }

            $roleExists = User::where('email', $credentials['email'])
            ->where('role_id', $credentials['role'])
            ->exists();

            if (!$roleExists) {
                return response()->json([
                    'errors' => [
                    'error' => ['The selected email does not exist for the given role.']
                    ],
                    'message' => 'not exist',
                    'login_attempt' => $la_resource
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if ($user->status == 'locked') {
                return response()->json([
                    'errors' => [
                        '1' => ['System locked your account.'],
                        '2' => ['Please contact administrator.']
                    ], 
                    'message' => 'locked',
                    'login_attempt' => $la_resource
                ], 422);
            } 

            $deletedAccount = User::where('email', $credentials['email'])
            ->where('role_id', $credentials['role'])
            ->where('deleted_flag', 1)
            ->exists();

            if ($deletedAccount) {
                return response()->json([
                    'errors' => [
                    'error' => ['Your account was deleted.']
                    ],
                    'message' => 'deleted',
                    'login_attempt' => $la_resource
                ], 422);
            }

            unset($credentials['role']);

            if (!Auth::attempt($credentials, $remember)) {
                return response()->json([
                    'errors' => [
                    'error' => ['The Provided credentials are not correct']
                    ],
                    'message' => 'not match',
                    'login_attempt' => $la_resource
                ], 422);
            }

            $loginAttempts = LoginAttempt::where('email', $request->email)
                ->where('deleted_flag', '=', '0')
                ->get();

            if ($loginAttempts->isNotEmpty()) {
                LoginAttempt::where('email', $request->email)
                    ->where('deleted_flag', '=', '0')
                    ->update(['deleted_flag' => '1']);
            }


            $user = Auth::user();
            $token = $user->createToken('main')->plainTextToken;

            $user = $this->getUser($user->id);

            return response([
                'user' => UserResource::collection($user),
                'token' => $token
            ]);
        } else {

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            if ($user->status !== 'locked') {
                $user->update(['status' => 'locked']);

                $login = LoginAttempt::where('email', $user->email)
                    ->where('deleted_flag', '=', '0')
                    ->first();

                if ($login) {
                    $login->update(['deleted_flag' => '1']);
                }
            } 

            $la_resource = [
                'email' => $request->email,
                'disabled' => false,
                'count' => 0,
                'countdown' => 0,
            ];

            return response()->json([
                'errors' => [
                    '1' => ['System locked your account.'],
                    '2' => ['Please contact administrator.']
                ], 
                'message' => 'locked',
                'login_attempt' => $la_resource
            ], 422);
        }
    }

    public function logout()
    {
        /** @var User $user */
        $user = Auth::user();
        // Revoke the token that was used to authenticate the current request...
        $user->currentAccessToken()->delete();

        return response([
            'success' => true
        ]);
    }

    private function getUser($id) {
        $user = User::query()
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.id', $id) // Corrected: specify 'users.id'
            ->get(['users.*', 'roles.role as role']);
        return $user;
    }
}
