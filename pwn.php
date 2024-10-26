<?php
// Database configuration
$dbconfig = [
    'db_server'   => '172.31.19.243',
    'db_port'     => '3306',
    'db_username' => 'tpglobalfx_u_test',
    'db_password' => '7#Dq5@^dJC8$(&A1',
    'db_name'     => 'cloud_tpglobalfx',
];

// Set up file path for SQL dump
$date = date('Y-m-d_H-i-s'); // Adds timestamp to the filename
$backupFile = __DIR__ . "/{$dbconfig['db_name']}_backup_$date.sql";

try {
    // Connect to the database
    $pdo = new PDO(
        "mysql:host={$dbconfig['db_server']};port={$dbconfig['db_port']};dbname={$dbconfig['db_name']}",
        $dbconfig['db_username'],
        $dbconfig['db_password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Start output buffering
    ob_start();

    // Write the header
    echo "-- Database backup for `{$dbconfig['db_name']}`\n";
    echo "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";

    // Get all tables in the database
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        // Get the table creation SQL
        $createTableStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        echo "\n\n-- Table structure for table `$table`\n\n";
        echo $createTableStmt['Create Table'] . ";\n\n";

        // Dump the data for each table
        echo "-- Dumping data for table `$table`\n\n";
        $rows = $pdo->query("SELECT * FROM `$table`", PDO::FETCH_ASSOC);
        
        foreach ($rows as $row) {
            $values = array_map([$pdo, 'quote'], array_values($row));
            echo "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
        }
    }

    // Get the output and write to the file
    $sqlDump = ob_get_clean();
    file_put_contents($backupFile, $sqlDump);

    echo "Database backup successful! File saved as: $backupFile";

} catch (PDOException $e) {
    echo "Error creating database backup: " . $e->getMessage();
}
?>
