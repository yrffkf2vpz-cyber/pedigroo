<?php

namespace App\Modules\Token\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Token\Services\TokenService;
use App\Modules\Token\Models\Token;
use App\Modules\Token\Models\TokenLoan;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function __construct(
        protected TokenService $service
    ) {}

    /**
     * Token egyenleg lekérése.
     */
    public function balance(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'balance' => $this->service->balance($user),
        ]);
    }

    /**
     * Token tranzakciók listázása.
     */
    public function history(Request $request)
    {
        $user = $request->user();

        $transactions = Token::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($transactions);
    }

    /**
     * Token örökbeadása (végleges átadás).
     */
    public function giveToken(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'amount'     => 'required|integer|min:1',
        ]);

        $to = \App\Models\User::findOrFail($validated['to_user_id']);

        $result = $this->service->giveToken($user, $to, $validated['amount']);

        return response()->json([
            'status' => 'ok',
            'action' => 'token_given',
            'data'   => $result,
        ]);
    }

    /**
     * Token kölcsönadása.
     */
    public function loanToken(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'amount'     => 'required|integer|min:1',
        ]);

        $to = \App\Models\User::findOrFail($validated['to_user_id']);

        $loan = $this->service->loanToken($user, $to, $validated['amount']);

        return response()->json([
            'status' => 'ok',
            'action' => 'loan_created',
            'loan'   => $loan,
        ]);
    }

    /**
     * Kölcsön visszafizetése.
     */
    public function repayLoan(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'loan_id' => 'required|exists:token_loans,id',
            'amount'  => 'required|integer|min:1',
        ]);

        $loan = $this->service->repayLoan(
            $user,
            $validated['loan_id'],
            $validated['amount']
        );

        return response()->json([
            'status' => 'ok',
            'action' => 'loan_repaid',
            'loan'   => $loan,
        ]);
    }
}
