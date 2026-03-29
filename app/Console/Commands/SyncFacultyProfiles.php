<?php

namespace App\Console\Commands;

use App\Services\FacultySyncService;
use Illuminate\Console\Command;
use Throwable;

class SyncFacultyProfiles extends Command
{
    protected $signature = 'faculty:sync';

    protected $description = 'Fetch faculty profiles from the PUPT-FLSS API to verify external faculty access.';

    public function handle(FacultySyncService $facultySyncService): int
    {
        try {
            $result = $facultySyncService->sync();
        } catch (Throwable $exception) {
            $this->error('Faculty sync failed: ' . $exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Faculty fetch completed successfully.');
        $this->line('Faculties fetched: ' . $result['fetched']);
        $this->line('Local admins synced: ' . $result['synced']);

        return self::SUCCESS;
    }
}
