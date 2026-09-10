<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateNotificationsTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('notifications');
        
        //  Define columns here
        $table->id();
        $table->bigInteger('task_id');
        $table->bigInteger('user_id')->nullable();
        $table->string('type'); // new_task, stage_changed, deadline_approaching, task_approved
        $table->text('message');
        $table->boolean('is_read')->default(false);
        $table->timestamp('read_at')->nullable();
        $table->timestamps();
        $table->softDeletes();
        
        //  Add indexes
        $table->index(['user_id', 'is_read']);
        $table->index('task_id');
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'notifications' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('notifications');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'notifications' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
