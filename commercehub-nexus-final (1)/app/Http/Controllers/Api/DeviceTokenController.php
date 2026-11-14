<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The mobile app (or website) sends its Firebase token here, so the server can push to this device.
 *   POST /api/device-tokens             after login, and whenever Firebase gives a new token
 *   POST /api/device-tokens/unregister  on logout
 * Routes need auth:sanctum. A user can only create and delete HIS OWN tokens.
 */
class DeviceTokenController extends Controller
{
    private const MAX_DEVICES_PER_USER = 10;

    public function store(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:512'],
            'platform' => ['required', Rule::in(DeviceToken::PLATFORMS)],
            'locale' => ['nullable', Rule::in(['ar', 'en'])],
            'app_version' => ['nullable', 'string', 'max:20'],
        ]);

        $user = $request->user();

        // Same token again (or the same phone with another account) => the row is moved to this user.
        DeviceToken::updateOrCreate(
            ['token' => $data['token']],
            [
                'user_id' => $user->id,
                'platform' => $data['platform'],
                'locale' => $data['locale'] ?? 'ar',
                'app_version' => $data['app_version'] ?? null,
                'last_used_at' => now(),
            ]
        );

        // Nobody can fill the table with tokens: only the newest 10 devices of the user are kept.
        $old = DeviceToken::where('user_id', $user->id)
            ->orderByDesc('last_used_at')
            ->skip(self::MAX_DEVICES_PER_USER)
            ->take(100)
            ->pluck('id');

        if ($old->isNotEmpty()) {
            DeviceToken::whereIn('id', $old)->delete();
        }

        return response()->json(['message' => 'ok']);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate(['token' => ['required', 'string', 'max:512']]);

        DeviceToken::where('user_id', $request->user()->id)->where('token', $data['token'])->delete();

        return response()->json(['message' => 'ok']);
    }
}
