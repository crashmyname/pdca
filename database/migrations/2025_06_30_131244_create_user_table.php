<?php

// use PDO;

use Bpjs\Framework\Helpers\Database;
use Bpjs\Framework\Helpers\SchemaBuilder;

class CreateUserTable
{
    public function up(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('users');

        $table->id();
        $table->string('name',150)->notNullable();
        $table->string('username',150)->notNullable()->unique();
        $table->string('unique_user',150)->nullable();
        $table->string('password');
        $table->enum('role', ['admin', 'leader', 'operator'])->default('operator');
        $table->timestamps(); // Menggantikan timestamp manual
        $table->rememberToken(); // Optional: untuk fitur remember me

        $sql = $table->buildCreateSQL();
        try {
            $pdo->exec($sql);
            echo "Table 'user' berhasil dibuat\n";
            $stmt = $pdo->prepare("
                INSERT INTO users (name, username, unique_user, password)
                VALUES (:name, :username, :unique_user, :password)
            ");

            $stmt->execute([
                ':name' => 'Administrator',
                ':username' => 'admin',
                ':unique_user' => 'adminxxx',
                ':password' => password_hash('admin123', PASSWORD_BCRYPT)
            ]);

            echo "User admin berhasil dibuat\n";
        } catch (\PDOException $e) {
            echo "Gagal membuat tabel: " . $e->getMessage() . "\n";
            echo "SQL: $sql\n";
        }

    }

    public function down(): void
    {
        $pdo = Database::connection();
        $table = new SchemaBuilder('users');
        $pdo->exec($table->buildDropSQL());
    }
}
