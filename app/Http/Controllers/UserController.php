<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoginAttempt;
use Nette\Utils\DateTime;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = $this->allUsers($request->user()->id);

        return response([
            'data' => UserResource::collection($users)
        ]);
    }

    public function authUser(Request $request)
    {       
        $authUser = $request->user();
        $user = $this->oneUser($authUser->id);
        return response()->json(['data'=> new UserResource($user[0]) ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'address' => 'string',
            'phone_number' => 'string|max:11',
            'email' => 'required|string|unique:users,email',
            'user_role' => 'required|int',
            'password' => [
                'required',
                'confirmed',
                Password::min(5)
            ]
        ]);

        $data['role_id'] = $data['user_role'];
        $data['password'] = Hash::make($data['password']);
        unset($data['user_role']);

        $user = User::insert($data);

        $users = $this->allUsers($request->user()->id);

        return response([
            'message' => 'User added successfully!',
            'data' => UserResource::collection($users)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $resource = User::find($id);

        if (!$resource) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $user = $this->oneUser($id);

        return response([
            'data' => UserResource::collection([$user[0]])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateLogin(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            $manilaTimezone = 'Asia/Manila';
            $nowInManila = Carbon::now($manilaTimezone);

            // Update the last_login timestamp
            $user->last_login = $nowInManila;
            $user->save();
        }
       
        return response(200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'email' => 'required|string|unique:users,email,'.$id,
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string',
        ]);

        $resource = User::findOrFail($id);
        $resource->update($validatedData);
       
        $users = $this->allUsers($request->user()->id);

        if ($resource->wasChanged()) {
            return response()->json([
                'message' => 'Updated successfully!',
                'data' => UserResource::collection($users)
            ], 200);
        } else {
            return response()->json(['message' => 'Nothing change!'], 200);
        } 
    }

    public function profile(Request $request, string $id) 
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'email' => 'required|string|unique:users,email,'.$id,
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string'
        ]);

        if (isset($request['image_url'])) {
            $relativePath = $this->saveImage($request['image_url']);
            $validatedData['image_url'] = $relativePath;

            if ($user->image_url) {
                $absolutePath = public_path($user->image_url);
                File::delete($absolutePath);
            }
        }

        $user->update($validatedData);

        if ($user->wasChanged()) {
            return response()->json(['message' => 'Save changes!'], 200);
        } else {
            return response()->json(['message' => 'Nothing change!'], 200);
        }
    }

    public function changeStatus(Request $request, string $id){
        $resource = User::findOrFail($id);
        $resource->update([ 'status' => $request->status]);

        $user = User::find($id);

        // if ($user) {
        //     $login = LoginAttempt::where('email', $user->email)
        //         ->where('deleted_flag', '=', '0')
        //         ->first();

        //     if ($login) {
        //         $login->update(['deleted_flag' => '1']);
        //     } 
        // } 

        // Check if the update was successful
        if ($resource->wasChanged()) {
            return response()->json(['message' => 'Change status successfully!'], 200);
        } else {
            return response()->json(['message' => 'No changes made to the user account!'], 200);
        }
    }


    public function changePassword(Request $request, string $id) 
    {
        $validatedData = $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'confirmed',
                Password::min(5)
            ]
        ]);

        $user = User::findOrFail($id);

        // Verify the current password before updating
        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password does not match.'], 422);
        }  

        $user->update([
            'password' => Hash::make($validatedData['password'])
        ]);

        // Check if the update was successful
        if ($user->wasChanged()) {
            return response()->json(['message' => 'Password changed successfully'], 200);
        } else {
            return response()->json(['message' => 'No changes!'], 200);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = User::find($id);

        if (!$item) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data['deleted_flag'] = 1;
        $resource = User::findOrFail($id);
        $update = $resource->update($data);
        if($update){
            return response()->json(['message' => 'User deleted'], 200);

        }
        return response()->json(['message' => 'Error'], 200);

    }

    /**
     * Delete Permanently the specified resource from storage.
     */
    public function permanentlyDelete(string $id)
    {
        $item = User::find($id);

        if (!$item) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'User deleted'], 200);
    }

    private function allUsers($id){
        $users = User::query()
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.id', '!=', $id)
            ->where('users.deleted_flag', '!=', 1)
            ->orderByDesc('users.created_at')
            ->get(['users.*', 'roles.role as role']);

        return $users;
    }

    private function oneUser($id) {
        $user = User::query()
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.id', $id)
            ->where('users.deleted_flag', '!=', 1)
            ->get(['users.*', 'roles.role as role']);
        return $user;
    }

    private function saveImage($image)
    {

        // Check if image is a valid base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            // Take out the base64 encoded text without the MIME type
            $image = substr($image, strpos($image, ',') + 1);
            // Get file extension
            $type = strtolower($type[1]); // jpg, png, gif

            // Check if the file is an image
            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('Invalid image type');
            }

            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false) {
                throw new \Exception('base64_decode failed');
            }

        } else {
            throw new \Exception('Did not match data URI with image data');
        }


        $dir = 'images/avatars/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $image);

        return $relativePath;
    }


}
