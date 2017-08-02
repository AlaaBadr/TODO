<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class LoginController extends Controller
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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->maxAttempts = 4;
        $this->decayMinutes = 2;
    }

    public function username()
    {
        return 'email';
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        //$credentials = $request->only('email', 'password');
        //return $credentials;
        try {
            if($this->hasTooManyLoginAttempts($request))
            {
                $this->fireLockoutEvent($request);
                return $this->sendLockoutResponse($request);
            }
            if(! $token = auth()->guard()->attempt($credentials))
            {
                $this->incrementLoginAttempts($request);
                return response()->json(['error' => 'User credentials are not correct!'], 401);
            }
        } catch(JWTException $ex) {
            return response()->json(['error' => 'Something went wrong!'], 500);
        }

        return response()->json(compact('token'));
    }

    public static function currentUser()
    {
        return auth()->user();
    }

    public static function searchId($id)
    {
        return User::find($id);
    }

    public static function searchUsername($username)
    {
        return User::where('username',$username)->first();
    }

    public function searchName($name)
    {
        return User::where('name',$name)->first();
    }

    public static function searchEmail($email)
    {
        return User::where('email',$email)->first();
    }

    public function destroy()
    {
        auth()->guard()->logout();
        return response()->json(['User logged out!'], 200);
    }

    public function changePassword(Request $request)
    {
        $user = LoginController::currentUser();

        if(password_verify($request->get('oldpassword'), $user->password))
        {
            $user->password = bcrypt($request->get('newpassword'));
            $user->save();
            return response()->json(['Password Changed Successfully!'], 200);
        }
        
        return response()->json(['error' => 'Old password is not correct!'], 401);
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

        return JWTAuth::fromUser($user);
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

        return $user;
    }
}