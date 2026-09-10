<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateAttachmentsTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('attachments');
        
        //  Define columns here
        $table->id();
        $table->bigInteger('task_id');
        $table->string('file_name');
        $table->string('file_path');
        $table->string('file_type')->nullable();
        $table->integer('file_size')->nullable();
        $table->string('uploaded_by_name')->nullable();
        $table->enum('uploaded_by_role', ['operator', 'leader', 'admin'])->default('operator');
        $table->timestamps();
        $table->softDeletes();
        
        //  Add indexes
        $table->index(['task_id']);
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'attachments' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('attachments');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'attachments' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
