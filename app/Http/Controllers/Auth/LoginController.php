<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\SlackBackupService;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider()
    {
        return Socialite::driver('slack')->setScopes(['channels:history', 'mpim:read', 'channels:read', 'groups:history', 'groups:read', 'im:history', 'im:read', 'links:read', 'mpim:history', 'users:read.email', 'users:read', 'usergroups:read', 'users.profile:read'])->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('slack')->user();
        $this->CreateAndLogin($user);
        return redirect()->route('home');
    }

    public function logout(\Illuminate\Http\Request $request)
    {
        /*$users = \App\User::whereId(auth()->id());
        if ($users->exists()) {
            $users->update(["token" => ""]);
        }*/

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect('/');
    }

    private function CreateAndLogin($user)
    {
        $currentUser = User::where('email', $user->getEmail());
        if (!$currentUser->exists()) {
            $user = User::Create([
                'id' => $user->getId(),
                'username' => $user->getName(),
                'real_name' => $user->real_name,
                'token' => $user->token,
                'email' => $user->getEmail(),
                'password' => Hash::make($user->getEmail()),
                'avatar' => $user->getAvatar(),
            ]);

            \Auth::login($user, true);
            $slack = new SlackBackupService();
            $slack->storeUsers();
            $slack->storeMainChannels();
            $slack->storePrivateChannels();
        } else {
            $token = $user->token;
            $user = $currentUser->first();
            $user->token = $token;
            $user->save();
            \Auth::login($user, true);
        }
    }
}
