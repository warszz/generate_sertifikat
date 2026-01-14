<?php
/**
 * Add Keterangan Field to Peserta Table
 * Adds custom description/notes field for certificates
 */

require_once 'database.php';

try {
    // Check if column already exists
    $checkStmt = $pdo->prepare("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = 'peserta' 
        AND COLUMN_NAME = 'keterangan'
    ");
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        echo "✓ Column 'keterangan' sudah ada di tabel peserta\n";
        exit;
    }
    
    // Add keterangan column
    $pdo->exec("
        ALTER TABLE peserta 
        ADD COLUMN keterangan VARCHAR(500) NULL 
        AFTER peran
    ");
    
    echo "✓ Column 'keterangan' berhasil ditambahkan ke tabel peserta\n";
    echo "  - Type: VARCHAR(500)\n";
    echo "  - Nullable: YES\n";
    echo "  - Position: After 'peran' column\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
