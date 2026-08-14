<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeployCommand extends Command
{
    protected $signature = 'deploy';

    protected $description = 'Run migrations and seed demo data on first deploy';

    public function handle(): void
    {
        $this->call('migrate', ['--force' => true]);

        if (User::count() === 0) {
            $this->call('db:seed', ['--force' => true]);
            $this->info('Demo data seeded successfully.');
        } else {
            $this->info('Data already present, skipping seed.');
        }
    }
}