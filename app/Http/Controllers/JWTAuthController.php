<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|confirmed',
                'role' => 'required|string|max:12',
                'schedule_type' => 'required|string|max:20', 
                'morning_clock_in' => 'required|date_format:H:i', 
                'morning_clock_out' => 'required|date_format:H:i|after:morning_clock_in', 
                'afternoon_clock_in' => 'required|date_format:H:i',
                'afternoon_clock_out' => 'required|date_format:H:i|after:afternoon_clock_in',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'message' => $validator->errors()
                ], 400);
            }

            $user = User::createUser($request);

            if (!$user) {
                return response()->json([
                    'error' => 'User creation failed',
                    'message' => 'Failed to create the user. Please try again.'
                ], 400);
            }

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'data' => [
                    'user' => $user,
                    'token' => $token
                ],
                'message' => 'User successfully registered'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'role' => 'required|in:admin,user',
                'schedule_type' => 'required|string',
                'interval' => 'required|integer',
                'morning_clock_in' => 'required',
                'morning_clock_out' => 'required',
                'afternoon_clock_in' => 'required',
                'afternoon_clock_out' => 'required',
            ]);

            

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
            ]);

            $user->workSchedule()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'schedule_type' => $validated['schedule_type'],
                    'interval' => $validated['interval'],
                    'morning_clock_in' => $validated['morning_clock_in'],
                    'morning_clock_out' => $validated['morning_clock_out'],
                    'afternoon_clock_in' => $validated['afternoon_clock_in'],
                    'afternoon_clock_out' => $validated['afternoon_clock_out'],
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso',
                'data' => $user->load('workSchedule'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar o usuário',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        try {

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $user = auth()->user();

            $isAdmin = $user && $user->role === 'admin';

            return response()->json([
                'data' => [
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60,
                    'role' => $isAdmin
                ]
            ], 200);

        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to login'], 500);
        }
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out'], 204);
    }

    public function getUser($id)
    {
        try {
            $user = User::getUser($id);

            return response()->json([
                'success' => true,
                'data' => $user,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar o usuário.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllUsers(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);

            $users = User::getAllUsers($perPage);

            return response()->json([
                'success' => true,
                'data' => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar os usuários.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            $user->workSchedule()->delete();

            $user->attendanceRecords()->delete();

            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuário excluído com sucesso.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado.',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir o usuário.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
