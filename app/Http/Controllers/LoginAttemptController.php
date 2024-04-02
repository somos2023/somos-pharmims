<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginAttempt;
use App\Http\Requests\LoginAttemptRequest;

class LoginAttemptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|exists:users,email',
            'disabled' => 'required|boolean',
            'countdown' => 'required|numeric',
            'count' => 'required|numeric'
        ]);

        $validatedData['disabled'] = $validatedData['disabled'] == false ? 0 : 1;

        $login = new LoginAttempt();
        $login->fill($validatedData);
        $login->save();
        $id = $login->id; 
        
        $resource = LoginAttempt::find($id);

        if (!$resource) {
            return response()->json(['message' => 'not found'], 404);
        }

        unset($resource['deleted_flag']);
        unset($resource['created_at']);
        unset($resource['updated_at']);

        $resource['disabled'] = $resource['disabled'] == 0 ? false : true;
        return response()->json([
            'success' => true,
            'data' => $resource
        ]); 
    }

    /**
     * Display the specified resource.
     */
    public function show(string $email)
    {
        $resource = LoginAttempt::where('email', $email)
            ->whereDate('created_at', now()->toDateString())
            ->where('deleted_flag', '=', '0') // Adding condition to check for today's date
            ->latest()
            ->first();

        unset($resource['deleted_flag']);
        unset($resource['created_at']);
        unset($resource['updated_at']);

        if (!$resource) {
            return response()->json(200);
        } else {
            $resource['disabled'] = $resource['disabled'] == 0 ? false : true;
            return response([
                'success' => true,
                'data' => $resource
            ]);
        }
    }

    public function updateCount(Request $request, string $id) {
        $validatedData = $request->validate([
            'countdown' => 'required|numeric'
        ]);

        $login = LoginAttempt::findOrFail($id);
        $login->update($validatedData);

         return response(200);  
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'email' => 'exists:users,email',
            'disabled' => 'required|boolean',
            'countdown' => 'required|numeric',
            'count' => 'required|numeric'
        ]);

        unset($validatedData['countdown']);

        $validatedData['disabled'] = $validatedData['disabled'] == false ? 0 : 1;

        $login = LoginAttempt::findOrFail($id);
        $login->update($validatedData);

        return response()->json([
            'success' => true,
            'data' => $login
        ]); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = LoginAttempt::find($id);

        if (!$item) {
            return response()->json(['message' => 'Failed']);
        }

        $item->delete();

        return response()->json(200);
    }
}
