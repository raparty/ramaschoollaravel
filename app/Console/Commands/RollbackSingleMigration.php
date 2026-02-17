<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RollbackSingleMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:rollback-single {migration : The migration file name} {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback a single migration without affecting its batch';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migrationFile = $this->argument('migration');
        
        // Extract migration name from file (without .php extension)
        $migrationName = basename($migrationFile, '.php');
        
        // Check if migration exists in database
        try {
            $migrationRecord = DB::table('migrations')
                ->where('migration', $migrationName)
                ->first();
        } catch (\Exception $e) {
            $this->error("Could not connect to database: " . $e->getMessage());
            $this->warn("Make sure your database connection is configured correctly in .env");
            return 1;
        }
            
        if (!$migrationRecord) {
            $this->error("Migration '{$migrationName}' not found in database.");
            $this->info("This migration has either not been run yet or has already been rolled back.");
            return 1;
        }
        
        // Show migration batch info
        $this->info("Found migration in batch {$migrationRecord->batch}");
        
        // Check if there are other migrations in the same batch
        $batchMigrations = DB::table('migrations')
            ->where('batch', $migrationRecord->batch)
            ->pluck('migration')
            ->toArray();
            
        if (count($batchMigrations) > 1) {
            $this->warn("WARNING: This migration is part of a batch with other migrations:");
            foreach ($batchMigrations as $batchMig) {
                $marker = ($batchMig === $migrationName) ? ' (this one)' : '';
                $this->line("  - {$batchMig}{$marker}");
            }
            $this->warn("Rolling back only this migration will break batch integrity.");
            $this->warn("Consider using 'php artisan migrate:rollback' to rollback the entire batch.");
            $this->newLine();
        }
        
        // Construct full path to migration file
        $fullPath = database_path('migrations/' . $migrationFile);
        if (!str_ends_with($fullPath, '.php')) {
            $fullPath .= '.php';
        }
        
        if (!file_exists($fullPath)) {
            $this->error("Migration file not found: {$fullPath}");
            return 1;
        }
        
        // Confirm with user (skip if --force is used)
        if (!$this->option('force')) {
            if (!$this->confirm("Are you sure you want to rollback '{$migrationName}'?", false)) {
                $this->info('Rollback cancelled.');
                return 0;
            }
        }
        
        try {
            // Load and execute the migration's down() method
            $migration = require $fullPath;
            
            $this->info("Rolling back: {$migrationName}");
            
            // Execute down method
            $migration->down();
            
            // Remove from migrations table
            DB::table('migrations')
                ->where('migration', $migrationName)
                ->delete();
                
            $this->info("Rolled back: {$migrationName}");
            $this->newLine();
            $this->info("Successfully rolled back 1 migration.");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Failed to rollback migration: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
