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

// Connect to the database
try {
    echo "Connecting to the database...<br>";
    $pdo = new PDO(
        "mysql:host={$dbconfig['db_server']};port={$dbconfig['db_port']};dbname={$dbconfig['db_name']}",
        $dbconfig['db_username'],
        $dbconfig['db_password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected to the database successfully.<br>";

    // Start output buffering
    ob_start();

    // Write the header
    echo "-- Database backup for `{$dbconfig['db_name']}`<br>";
    echo "-- Generated: " . date('Y-m-d H:i:s') . "<br><br>";

    // Get all tables in the database
    echo "Retrieving list of tables...<br>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    echo "Tables found in the database:<br>";
    foreach ($tables as $table) {
        echo " - $table<br>";
    }

    foreach ($tables as $table) {
        echo "<br>Processing table `$table`...<br>";
        
        // Get the table creation SQL
        $createTableStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        echo "-- Table structure for table `$table`<br>";
        
        // Write the table structure
        file_put_contents($backupFile, "-- Table structure for table `$table`\n", FILE_APPEND);
        file_put_contents($backupFile, $createTableStmt['Create Table'] . ";\n\n", FILE_APPEND);

        // Dump the data for each table
        file_put_contents($backupFile, "-- Dumping data for table `$table`\n", FILE_APPEND);
        $rows = $pdo->query("SELECT * FROM `$table`", PDO::FETCH_ASSOC);
        
        foreach ($rows as $row) {
            $values = array_map([$pdo, 'quote'], array_values($row));
            file_put_contents($backupFile, "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n", FILE_APPEND);
        }

        echo "Data for table `$table` has been written successfully.<br>";
    }

    echo "<br>Database backup successful! File saved as: $backupFile";

} catch (PDOException $e) {
    echo "Error creating database backup: " . $e->getMessage();
}
?>
