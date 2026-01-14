<?php
/**
 * Generate PDF Certificate
 * Supports both:
 * 1. Default templates: Using sertifikat_1.php, sertifikat_2.php, etc
 * 2. Custom templates: Using Fabric.js canvas JSON with variable replacement
 */

session_start();
require_once '../config/database.php';
require_once '../helpers/replace_variables.php';
require_once '../helpers/fabric_to_html.php';

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
$templateType = $_GET['type'] ?? 'default'; // 'default' or 'custom'

try {
    // DEFAULT TEMPLATE FLOW
    if ($templateType === 'default') {
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
        $keterangan = $row['keterangan'] ?? ''; // Optional custom description
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
    }
    
    // CUSTOM TEMPLATE FLOW
    else if ($templateType === 'custom') {
        // Get template ID and participant ID
        $templateId = intval($_GET['template_id'] ?? 0);
        $pesertaId = intval($_GET['peserta_id'] ?? 0);
        
        if ($templateId === 0 || $pesertaId === 0) {
            $_SESSION['errors'] = ["Template ID dan Peserta ID diperlukan"];
            header("Location: generate.php");
            exit();
        }
        
        // Load template
        $templateStmt = $pdo->prepare("
            SELECT canvas_json FROM certificate_templates_custom WHERE id = ?
        ");
        $templateStmt->execute([$templateId]);
        
        if ($templateStmt->rowCount() === 0) {
            $_SESSION['errors'] = ["Template custom tidak ditemukan"];
            header("Location: generate.php");
            exit();
        }
        
        $templateRow = $templateStmt->fetch(PDO::FETCH_ASSOC);
        $canvasJson = $templateRow['canvas_json'];
        
        // Load participant data
        $pesertaStmt = $pdo->prepare("
            SELECT nama, instansi, peran, keterangan FROM peserta WHERE id = ? AND username = ?
        ");
        $pesertaStmt->execute([$pesertaId, $_SESSION['user']]);
        
        if ($pesertaStmt->rowCount() === 0) {
            $_SESSION['errors'] = ["Data peserta tidak ditemukan atau Anda tidak memiliki akses"];
            header("Location: generate.php");
            exit();
        }
        
        $pesertaRow = $pesertaStmt->fetch(PDO::FETCH_ASSOC);
        $nama = $pesertaRow['nama'];
        $instansi = $pesertaRow['instansi'];
        $peran = $pesertaRow['peran'];
        $keterangan = $pesertaRow['keterangan'] ?? '';
        
        // Participant data for variable replacement
        $participantData = [
            'nama' => $nama,
            'instansi' => $instansi,
            'peran' => $peran
        ];
        
        // Convert Fabric canvas to HTML with variable replacement
        $containerHtml = fabricCanvasToHtml($canvasJson, $participantData);
        
        // Wrap in complete HTML document
        $html = wrapInDocument($containerHtml);
        
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
    }
    
    else {
        $_SESSION['errors'] = ["Template type tidak valid"];
        header("Location: generate.php");
        exit();
    }
    
} catch (Exception $e) {
    $_SESSION['errors'] = ["Error: " . $e->getMessage()];
    header("Location: generate.php");
    exit();
}
?>
