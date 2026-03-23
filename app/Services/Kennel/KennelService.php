<?php

namespace App\Services\Kennel;

use App\Models\Kennel;
use App\Models\KennelMember;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class KennelService
{
    private DataLifecycleService $dataLifecycle;
    private TimelineService $timeline;

    public function __construct(
        DataLifecycleService $dataLifecycle,
        TimelineService $timeline
    ) {
        $this->dataLifecycle = $dataLifecycle;
        $this->timeline = $timeline;
    }

    /**
     * Create a new kennel with initial members
     */
    public function createKennel(array $data, array $initialMembers = []): Kennel
    {
        return DB::transaction(function () use ($data, $initialMembers) {
            // 1. Create kennel
            $kennel = Kennel::create([
                'name' => $data['name'],
                'abbreviation' => $data['abbreviation'] ?? null,
                'owner_id' => $data['owner_id'],
                'country_id' => $data['country_id'] ?? null,
                'description' => $data['description'] ?? null,
                'founded_date' => $data['founded_date'] ?? now(),
                'status' => 'active',
            ]);

            // 2. Add initial members
            foreach ($initialMembers as $member) {
                $this->addMemberToKennel($kennel->id, $member['dog_id'], $member['role'] ?? 'member');
            }

            // 3. Record timeline
            $this->timeline->recordKennelCreated($kennel, [
                'initial_members_count' => count($initialMembers),
            ]);

            return $kennel;
        });
    }

    /**
     * Promote pending kennel to active with full audit
     */
    public function promotePendingKennelFull(int $pendingKennelId): Kennel
    {
        return DB::transaction(function () use ($pendingKennelId) {
            // 1. Promote via DataLifecycleService
            $kennel = $this->dataLifecycle->promotePendingKennel($pendingKennelId);

            // 2. Record in timeline
            $this->timeline->recordKennelPromoted($pendingKennelId, $kennel->id);

            return $kennel;
        });
    }

    /**
     * Add member to kennel
     */
    public function addMemberToKennel(int $kennelId, int $dogId, string $role = 'member'): KennelMember
    {
        return DB::transaction(function () use ($kennelId, $dogId, $role) {
            // 1. Check if member already exists
            $existing = KennelMember::where('kennel_id', $kennelId)
                ->where('dog_id', $dogId)
                ->whereNull('archived_at')
                ->first();

            if ($existing) {
                return $existing;
            }

            // 2. Add member
            $member = KennelMember::create([
                'kennel_id' => $kennelId,
                'dog_id' => $dogId,
                'role' => $role,
                'joined_date' => now(),
                'status' => 'active',
            ]);

            // 3. Record timeline
            $this->timeline->recordMemberAdded($kennelId, $dogId, $role);

            return $member;
        });
    }

    /**
     * Remove member from kennel
     */
    public function removeMemberFromKennel(int $kennelId, int $dogId, string $reason = null): bool
    {
        return DB::transaction(function () use ($kennelId, $dogId, $reason) {
            $member = KennelMember::where('kennel_id', $kennelId)
                ->where('dog_id', $dogId)
                ->whereNull('archived_at')
                ->first();

            if (!$member) {
                return false;
            }

            // 1. Archive member
            $member->update(['archived_at' => now()]);

            // 2. Record timeline
            $this->timeline->recordMemberRemoved($kennelId, $dogId, $reason);

            return true;
        });
    }

    /**
     * Get kennel with all members
     */
    public function getKennelFull(int $kennelId): ?Kennel
    {
        return Kennel::with('members', 'owner')
            ->where('id', $kennelId)
            ->whereNull('archived_at')
            ->first();
    }

    /**
     * Get all active members of a kennel
     */
    public function getActiveMembers(int $kennelId): Collection
    {
        return KennelMember::where('kennel_id', $kennelId)
            ->whereNull('archived_at')
            ->get();
    }

    /**
     * Update kennel details
     */
    public function updateKennel(int $kennelId, array $data): Kennel
    {
        return DB::transaction(function () use ($kennelId, $data) {
            $kennel = Kennel::findOrFail($kennelId);
            
            $changes = [];
            foreach ($data as $key => $value) {
                if ($kennel->{$key} !== $value) {
                    $changes[$key] = ['old' => $kennel->{$key}, 'new' => $value];
                }
            }

            if ($changes) {
                $kennel->update($data);
                $this->timeline->recordKennelUpdated($kennel, $changes);
            }

            return $kennel;
        });
    }

    /**
     * Archive kennel
     */
    public function archiveKennelFull(int $kennelId, string $reason = null): bool
    {
        return DB::transaction(function () use ($kennelId, $reason) {
            // 1. Archive via DataLifecycleService
            $this->dataLifecycle->archiveKennel($kennelId, $reason);

            // 2. Record in timeline
            $this->timeline->recordKennelArchived($kennelId, $reason);

            return true;
        });
    }

    /**
     * Get kennel timeline
     */
    public function getKennelTimeline(int $kennelId, int $limit = 50): array
    {
        return $this->timeline->getKennelTimeline($kennelId, $limit);
    }

    /**
     * Reject pending kennel
     */
    public function rejectPendingKennelFull(int $pendingKennelId, string $reason): bool
    {
        return DB::transaction(function () use ($pendingKennelId, $reason) {
            $this->dataLifecycle->rejectPendingKennel($pendingKennelId, $reason);
            // Timeline for rejection can be added separately if needed
            return true;
        });
    }
}