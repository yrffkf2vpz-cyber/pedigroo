<?php

namespace App\Modules\Invitation\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Invitation\Services\InvitationService;
use App\Modules\Invitation\Models\Invitation;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function __construct(
        protected InvitationService $service
    ) {}

    /**
     * Meghívók listázása (admin / breed admin).
     */
    public function index(Request $request)
    {
        $actor = $request->user();

        // Super Admin ? minden meghívó
        if ($actor->super_admin) {
            return Invitation::with('inviter', 'invitedUser')->paginate(20);
        }

        // Breed Admin ? csak a saját fajtájához tartozó meghívók
        if ($actor->breed_id) {
            return Invitation::whereHas('inviter', function ($q) use ($actor) {
                $q->where('breed_id', $actor->breed_id);
            })
            ->with('inviter', 'invitedUser')
            ->paginate(20);
        }

        abort(403, 'Nincs jogosultság.');
    }

    /**
     * Meghívó generálása (admin / breed admin / user saját készletébol).
     */
    public function generate(Request $request)
    {
        $actor = $request->user();

        $validated = $request->validate([
            'count' => 'sometimes|integer|min:1|max:10',
        ]);

        $count = $validated['count'] ?? 1;

        // Normál user ? csak a saját készletébol generálhat
        // (késobb: meghívó készlet ellenorzése)
        // Adminok ? korlátlanul generálhatnak
        $invitations = $this->service->generate($actor, $count);

        return response()->json([
            'status' => 'ok',
            'invitations' => $invitations,
        ]);
    }

    /**
     * Meghívó érvényesítése (nyilvános endpoint).
     * A meghívott rákattint az e-mailben lévo linkre.
     */
    public function validateToken(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        $invitation = $this->service->validateToken($validated['token']);

        return response()->json([
            'valid' => true,
            'inviter' => $invitation->inviter->only(['id', 'name']),
            'expires_at' => $invitation->expires_at,
        ]);
    }

    /**
     * Meghívó elfogadása ? regisztráció befejezése.
     */
    public function accept(Request $request)
    {
        $validated = $request->validate([
            'token'    => 'required|string',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = $this->service->acceptInvitation(
            $validated['token'],
            $validated
        );

        return response()->json([
            'status' => 'registered',
            'user'   => $user,
        ]);
    }
}
