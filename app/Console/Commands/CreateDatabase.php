<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the database if it does not exist';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $database = env('DB_DATABASE');

        // Temporarily use the 'information_schema' database to execute the CREATE DATABASE statement
        Config::set('database.connections.mysql.database', 'information_schema');

        $query = "CREATE DATABASE IF NOT EXISTS `$database`";

        try {
            DB::statement($query);
            $this->info("Database '$database' created or already exists.");
        } catch (\Exception $e) {
            $this->error("Failed to create database: " . $e->getMessage());
            return 1;
        }

        // Restore the original database configuration
        Config::set('database.connections.mysql.database', $database);

        return 0;
    }
}
