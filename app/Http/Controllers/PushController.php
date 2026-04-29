<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->user()->updatePushSubscription(
            $request->endpoint,
            $request->publicKey,
            $request->authToken,
            $request->contentEncoding
        );

        return response()->json(['success' => true]);
    }

    public function unsubscribe(Request $request)
    {
        $request->user()->deletePushSubscription($request->endpoint);
        return response()->json(['success' => true]);
    }
}
