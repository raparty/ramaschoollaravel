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
    protected $signature = 'migrate:rollback-single {migration : The migration file name}';

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
        $migrationRecord = DB::table('migrations')
            ->where('migration', $migrationName)
            ->first();
            
        if (!$migrationRecord) {
            $this->error("Migration '{$migrationName}' not found in database.");
            return 1;
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
        
        // Confirm with user
        if (!$this->confirm("Are you sure you want to rollback '{$migrationName}'?", false)) {
            $this->info('Rollback cancelled.');
            return 0;
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
