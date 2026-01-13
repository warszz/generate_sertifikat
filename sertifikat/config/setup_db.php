<?php
require_once 'database.php';

// Create Tables
$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS template_sertifikat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_template VARCHAR(100) NOT NULL,
    file_template VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS peserta (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    nama VARCHAR(200) NOT NULL,
    instansi VARCHAR(200) NOT NULL,
    peran VARCHAR(100) NOT NULL,
    template_id INT NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES template_sertifikat(id),
    INDEX idx_username (username)
);

CREATE TABLE IF NOT EXISTS generated_pdf (
    id INT PRIMARY KEY AUTO_INCREMENT,
    peserta_id INT NOT NULL,
    file_path VARCHAR(255),
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (peserta_id) REFERENCES peserta(id)
);
";

try {
    $pdo->exec($sql);
    
    // Insert default templates
    $insert_templates = "
    INSERT IGNORE INTO template_sertifikat (id, nama_template, file_template) VALUES
    (1, 'Template Seminar', 'sertifikat_1.php'),
    (2, 'Template Workshop', 'sertifikat_2.php'),
    (3, 'Template Pelatihan', 'sertifikat_3.php');
    ";
    
    $pdo->exec($insert_templates);
    
    echo "Database berhasil dibuat!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
