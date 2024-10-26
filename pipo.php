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
    echo "Connecting to the database...<br>";
    $pdo = new PDO(
        "mysql:host={$dbconfig['db_server']};port={$dbconfig['db_port']};dbname={$dbconfig['db_name']}",
        $dbconfig['db_username'],
        $dbconfig['db_password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected to the database successfully.<br>";

    // Write the header to the file
    $header = "-- Database backup for `{$dbconfig['db_name']}`\n";
    $header .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    file_put_contents($backupFile, $header);

    // Get all tables in the database
    echo "Retrieving list of tables...<br>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    echo "Tables found in the database:<br>";
    foreach ($tables as $table) {
        echo " - $table<br>";
    }

    // Loop through each table to dump its structure and data
    foreach ($tables as $table) {
        echo "<br>Processing table `$table`...<br>";

        // Get the table creation SQL
        $createTableStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        $tableHeader = "\n\n-- Table structure for table `$table`\n";
        $tableHeader .= $createTableStmt['Create Table'] . ";\n\n";
        file_put_contents($backupFile, $tableHeader, FILE_APPEND);

        // Dump the data for each table
        file_put_contents($backupFile, "-- Dumping data for table `$table`\n", FILE_APPEND);
        $rowQuery = $pdo->query("SELECT * FROM `$table`", PDO::FETCH_ASSOC);

        // Check if rows are present
        $rowCount = false;
        while ($row = $rowQuery->fetch(PDO::FETCH_ASSOC)) {
            $rowCount = true;
            // Format row values for the INSERT statement
            $values = array_map(function ($value) use ($pdo) {
                return $value === null ? 'NULL' : $pdo->quote($value);
            }, array_values($row));
            
            $insertStmt = "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
            file_put_contents($backupFile, $insertStmt, FILE_APPEND);
        }

        if (!$rowCount) {
            file_put_contents($backupFile, "-- No data in table `$table`\n", FILE_APPEND);
            echo "No data found for table `$table`.<br>";
        } else {
            echo "Data for table `$table` has been written successfully.<br>";
        }
    }

    echo "<br>Database backup successful! File saved as: $backupFile";

} catch (PDOException $e) {
    echo "Error creating database backup: " . $e->getMessage();
}
?>
