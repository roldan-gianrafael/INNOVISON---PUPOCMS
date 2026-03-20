<?php

namespace App\Console\Commands;

use App\Services\FacultySyncService;
use Illuminate\Console\Command;
use Throwable;

class SyncFacultyProfiles extends Command
{
    protected $signature = 'faculty:sync';

    protected $description = 'Sync faculty profiles from the PUPT-FLSS API into the local admins table.';

    public function handle(FacultySyncService $facultySyncService): int
    {
        try {
            $result = $facultySyncService->sync();
        } catch (Throwable $exception) {
            $this->error('Faculty sync failed: ' . $exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Faculty sync completed successfully.');
        $this->line('Faculties fetched: ' . $result['fetched']);
        $this->line('Admins synced: ' . $result['synced']);

        return self::SUCCESS;
    }
}
