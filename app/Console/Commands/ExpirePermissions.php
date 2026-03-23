<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Access\AccessPermission;
use App\Modules\AccessControl\Services\AuditLogService;
use App\Modules\AccessControl\Services\AccessTimelineBuilder;

class ExpirePermissions extends Command
{
    protected $signature = 'permissions:expire';
    protected $description = 'Expire old access permissions and log events';

    public function __construct(
        protected AuditLogService $audit,
        protected AccessTimelineBuilder $timeline
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $expired = AccessPermission::whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expired as $permission) {
            $request = $permission->request;

            // 1) Timeline
            $this->timeline->permissionExpired($permission);

            // 2) Audit log
            $this->audit->log(
                userId: $request->requester_user_id,
                kennelId: $request->kennel_id,
                dogId: $request->dog_id,
                action: 'permission_expired'
            );

            // 3) Delete permission
            $permission->delete();
        }

        return 0;
    }
}