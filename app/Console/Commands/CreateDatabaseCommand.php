<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('db:create')]
#[Description('Create the database specified in .env if it does not exist')]
class CreateDatabaseCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = config('database.default');
        
        if ($connection !== 'mysql') {
            $this->info("This command only supports mysql. Currently using: {$connection}");
            return;
        }

        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        try {
            $pdo = new \PDO("mysql:host={$host};port={$port}", $username, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

            $this->info("Successfully created database '{$database}' or it already exists.");
        } catch (\PDOException $e) {
            $this->error("Failed to create database: " . $e->getMessage());
        }
    }
}
