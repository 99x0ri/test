<?php
// Database configuration
$dbconfig = [
    'db_server'   => '172.31.19.243',
    'db_port'     => '3306',
    'db_username' => 'tpglobalfx_u_test',
    'db_password' => '7#Dq5@^dJC8$(&A1',
    'db_name'     => 'cloud_tpglobalfx',
];

try {
    echo "Connecting to the database...<br>";
    $pdo = new PDO(
        "mysql:host={$dbconfig['db_server']};port={$dbconfig['db_port']};dbname={$dbconfig['db_name']}",
        $dbconfig['db_username'],
        $dbconfig['db_password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected to the database successfully.<br>";

    // Prepare the DELETE statement
    $emailToDelete = 'adminreports@tpglobalfx.com';
    $stmt = $pdo->prepare("DELETE FROM vtiger_users WHERE email1 = :email1");
    
    // Bind the parameter
    $stmt->bindParam(':email1', $emailToDelete);

    // Execute the query
    if ($stmt->execute()) {
        echo "Successfully deleted user with email: $emailToDelete.<br>";
    } else {
        echo "No user found with email: $emailToDelete.<br>";
    }
} catch (PDOException $e) {
    echo "Error executing the query: " . $e->getMessage();
}
?>
