<?php

namespace App\Modules\Token\Services;

use App\Models\User;
use App\Modules\Token\Models\Token;
use App\Modules\Token\Models\TokenLoan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TokenService
{
    /**
     * Token jutalom adása (rendszer adja).
     */
    public function reward(User $user, int $amount, string $reason): Token
    {
        return Token::create([
            'user_id' => $user->id,
            'amount'  => $amount,
            'type'    => 'reward',
            'reason'  => $reason,
        ]);
    }

    /**
     * Token költése (pozitív amount, type=spend).
     */
    public function spend(User $user, int $amount, string $reason): Token
    {
        if ($this->balance($user) < $amount) {
            throw ValidationException::withMessages([
                'token' => 'Nincs elegendo token.',
            ]);
        }

        return Token::create([
            'user_id' => $user->id,
            'amount'  => $amount,
            'type'    => 'spend',
            'reason'  => $reason,
        ]);
    }

    /**
     * Token örökbeadása (végleges átadás: from ? to).
     */
    public function giveToken(User $from, User $to, int $amount): array
    {
        if ($from->id === $to->id) {
            throw ValidationException::withMessages([
                'token' => 'Magadnak nem adhatsz tokent.',
            ]);
        }

        if ($this->balance($from) < $amount) {
            throw ValidationException::withMessages([
                'token' => 'Nincs elegendo token az örökbeadáshoz.',
            ]);
        }

        return DB::transaction(function () use ($from, $to, $amount) {

            // Donor költ
            $spend = Token::create([
                'user_id' => $from->id,
                'amount'  => $amount,
                'type'    => 'spend',
                'reason'  => "Token given permanently to user #{$to->id}",
            ]);

            // Receiver jutalmat kap
            $reward = Token::create([
                'user_id' => $to->id,
                'amount'  => $amount,
                'type'    => 'reward',
                'reason'  => "Token received permanently from user #{$from->id}",
            ]);

            // (Késobb: reputációs esemény)

            return compact('spend', 'reward');
        });
    }

    /**
     * Token kölcsönadása (visszafizetendo).
     */
    public function loanToken(User $from, User $to, int $amount): TokenLoan
    {
        if ($from->id === $to->id) {
            throw ValidationException::withMessages([
                'loan' => 'Magadnak nem adhatsz kölcsön.',
            ]);
        }

        if ($this->balance($from) < $amount) {
            throw ValidationException::withMessages([
                'token' => 'Nincs elegendo token a kölcsönadásra.',
            ]);
        }

        return DB::transaction(function () use ($from, $to, $amount) {

            // Lender költ
            Token::create([
                'user_id' => $from->id,
                'amount'  => $amount,
                'type'    => 'spend',
                'reason'  => "Token loaned to user #{$to->id}",
            ]);

            // Borrower jutalmat kap
            Token::create([
                'user_id' => $to->id,
                'amount'  => $amount,
                'type'    => 'reward',
                'reason'  => "Token loan received from user #{$from->id}",
            ]);

            // Kölcsön nyilvántartása
            return TokenLoan::create([
                'lender_id'     => $from->id,
                'borrower_id'   => $to->id,
                'amount'        => $amount,
                'repaid_amount' => 0,
            ]);
        });
    }

    /**
     * Kölcsön visszafizetése (részben vagy egészben).
     */
    public function repayLoan(User $borrower, int $loanId, int $amount): TokenLoan
    {
        $loan = TokenLoan::findOrFail($loanId);

        if ($loan->borrower_id !== $borrower->id) {
            throw ValidationException::withMessages([
                'loan' => 'Nem a te kölcsönöd.',
            ]);
        }

        if ($loan->repaid_at !== null) {
            throw ValidationException::withMessages([
                'loan' => 'Ez a kölcsön már vissza lett fizetve.',
            ]);
        }

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'A visszafizetés összege legyen pozitív.',
            ]);
        }

        if ($this->balance($borrower) < $amount) {
            throw ValidationException::withMessages([
                'token' => 'Nincs elegendo token a visszafizetéshez.',
            ]);
        }

        if ($loan->repaid_amount + $amount > $loan->amount) {
            throw ValidationException::withMessages([
                'amount' => 'Nem fizethetsz vissza többet, mint a kölcsön összege.',
            ]);
        }

        return DB::transaction(function () use ($loan, $amount) {

            // Borrower költ
            Token::create([
                'user_id' => $loan->borrower_id,
                'amount'  => $amount,
                'type'    => 'spend',
                'reason'  => "Loan repayment to user #{$loan->lender_id}",
            ]);

            // Lender jutalmat kap
            Token::create([
                'user_id' => $loan->lender_id,
                'amount'  => $amount,
                'type'    => 'reward',
                'reason'  => "Loan repayment received from user #{$loan->borrower_id}",
            ]);

            // Kölcsön frissítése
            $loan->repaid_amount += $amount;

            if ($loan->repaid_amount >= $loan->amount) {
                $loan->repaid_at = now();
            }

            $loan->save();

            // (Késobb: reputációs esemény)

            return $loan;
        });
    }

    /**
     * Token egyenleg lekérése.
     */
    public function balance(User $user): int
    {
        return Token::balanceFor($user->id);
    }
}
