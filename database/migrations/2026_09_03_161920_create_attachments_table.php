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
        $table->enum('file_type',['4m', 'logbook', 'nursecall'])->nullable();
        $table->string('original_name');
        $table->string('stored_name');
        $table->string('file_path');
        $table->string('mime_type');
        $table->integer('file_size')->nullable();
        $table->bigInteger('uploaded_by')->notNullable();
        $table->string('uploaded_by_name')->nullable();
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
