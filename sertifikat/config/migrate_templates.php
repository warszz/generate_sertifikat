<?php
/**
 * Database Migration for Custom Certificate Templates
 * Creates table for storing editor templates (from editor/index.html)
 */

require_once 'database.php';

echo "Creating certificate_templates_custom table...\n\n";

$sql = "
CREATE TABLE IF NOT EXISTS certificate_templates_custom (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    canvas_json LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

try {
    $pdo->exec($sql);
    echo "✓ Table 'certificate_templates_custom' created successfully!\n\n";
    
    // Show table info
    $result = $pdo->query("DESC certificate_templates_custom");
    echo "Table Structure:\n";
    echo "================\n";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        printf("%-15s | %-20s | %s\n", $row['Field'], $row['Type'], $row['Key'] ?: 'NULL');
    }
    echo "\n";
    
} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage() . "\n");
}
?>
