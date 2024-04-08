<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
//use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();
        return response()->json($usuarios, 200);
    }

    public function users(Request $request)
    {
        $token = $request->token; // Obtener el token de la solicitud

        // Verificar y validar el token JWT
        // Ejemplo de verificación del token utilizando Tymon JWT-Auth
        try {
            if(isset($token)){
                $user = JWTAuth::toUser($token); // Verificar el token y obtener el usuario asociado
                // Lógica adicional para acceder a la base de datos una vez que el token se ha validado
                $usuarios = Usuario::all(); // Ejemplo de acceso a la base de datos utilizando Eloquent ORM
                return response()->json($usuarios, 200);
            }else{
                return response()->json(['error' => 'Se requiere token'], 401);
            }

        } catch (JWTException $e) {
            // Manejar el error de validación del token
            return response()->json(['error' => 'Token inválido'], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usuario' => 'required|unique:usuarios',
            'clave' => 'required',
            // Agregar validaciones para otros campos
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $usuario = new Usuario([
            'usuario' => $request->usuario,
            'clave' => md5($request->clave),
            'nivel' => 2,
            'cedula' => $request->cedula,
            'email' => $request->email,
            'activo' => 1,

        ]);
        $usuario->save();

        return response()->json(['message' => 'Usuario registrado con éxito'], 201);
    }

    public function login(Request $request)
    {
        $user = Usuario::where('usuario', $request->usuario)->first();

        if (!$user || $user->clave !== md5($request->clave)) {
            return response()->json(['error' => 'Credenciales inválidas '], 401);
        }

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'No se pudo crear el token'], 500);
        }

        return response()->json(['user' => $user,'token' => $token]);
    }


    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::parseToken());

        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }

    public function usuarios(Request $request)
    {
        $usuarios = Usuario::find($request->id);
        return response()->json($usuarios, 200);
    }
}
