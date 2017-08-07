<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Transformers\UserTransformer;

class LoginController extends ApiController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use ThrottlesLogins;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected $userTransformer;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->maxAttempts = 4;
        $this->decayMinutes = 2;

        $this->userTransformer = new UserTransformer();
    }

    public function username()
    {
        return 'email';
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if($this->hasTooManyLoginAttempts($request))
            {
                $this->fireLockoutEvent($request);
                return $this->sendLockoutResponse($request);
            }
            if(! $token = auth()->guard()->attempt($credentials))
            {
                $this->incrementLoginAttempts($request);
                return $this->respondUnAuthenticated("User credentials are not correct!");
            }
        } catch(JWTException $ex) {
            return $this->respondInternalServerError();
        }

        return response()->json(compact('token'));
    }

    public static function currentUser()
    {
        return auth()->user();
    }

    public static function searchId($id)
    {
        return User::findOrFail($id);
    }

    public function searchUsername($username)
    {
        $user = User::where('username',$username)->first();
        return $this->respond(['data' => $this->userTransformer->transform($user)]);
    }

    public function searchName($name)
    {
        $user = User::where('name',$name)->first();
        return $this->respond(['data' => $this->userTransformer->transform($user)]);
    }

    public function searchEmail($email)
    {
        $user = User::where('email',$email)->first();
        return $this->respond(['data' => $this->userTransformer->transform($user)]);
    }

    public function destroy()
    {
        auth()->guard()->logout();
        return $this->respondSuccess("User logged out!");
    }

    public function changePassword(Request $request)
    {
        $user = LoginController::currentUser();

        if(password_verify($request->get('oldpassword'), $user->password))
        {
            $user->password = bcrypt($request->get('newpassword'));
            $user->save();
            return $this->respondSuccess("Password Changed Successfully!");
        }

        return $this->respondUnAuthenticated("Old password is not correct!");
    }

    public function avatar(Request $request)
    {
        $photo = $request->file('avatar');
        $ext = $photo->extension();
        return $photo->storeAs('avatars/'.LoginController::currentUser()->username, "avatar.{$ext}");
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $user = $this->findOrCreateGitHubUser(Socialite::driver('github')->user());

        $token = JWTAuth::fromUser($user);
        return response()->json(compact('token'));
    }

    public function findOrCreateGitHubUser($githubUser)
    {
        $user = User::firstOrNew(['github_id' => $githubUser->id]);

        if($user->exists)
            return $user;

        $user->fill([
            'username' => $githubUser->nickname,
            'email' => $githubUser->email,
            'name' => $githubUser->name
        ])->save();

        return $this->respond(['data' => $this->userTransformer->transform($user)]);
    }
}