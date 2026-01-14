<?php
/**
 * Add Keterangan Field to custom_cert_peserta Table
 */

require_once 'database.php';

try {
    // Check if column already exists
    $checkStmt = $pdo->prepare("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = 'custom_cert_peserta' 
        AND COLUMN_NAME = 'keterangan'
    ");
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        echo "✓ Column 'keterangan' sudah ada di tabel custom_cert_peserta\n";
        exit;
    }
    
    // Add keterangan column
    $pdo->exec("
        ALTER TABLE custom_cert_peserta 
        ADD COLUMN keterangan VARCHAR(500) NULL 
        AFTER peran
    ");
    
    echo "✓ Column 'keterangan' berhasil ditambahkan ke tabel custom_cert_peserta\n";
    echo "  - Type: VARCHAR(500)\n";
    echo "  - Nullable: YES\n";
    echo "  - Position: After 'peran' column\n";
    
    // Verify
    $result = $pdo->query('DESC custom_cert_peserta');
    echo "\nTabel structure sekarang:\n";
    echo str_repeat("-", 50) . "\n";
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " | " . $row['Type'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
