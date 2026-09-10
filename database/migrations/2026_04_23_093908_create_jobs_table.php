<?php

use Bpjs\Framework\Helpers\Database;
use Bpjs\Framework\Helpers\SchemaBuilder;

class CreateJobsTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('jobs');
        
        $table->id();
        $table->string('queue', 255)->notNullable()->default('default');
        $table->longText('payload')->notNullable();
        $table->string('status', 20)->notNullable()->default('pending');
        $table->integer('attempts')->notNullable()->default(0);
        $table->text('error_message')->nullable();
        $table->timestamp('available_at')->nullable();
        $table->timestamp('reserved_at')->nullable();
        $table->timestamp('created_at')->default('CURRENT_TIMESTAMP');
        $table->timestamp('updated_at')->default('CURRENT_TIMESTAMP');
        
        $table->index(['queue', 'status'], 'idx_queue_status');
        $table->index(['available_at'], 'idx_available');
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo "Table 'jobs' berhasil dibuat\n";
        } catch (\PDOException $e) {
            echo "Gagal membuat tabel: " . $e->getMessage() . "\n";
            echo "SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('jobs');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo "Table 'jobs' berhasil dihapus\n";
        } catch (\PDOException $e) {
            echo "Gagal menghapus tabel: " . $e->getMessage() . "\n";
        }
    }
}