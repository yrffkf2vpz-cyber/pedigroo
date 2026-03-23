<?php

namespace App\Modules\Device\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Device\Models\UserDevice;
use App\Modules\Device\Services\DeviceService;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function __construct(
        protected DeviceService $service
    ) {}

    /**
     * Detect device or request verification.
     * Called when user logs in or opens the app from a device.
     */
    public function detect(Request $request)
    {
        $validated = $request->validate([
            'fingerprint' => 'required|string|max:255',
            'device_name' => 'nullable|string|max:255',
        ]);

        $userId = auth()->id();

        $result = $this->service->detectOrRequestVerification(
            userId: $userId,
            fingerprint: $validated['fingerprint'],
            deviceName: $validated['device_name'] ?? null
        );

        return response()->json($result);
    }

    /**
     * Verify the code and add the new device.
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'fingerprint' => 'required|string|max:255',
            'device_name' => 'nullable|string|max:255',
            'code' => 'required|string|max:10',
        ]);

        $userId = auth()->id();

        $device = $this->service->verifyCodeAndAddDevice(
            userId: $userId,
            fingerprint: $validated['fingerprint'],
            deviceName: $validated['device_name'] ?? null,
            code: $validated['code']
        );

        return response()->json([
            'status' => 'device_added',
            'device' => $device,
        ]);
    }

    /**
     * List all devices for the authenticated user.
     */
    public function list()
    {
        $userId = auth()->id();

        $devices = UserDevice::where('user_id', $userId)
            ->orderByDesc('is_default')
            ->orderByDesc('last_used_at')
            ->get();

        return response()->json($devices);
    }

    /**
     * Set a device as default.
     */
    public function setDefault(int $deviceId)
    {
        $userId = auth()->id();

        $this->service->setDefaultDevice(
            userId: $userId,
            deviceId: $deviceId
        );

        return response()->json(['status' => 'default_set']);
    }

    /**
     * Delete a device.
     */
    public function delete(int $deviceId)
    {
        $userId = auth()->id();

        $this->service->deleteDevice(
            userId: $userId,
            deviceId: $deviceId
        );

        return response()->json(['status' => 'device_deleted']);
    }
}
