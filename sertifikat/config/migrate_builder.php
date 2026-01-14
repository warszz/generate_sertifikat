<?php
/**
 * Database Migration for Custom Certificate Builder
 * Adds tables for custom certificate designs (backward-compatible extension)
 * 
 * Usage:
 * - Via CLI: php config/migrate_builder.php
 * - Via Browser: http://your-domain/sertifikat/config/migrate_builder.php
 */

require_once 'database.php';

echo "Starting Certificate Builder Migration...\n\n";

// 1. Create directories
$uploadsDir = dirname(__DIR__) . '/uploads/certificates';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
    echo "✓ Created uploads/certificates directory\n";
} else {
    echo "✓ uploads/certificates directory already exists\n";
}

// Make directory writable
chmod($uploadsDir, 0755);

// 2. Create database tables
$sql = "
CREATE TABLE IF NOT EXISTS custom_certificates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    nama VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    design_data LONGTEXT NOT NULL COMMENT 'JSON - Fabric.js canvas state',
    canvas_width INT DEFAULT 800,
    canvas_height INT DEFAULT 566,
    adalah_template BOOLEAN DEFAULT FALSE COMMENT 'If TRUE, can be used as template for others',
    status ENUM('draft', 'published') DEFAULT 'draft',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (username) REFERENCES users(username),
    INDEX idx_username (username),
    INDEX idx_status (status)
);

CREATE TABLE IF NOT EXISTS custom_cert_peserta (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    nama VARCHAR(200) NOT NULL,
    instansi VARCHAR(200),
    peran VARCHAR(100),
    custom_cert_id INT NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (custom_cert_id) REFERENCES custom_certificates(id) ON DELETE CASCADE,
    FOREIGN KEY (username) REFERENCES users(username),
    INDEX idx_username (username),
    INDEX idx_cert_id (custom_cert_id)
);

CREATE TABLE IF NOT EXISTS generated_custom_pdf (
    id INT PRIMARY KEY AUTO_INCREMENT,
    peserta_id INT NOT NULL,
    file_path VARCHAR(255),
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (peserta_id) REFERENCES custom_cert_peserta(id) ON DELETE CASCADE
);
";

try {
    // Split and execute each CREATE TABLE statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement . ';');
        }
    }
    
    echo "✓ Database tables created successfully\n";
    echo "\n";
    echo "========================================\n";
    echo "Migration completed successfully!\n";
    echo "========================================\n";
    echo "\nNext steps:\n";
    echo "1. Verify uploads/certificates directory is writable\n";
    echo "2. Access the builder at: /admin/custom_certificates.php\n";
    echo "3. Check /admin/generate.php for the 'Custom Builder' button\n";
    echo "\n";
    
} catch (PDOException $e) {
    echo "\n❌ Migration Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
