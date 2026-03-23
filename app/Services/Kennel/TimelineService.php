<?php

namespace App\Services\Kennel;

use App\Models\Kennel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimelineService
{
    /**
     * Record kennel creation event
     */
    public function recordKennelCreated(Kennel $kennel, array $metadata = []): void
    {
        $this->logTimelineEvent('kennel_created', $kennel->id, [
            'name' => $kennel->name,
            'owner_id' => $kennel->owner_id,
            ...($metadata ?? []),
        ]);
    }

    /**
     * Record kennel updated event
     */
    public function recordKennelUpdated(Kennel $kennel, array $changes = []): void
    {
        $this->logTimelineEvent('kennel_updated', $kennel->id, [
            'changes' => $changes,
        ]);
    }

    /**
     * Record member added event
     */
    public function recordMemberAdded(int $kennelId, int $dogId, string $role = 'member'): void
    {
        $this->logTimelineEvent('member_added', $kennelId, [
            'dog_id' => $dogId,
            'role' => $role,
        ]);
    }

    /**
     * Record member removed event
     */
    public function recordMemberRemoved(int $kennelId, int $dogId, string $reason = null): void
    {
        $this->logTimelineEvent('member_removed', $kennelId, [
            'dog_id' => $dogId,
            'reason' => $reason,
        ]);
    }

    /**
     * Record kennel promoted from pending event
     */
    public function recordKennelPromoted(int $pendingKennelId, int $kennelId): void
    {
        $this->logTimelineEvent('kennel_promoted', $kennelId, [
            'source_pending_id' => $pendingKennelId,
        ]);
    }

    /**
     * Record kennel archived event
     */
    public function recordKennelArchived(int $kennelId, string $reason = null): void
    {
        $this->logTimelineEvent('kennel_archived', $kennelId, [
            'reason' => $reason,
        ]);
    }

    /**
     * Get full kennel timeline
     */
    public function getKennelTimeline(int $kennelId, int $limit = 100): array
    {
        return DB::table('pd_kennel_timeline')
            ->where('kennel_id', $kennelId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get timeline events for a specific date range
     */
    public function getTimelineByDateRange(int $kennelId, \DateTime $from, \DateTime $to): array
    {
        return DB::table('pd_kennel_timeline')
            ->where('kennel_id', $kennelId)
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
    }

    /**
     * Get timeline events by type
     */
    public function getTimelineByEventType(int $kennelId, string $eventType): array
    {
        return DB::table('pd_kennel_timeline')
            ->where('kennel_id', $kennelId)
            ->where('event_type', $eventType)
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
    }

    /**
     * Internal method to log timeline event
     */
    private function logTimelineEvent(string $eventType, int $kennelId, array $metadata = []): void
    {
        DB::table('pd_kennel_timeline')->insert([
            'kennel_id' => $kennelId,
            'event_type' => $eventType,
            'user_id' => Auth::id(),
            'metadata' => json_encode($metadata),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}