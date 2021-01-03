<?php

namespace App\Http\Controllers;

use App\Http\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class UsersController extends Controller
{
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(Request $request)
    {
        $user = $this->userService->login($request->get('email'), $request->get('password'));
        if (!$user) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        return response()->json([
            'user' => $user
        ]);
    }

    public function store(Request $request)
    {
        try {
            return response()->json([
                'user' => $this->userService->store($request->all())
            ]);
        } catch (InvalidParameterException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Unknown error' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            return response()->json([
                'user' => $this->userService->update($id, $request->all())
            ]);
        } catch (InvalidParameterException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Unknown error' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $this->userService->destroy($id);
        return response()->json([
            'success' => true
        ]);
    }
}
