<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateTaskHistoriesTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('task_histories');
        
        //  Define columns here
        $table->id();
        $table->bigInteger('task_id');
        $table->string('action'); // create, update, move_stage, change_status, approve, delete
        $table->string('old_stage')->nullable();
        $table->string('new_stage')->nullable();
        $table->string('old_status')->nullable();
        $table->string('new_status')->nullable();
        $table->json('old_values')->nullable();
        $table->json('new_values')->nullable();
        $table->string('changed_by_name')->nullable();
        $table->string('changed_by_role')->nullable();
        $table->text('notes')->nullable();
        $table->timestamp('created_at')->default('CURRENT_TIMESTAMP');
        $table->timestamp('updated_at')->default('CURRENT_TIMESTAMP');
        $table->softDeletes();
        
        //  Add indexes
        $table->index(['task_id', 'action','created_at']);
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'task_histories' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('task_histories');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'task_histories' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
