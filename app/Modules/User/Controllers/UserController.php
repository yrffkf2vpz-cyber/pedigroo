<?php

namespace App\Modules\User\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\User\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        protected UserService $service
    ) {}

    /**
     * User lista (Super Admin vagy Breed Admin szuréssel).
     */
    public function index(Request $request)
    {
        $actor = $request->user();

        // Super Admin ? minden user
        if ($actor->super_admin) {
            return User::query()->paginate(20);
        }

        // Breed Admin ? csak a saját fajtáját látja
        if ($actor->breed_id) {
            return User::where('breed_id', $actor->breed_id)->paginate(20);
        }

        // Normál user ? semmit nem lát
        abort(403, 'Nincs jogosultság.');
    }

    /**
     * Egy user megtekintése.
     */
    public function show(Request $request, User $user)
    {
        $this->authorize('view', $user);

        return $user;
    }

    /**
     * User módosítása.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'roles' => 'sometimes|array',
        ]);

        $updated = $this->service->update($user, $validated);

        return response()->json($updated);
    }

    /**
     * Szerepkörök kezelése.
     */
    public function assignRoles(Request $request, User $user)
    {
        $this->authorize('assignRoles', $user);

        $validated = $request->validate([
            'roles' => 'required|array',
        ]);

        $this->service->syncRoles($user, $validated['roles']);

        return response()->json(['status' => 'ok']);
    }

    /**
     * User törlése (ha engedélyezett).
     */
    public function destroy(Request $request, User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json(['status' => 'deleted']);
    }
}
