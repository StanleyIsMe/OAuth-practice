<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Validator;
use Illuminate\Http\Request;
/**
 * Class AuthController
 * @package App\Http\Controllers\Auth
 */
class AuthController extends Controller
{

    /**
     * @var AuthService
     */
    private $authService;

    /**
     * AuthController constructor.
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|string|email',
                'password' => 'required|string|max:30|min:6',
                'name' => 'required|string|max:10'
            ]);

            $email = $request->input('email');
            $password = $request->input('password');
            $name = $request->input('name');

            $this->authService->register($email, $password, $name);
            $response = [
                'success' => true,
                'message' => '註冊成功',
                'value' => ''
            ];

            return response()->json($response);
        } catch(\Exception $e) {

            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'value' => ''
            ];
            return response()->json($response, 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|string|email',
                'password' => 'required|string|max:20|min:6',
            ]);

            $email = $request->input('email');
            $password = $request->input('password');

            $name = $this->authService->login($email, $password);

            $response = [
                'success' => true,
                'message' => '登錄成功',
                'value' => ['name' => $name]
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'value' => ''
            ];
            return response()->json($response, 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function facebookLogin(Request $request)
    {
        $this->validate($request, [
            'redirect_uri' => 'required_with:code',
            'code' => 'required_with:redirect_url'
        ]);

        try {
            if ($request->has('redirect_uri')) {
                // fb auth for using code to ask for access token and verify
                $this->authService->fbLogin($request->input('redirect_uri'), $request->input('code'));
            } else {
                if ($request->has('access_token')) {
                    // fb auth for using access token and verity
                    $this->authService->fbLoginByAccessToken($request->input('access_token'));
                }
            }
            return redirect('/');
        } catch (\Exception $e) {
            return redirect('/');
        }
    }
}
