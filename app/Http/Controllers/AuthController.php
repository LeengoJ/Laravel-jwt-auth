<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            // return response()->json($validator->errors(), 422);
            return json_encode(Response::error(Response::CVTM($validator)));
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return json_encode(Response::error(Response::CVTM($validator)));
        }
        // return $this->createNewToken($token);
            return json_encode(Response::success($token,"Dang nhap thanh cong"));

    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'sdt' => 'required|numeric',
            'role' => 'required|string'
        ]);
        if($validator->fails()){
            // return response()->json($validator->errors()->toArray(), 400);
            return json_encode(Response::error(Response::CVTM($validator)));
            // return response()->json(Response::error(Response::CVTM($validator)));
            // return "sai";
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
        return response()->json(Response::success(Response::CVTM($validator)));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(Response::success([],"User successfully signed out"));

    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        // return response()->json(auth()->user());
        return json_encode(Response::success(auth()->user(),"Da lay duoc thong tin"));

    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
    //
    public function searchUsers($search, $filter) {
        $users = User::find();

        $result = array_filter($users, function ($user) use ($search, $filter) {
            switch ($filter) {
                case 'name':
                    return stripos($user['name'], $search) !== false;
                case 'sdt':
                    return str_contains($user['sdt'], $search);
                default:
                    return false;
            }
        });

        // Giả định rằng bạn có một phương thức Response::success để tạo một HTTP response
        // Thay thế này bằng cách bạn thích để tạo một response nếu cần thiết
        // return Response::success("success", $result);
        return response()->json(Response::success($result,"success"));

    }
    function getAllUsers() {
        $user = User::find();
        return response()->json(Response::success($user,"success"));
    }
}
