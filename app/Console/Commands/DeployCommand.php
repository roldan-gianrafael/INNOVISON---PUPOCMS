<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class DeployCommand extends Command
{
    protected $signature = 'lightsail:deploy {--force : Force migrations in production}';

    protected $description = 'Clear caches, run migrations, and rebuild production caches for Lightsail deployments.';

    public function handle(): int
    {
        $commands = [
            ['config:clear', []],
            ['route:clear', []],
            ['view:clear', []],
            ['cache:clear', []],
            ['migrate', $this->migrationOptions()],
            ['config:cache', []],
            ['route:cache', []],
            ['view:cache', []],
        ];

        try {
            foreach ($commands as [$command, $arguments]) {
                $this->line('Running: php artisan ' . $command);
                $exitCode = Artisan::call($command, $arguments);
                $this->output->write(Artisan::output());

                if ($exitCode !== self::SUCCESS) {
                    $this->error('Deployment stopped on command: ' . $command);

                    return self::FAILURE;
                }
            }
        } catch (Throwable $exception) {
            $this->error('Lightsail deployment failed: ' . $exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Lightsail deployment completed successfully.');

        return self::SUCCESS;
    }

    private function migrationOptions(): array
    {
        $options = [];

        if (app()->environment('production') || $this->option('force')) {
            $options['--force'] = true;
        }

        return $options;
    }
}
