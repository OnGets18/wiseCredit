<?php
/**
 * Created by PhpStorm.
 * User: on
 * Date: 1/30/19
 * Time: 3:31 PM
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Middleware\AuthUser;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /***
     * Create new user.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {

        //Check if user exist.
        if (User::where('email', $request->get('email'))->exists()) {
            return response()->json(['status' => 'User Exists'], 400);
        }

        //create new user, password convert to hash.
        User::create([
            'email'    => $request->get('email'),
            'password' => Hash::make($request->get('password'))
        ]);
        return response()->json(['status' => 'Used Successfully Created'], 200);
    }

    /**
     * Login User
     *
     * If login successful, return token. else error message.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $user = User::where('email', $request->get('email'))->first();
        $userPassword = $user->password;
        $userPasswordPost = $request->get('password');

        //Check with hash the password of user.
        if (Hash::check($userPasswordPost, $userPassword)) {
            //Create new random token.
            $newToken = str_random(160);
            //Update the token in db.
            $this->updatedNewTokenUser($user, $newToken);
            //Return token to client.
            return response()->json(['token' => $newToken], 200);
        } else {
            //Return Error Message.
            return response()->json(['status' => 'Error Login'], 400);
        }
    }

    /**
     * Return User Details (if token valid!)
     *
     * @return mixed
     */
    public function account()
    {
        return AuthUser::getUser();
    }

    //x-www-from-urlencoded
    /***
     * Update user Details.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request){

        return AuthUser::getUser()->update(
            $request->all()
        ) ? response()->json('User Successful Updated',200)
            : response()->json('Error Updated',400);
    }

    /**
     * Delete user by id (if token valid!).
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id){

        if(User::find($id)->delete()){
            return response()->json('User Successful Deleted!',200);
        }
        else
            return response()->json('Error Delete',400);
    }


    /**
     * Update new token after login.
     *
     * @param User $user
     *
     * @param String $newToken
     */
    private function updatedNewTokenUser(User $user, String $newToken)
    {
        $user->update(['token' => $newToken]);
    }
}