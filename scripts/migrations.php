<?php

// Get config
require_once(__DIR__ . '/../app/config/config.php');

// Get constants
require_once(__DIR__ . '/../constants.php');

// Parse command line arguments for action in form scriptname [action] as a string
$action = isset($argv[1]) ? $argv[1] : '';

switch ($action) {
    case 'run':
        $mysql_dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4';
        $db = new PDO($mysql_dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        // Check if migrations table exists
        $stmt = $db->prepare('SHOW TABLES LIKE :table');
        $stmt->bindValue(':table', 'migrations');
        $stmt->execute();
        $result = $stmt->fetch();
        if (!$result) {
            // create migrations table
            $stmt = $db->prepare('CREATE TABLE migrations (
                id INT(11) NOT NULL AUTO_INCREMENT,
                migration VARCHAR(255) NOT NULL,
                applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8');
            $stmt->execute();
        }
        // Get all migrations
        $stmt = $db->prepare('SELECT * FROM migrations');
        $stmt->execute();
        $migrations = $stmt->fetchAll();

        // Get all files in MIGRATIONS_DIR
        $files = scandir(MIGRATIONS_DIR);
        // Remove . ..
        $files = array_diff($files, array('.', '..'));

        $migrations_to_run = $files;
        foreach ($migrations as $migration) {
            // Find migration in migrations_to_run
            $key = array_search($migration['migration'], $migrations_to_run);
            if ($key !== false) {
                unset($migrations_to_run[$key]);
            }
        }
        foreach ($migrations_to_run as $migration) {
            try {
                $migration_content = file_get_contents(MIGRATIONS_DIR . $migration);
                $stmt = $db->prepare($migration_content);
                $stmt->execute();
                $stmt = $db->prepare('INSERT INTO migrations (migration) VALUES (:migration)');
                $stmt->bindValue(':migration', $migration);
                $stmt->execute();
                echo "\033[32mMigration successful: " . $migration . "\033[0m\n";
            } catch (Exception $e) {
                echo "\033[31mMigration failed: " . $migration . ": " . $e->getMessage() . "\033[0m\n";
                exit(1);
            }
        }
        echo "\033[32mMigration successful\033[0m\n";
        break;
    case 'create':
        $name = isset($argv[2]) ? $argv[2] : '';
        if ($name) {
            // Create migration
            $current_timestamp = date('YmdHis');
            $file_name = $current_timestamp . '_' . $name . '.sql';
            // Create a new empty file in MIGRATIONS_DIR
            touch(MIGRATIONS_DIR . $file_name);
            echo "Created migration: " . MIGRATIONS_DIR . $file_name;
            exit(0);
        } else {
            echo "please supply a name for your migration";
            exit(1);
        }
        break;
    default:
        // Print usage
        echo "Usage: php migrations.php [action]\n";
        echo "Actions:\n";
        echo "  run - Apply migrations to database\n";
        echo "  create - Create a database migration, requires a migration name as a string\n";
        echo "  help - Show this help message\n";
        echo "Example: php migrations.php run\n";
        echo "Example: php migrations.php create checklist_groups\n";
        exit(1);
        break;
}