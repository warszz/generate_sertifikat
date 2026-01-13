<?php
session_start();
require_once '../config/database.php';

// Check if Dompdf is installed
if (!file_exists('../vendor/autoload.php')) {
    die("Error: Dompdf library tidak ditemukan. Silakan install dengan: composer require dompdf/dompdf");
}

require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (empty($_GET['id'])) {
    header("Location: generate.php");
    exit();
}

$id = intval($_GET['id']);

try {
    $data = $pdo->prepare("
        SELECT p.*, t.file_template, t.nama_template
        FROM peserta p
        JOIN template_sertifikat t ON p.template_id = t.id
        WHERE p.id = ? AND p.username = ?
    ");
    
    $data->execute([$id, $_SESSION['user']]);
    
    if ($data->rowCount() === 0) {
        $_SESSION['errors'] = ["Data peserta tidak ditemukan atau Anda tidak memiliki akses"];
        header("Location: generate.php");
        exit();
    }
    
    $row = $data->fetch(PDO::FETCH_ASSOC);
    
    // Ambil data untuk digunakan di template
    $nama = $row['nama'];
    $instansi = $row['instansi'];
    $peran = $row['peran'];
    $template_file = $row['file_template'];
    
    // Generate certificate content
    ob_start();
    $template_path = "../templates/" . $template_file;
    
    if (!file_exists($template_path)) {
        ob_end_clean();
        $_SESSION['errors'] = ["Template file tidak ditemukan: " . htmlspecialchars($template_file)];
        header("Location: generate.php");
        exit();
    }
    
    include $template_path;
    $html = ob_get_clean();
    
    // Setup Dompdf
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isPhpEnabled', true);
    
    $pdf = new Dompdf($options);
    $pdf->loadHtml($html);
    $pdf->setPaper('A4', 'landscape');
    $pdf->render();
    
    // Set output filename
    $filename = "sertifikat_" . str_replace(' ', '_', $nama) . "_" . date('dmY') . ".pdf";
    
    // Stream PDF to browser
    $pdf->stream($filename, array("Attachment" => true));
    
} catch (Exception $e) {
    $_SESSION['errors'] = ["Error: " . $e->getMessage()];
    header("Location: generate.php");
    exit();
}
?>
