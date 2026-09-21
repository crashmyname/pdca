<?php

use Bpjs\Framework\Helpers\SchemaBuilder;
use Bpjs\Framework\Helpers\Database;

class CreateTasksTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('tasks');
        
        //  Define columns here
        $table->id();
        $table->string('task_code')->unique();
        $table->string('operator_name');
        $table->date('task_date');
        $table->string('section');
        $table->text('problem');
        $table->enum('category',['man','machine','material','methode'])->nullable();
        $table->string('pic_section',15)->nullable();
        $table->text('temporary_action')->nullable();
        $table->text('permanent_action')->nullable();
        $table->date('deadline')->nullable();
        $table->string('pic')->nullable();
        $table->string('doc_4m_status',10)->notNullable()->default('belum');
        $table->string('doc_logbook_status',10)->notNullable()->default('belum');
        $table->string('doc_nursecall_status',10)->notNullable()->default('belum');
        $table->enum('stage',['plan','do','check','act'])->default('plan');
        $table->timestamp('stage_updated_at')->nullable();
        $table->bigInteger('stage_updated_by')->nullable();
        $table->enum('status',['open','in_progress','done','cancelled'])->default('open');
        $table->timestamp('status_updated_at')->nullable();
        $table->bigInteger('status_updated_by')->nullable();
        $table->string('leader_signature')->nullable();
        $table->timestamp('approved_at')->nullable();
        $table->bigInteger('approved_by')->nullable();
        $table->string('created_by_name')->nullable();
        $table->enum('created_by_role', ['admin', 'team_leader' , 'group_leader', 'manager' , 'operator'])->default('operator');
        $table->string('created_by')->nullable();
        $table->string('updated_by')->nullable();
        $table->timestamp('created_at')->default('CURRENT_TIMESTAMP');
        $table->timestamp('updated_at')->default('CURRENT_TIMESTAMP');
        $table->softDeletes();
        
        //  Add indexes
        $table->index('task_code');
        $table->index('stage');
        $table->index('status');
        $table->index('task_date');
        $table->index('deadline');
        $table->index('operator_name');
        $table->index(['stage', 'status']);
        $table->index(['section', 'task_date']);
        $table->index('created_at');
        
        $sql = $table->buildCreateSQL();
        
        try {
            $pdo->exec($sql);
            echo " Table 'tasks' created successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to create table: " . $e->getMessage() . "\n";
            echo " SQL: " . $sql . "\n";
        }
    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('tasks');
        
        try {
            $pdo->exec($table->buildDropSQL());
            echo " Table 'tasks' dropped successfully\n";
        } catch (\PDOException $e) {
            echo " Failed to drop table: " . $e->getMessage() . "\n";
        }
    }
}
