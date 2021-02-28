<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HistoriChat;
use App\Models\Chats;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return response()->json('OK');
    }

    public function letMessageWithPeople(Request $request)
    {
        $name = $request->search_name;
        $user = User::where('id','<>',\Auth::id())->where('name','like', '%' .$name. '%')->get();
        return response()->json(['data' => $user]);
    }

    // For People not message
    public function latestMessage()
    {
        $data = HistoriChat::join('users','users.id','=','histori_chats.another_user_id_chat')
        ->where('histori_chats.user_id_chat',\Auth::id())->get();
        return response()->json(['data' => $data ]);
    }

    public function countUnreadMessage($sender,$receiver)
    {
        $data = Chats::where('sender',$sender)
            ->where('receiver',$receiver)->where('is_read','0')->count();
        return response()->json(['data' => $data]);
    }

    // For Message
    public function getLatestMessage($sender,$receiver)
    {
        $data = Chats::where('sender',$sender)
            ->where('receiver',$receiver)
            ->orWhere('sender',$receiver)
            ->where('receiver',$sender)
            ->orderBy('created_at','DESC')
            ->first();
        return response()->json(['data' => $data]);
    }


    // User
    public function getUserById($id)
    {
        $data = User::find($id);
        return response()->json(['data' => $data]);
    }

    public function update(Request $request)
    {
        $user = User::find(\Auth::id());
        $user->name = $request->name;
        $user->email = $request->email;
        if($request->password != ""){
            $user->password = app('hash')->make($request->password);
        }
        if($request->photo != "default.jpg"){
            if($request->old_photo != "default.jpg"){
                File::delete(public_path().'/profil/'.$request->old_photo);
            }
            $decoded = base64_decode($request->photo);
            $exploded = getTypeFile($decoded);
            if(FindCharacter($exploded, 'jpeg'))
                $extension = 'jpg';
            else
                $extension = 'png';

            $fileName = \Illuminate\Support\Str::random(32).'.'.$extension;
            $path = public_path().'/profil/'.$fileName;
            file_put_contents($path,$decoded);
            $user->photo = $fileName;
        }
        $update = $user->save();
        return response()->json(['data' => 'success']);
    }

    public function updateUserOnline(Request $request)
    {
        $id = $request->iduser;
        $user = User::find($id);
        $user->login_at = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $update = $user->save();
        return response()->json('success');
    }

    public function upUserClose(Request $request)
    {
        $id = $request->iduser;
        $user = User::find($id);
        $user->login_at = null;
        $update = $user->save();
        return response()->json('success');
    }

    public function logout(Request $request)
    {
        $token = $request->token;
        $logout = \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->invalidate();
        return response()->json(['data' => 'success']);
    }
}