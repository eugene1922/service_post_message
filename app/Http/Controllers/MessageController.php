<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SendMessageRequest;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function send(SendMessageRequest $request)
    {
    	$id = $request->input('user_id');
    	$message = $request->input('message');

    	try {
    		$success = DB::table("user_messages")->insert(
	    		[
	    			'user_id' => $id,
	    			'message' => $message
	    		]
	    	);

	    	$result = ['success' => $success];
    	} catch (Illuminate\Database\QueryException $e) {
    		Log::error('MessageController::send error.', ['error' => $e->getMessage()]);

    		$result = ['error' => 'Something went wrong'];
    	}

    	return response()->json($result);
    }
}
