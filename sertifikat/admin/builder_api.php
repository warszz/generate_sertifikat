<?php
/**
 * Certificate Builder API
 * Handles saving, loading, and managing custom certificate designs
 */
session_start();
require_once '../config/database.php';

// Load Dompdf if available
if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
}

use Dompdf\Dompdf;
use Dompdf\Options;

header('Content-Type: application/json');

// Verify user is logged in
if (empty($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$username = $_SESSION['user'];
$action = $_GET['action'] ?? $_POST['action'] ?? null;

switch ($action) {
    case 'save':
        saveCertificateDesign();
        break;
    case 'load':
        loadCertificateDesign();
        break;
    case 'list':
        listCertificates();
        break;
    case 'delete':
        deleteCertificate();
        break;
    case 'generate_pdf':
        generateCustomPDF();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Save Certificate Design
 */
function saveCertificateDesign() {
    global $pdo, $username;

    $name = $_POST['name'] ?? null;
    $description = $_POST['description'] ?? null;
    $designData = $_POST['design_data'] ?? null;
    $isTemplate = $_POST['is_template'] ?? 0;
    $canvasWidth = $_POST['canvas_width'] ?? 800;
    $canvasHeight = $_POST['canvas_height'] ?? 566;
    $certId = $_POST['cert_id'] ?? null;

    if (!$name || !$designData) {
        echo json_encode(['success' => false, 'message' => 'Name and design data are required']);
        return;
    }

    try {
        if ($certId) {
            // Update existing certificate
            $stmt = $pdo->prepare("
                UPDATE custom_certificates
                SET nama = ?, deskripsi = ?, design_data = ?, adalah_template = ?, 
                    canvas_width = ?, canvas_height = ?, diperbarui_pada = NOW()
                WHERE id = ? AND username = ?
            ");
            $stmt->execute([
                $name,
                $description,
                $designData,
                $isTemplate,
                $canvasWidth,
                $canvasHeight,
                $certId,
                $username
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Certificate updated successfully',
                'cert_id' => $certId
            ]);
        } else {
            // Insert new certificate
            $stmt = $pdo->prepare("
                INSERT INTO custom_certificates 
                (username, nama, deskripsi, design_data, adalah_template, canvas_width, canvas_height, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'draft')
            ");
            $stmt->execute([
                $username,
                $name,
                $description,
                $designData,
                $isTemplate,
                $canvasWidth,
                $canvasHeight
            ]);

            $certId = $pdo->lastInsertId();

            echo json_encode([
                'success' => true,
                'message' => 'Certificate saved successfully',
                'cert_id' => $certId
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

/**
 * Load Certificate Design
 */
function loadCertificateDesign() {
    global $pdo, $username;

    $id = $_GET['id'] ?? null;

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Certificate ID is required']);
        return;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT id, nama, deskripsi, design_data, canvas_width, canvas_height
            FROM custom_certificates
            WHERE id = ? AND (username = ? OR adalah_template = 1)
        ");
        $stmt->execute([$id, $username]);

        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Certificate not found']);
            return;
        }

        $certificate = $stmt->fetch(PDO::FETCH_ASSOC);

        // Decode JSON if it's a string
        $designData = $certificate['design_data'];
        if (is_string($designData)) {
            $designData = json_decode($designData, true);
        }

        echo json_encode([
            'success' => true,
            'cert_id' => $certificate['id'],
            'name' => $certificate['nama'],
            'description' => $certificate['deskripsi'],
            'design_data' => $designData,
            'canvas_width' => $certificate['canvas_width'],
            'canvas_height' => $certificate['canvas_height']
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

/**
 * List User's Certificates
 */
function listCertificates() {
    global $pdo, $username;

    try {
        $stmt = $pdo->prepare("
            SELECT id, nama, deskripsi, status, adalah_template, dibuat_pada
            FROM custom_certificates
            WHERE username = ?
            ORDER BY dibuat_pada DESC
        ");
        $stmt->execute([$username]);

        $certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'certificates' => $certificates
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

/**
 * Delete Certificate
 */
function deleteCertificate() {
    global $pdo, $username;

    $id = $_POST['id'] ?? null;

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Certificate ID is required']);
        return;
    }

    try {
        // Verify ownership
        $verify = $pdo->prepare("SELECT id FROM custom_certificates WHERE id = ? AND username = ?");
        $verify->execute([$id, $username]);

        if ($verify->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        // Delete related PDFs
        $pdo->prepare("
            DELETE FROM generated_custom_pdf 
            WHERE peserta_id IN (
                SELECT id FROM custom_cert_peserta WHERE custom_cert_id = ?
            )
        ")->execute([$id]);

        // Delete participants
        $pdo->prepare("DELETE FROM custom_cert_peserta WHERE custom_cert_id = ?")->execute([$id]);

        // Delete certificate
        $stmt = $pdo->prepare("DELETE FROM custom_certificates WHERE id = ? AND username = ?");
        $stmt->execute([$id, $username]);

        echo json_encode([
            'success' => true,
            'message' => 'Certificate deleted successfully'
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

/**
 * Generate PDF for Participant
 */
function generateCustomPDF() {
    global $pdo, $username;

    $certId = $_POST['cert_id'] ?? null;
    $participantName = $_POST['participant_name'] ?? null;
    $institution = $_POST['institution'] ?? null;
    $role = $_POST['role'] ?? null;
    $keterangan = $_POST['keterangan'] ?? '';

    if (!$certId || !$participantName) {
        echo json_encode(['success' => false, 'message' => 'Certificate ID and participant name are required']);
        return;
    }

    try {
        // Check if Dompdf is installed
        if (!file_exists('../vendor/autoload.php')) {
            echo json_encode(['success' => false, 'message' => 'Dompdf library not found']);
            return;
        }

        // Get certificate design
        $stmt = $pdo->prepare("
            SELECT design_data, canvas_width, canvas_height, nama as cert_name
            FROM custom_certificates
            WHERE id = ? AND (username = ? OR adalah_template = 1)
        ");
        $stmt->execute([$certId, $username]);

        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Certificate not found']);
            return;
        }

        $certificate = $stmt->fetch(PDO::FETCH_ASSOC);

        // Save participant
        $pesertaStmt = $pdo->prepare("
            INSERT INTO custom_cert_peserta (username, nama, instansi, peran, keterangan, custom_cert_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $pesertaStmt->execute([$username, $participantName, $institution, $role, $keterangan, $certId]);
        $pesertaId = $pdo->lastInsertId();

        // Generate HTML from Fabric.js JSON
        $html = generateHTMLFromFabric($certificate['design_data'], $participantName, $institution, $role, $keterangan);

        // Generate PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);

        $pdf = new Dompdf($options);
        $pdf->loadHtml($html);
        $pdf->setPaper([0, 0, $certificate['canvas_width'], $certificate['canvas_height']], 'portrait');
        $pdf->render();

        // Save to file
        $uploadDir = '../uploads/certificates/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = 'cert_' . $pesertaId . '_' . str_replace(' ', '_', $participantName) . '_' . date('dmY') . '.pdf';
        $filepath = $uploadDir . $filename;

        file_put_contents($filepath, $pdf->output());

        // Save PDF reference
        $pdfStmt = $pdo->prepare("
            INSERT INTO generated_custom_pdf (peserta_id, file_path)
            VALUES (?, ?)
        ");
        $pdfStmt->execute([$pesertaId, $filepath]);

        echo json_encode([
            'success' => true,
            'message' => 'PDF generated successfully',
            'download_url' => 'download_cert.php?id=' . $pesertaId,
            'pdf_path' => $filepath
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

/**
 * Generate HTML from Fabric.js JSON design
 */
function generateHTMLFromFabric($fabricJson, $participantName, $institution, $role, $keterangan = '') {
    if (is_string($fabricJson)) {
        $fabricJson = json_decode($fabricJson, true);
    }

    // Start building HTML
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            * { margin: 0; padding: 0; }
            body { margin: 0; padding: 0; }
            .fabric-canvas-wrapper {
                position: relative;
                width: ' . ($fabricJson['width'] ?? 800) . 'px;
                height: ' . ($fabricJson['height'] ?? 566) . 'px;
                background: ' . ($fabricJson['background'] ?? '#fff') . ';
                margin: auto;
                page-break-after: avoid;
                overflow: hidden;
            }
            .fabric-object {
                position: absolute;
                transform-origin: center center;
            }
            .fabric-text {
                white-space: nowrap;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        </style>
    </head>
    <body>
        <div class="fabric-canvas-wrapper">';

    // Process objects
    if (isset($fabricJson['objects']) && is_array($fabricJson['objects'])) {
        foreach ($fabricJson['objects'] as $obj) {
            $html .= renderFabricObject($obj, $participantName, $institution, $role, $keterangan);
        }
    }

    $html .= '
        </div>
    </body>
    </html>';

    return $html;
}

/**
 * Render individual Fabric.js object to HTML
 */
function renderFabricObject($obj, $participantName, $institution, $role, $keterangan = '') {
    if ($obj['type'] === 'text' || $obj['type'] === 'i-text') {
        // Replace placeholders - support both old and new format
        $text = $obj['text'] ?? '';
        
        // New format: {field}
        $text = str_replace('{nama}', htmlspecialchars($participantName), $text);
        $text = str_replace('{instansi}', htmlspecialchars($institution), $text);
        $text = str_replace('{peran}', htmlspecialchars($role), $text);
        $text = str_replace('{keterangan}', htmlspecialchars($keterangan), $text);
        
        // Old format: [FIELD] - for backward compatibility
        $text = str_replace('[NAMA_PESERTA]', htmlspecialchars($participantName), $text);
        $text = str_replace('[INSTANSI]', htmlspecialchars($institution), $text);
        $text = str_replace('[PERAN]', htmlspecialchars($role), $text);

        $fill = $obj['fill'] ?? '#000';
        $fontSize = $obj['fontSize'] ?? 16;
        $fontFamily = $obj['fontFamily'] ?? 'Arial';
        $fontWeight = $obj['fontWeight'] ?? 'normal';
        $left = $obj['left'] ?? 0;
        $top = $obj['top'] ?? 0;
        $width = $obj['width'] ?? 100;
        $height = $obj['height'] ?? 50;
        $angle = $obj['angle'] ?? 0;

        return '
        <div class="fabric-object fabric-text" style="
            left: ' . $left . 'px;
            top: ' . $top . 'px;
            width: ' . $width . 'px;
            height: ' . $height . 'px;
            transform: rotate(' . $angle . 'deg);
            font-size: ' . $fontSize . 'px;
            font-family: ' . $fontFamily . ';
            font-weight: ' . $fontWeight . ';
            color: ' . $fill . ';
        ">' . $text . '</div>';
    }

    if ($obj['type'] === 'rect') {
        $left = $obj['left'] ?? 0;
        $top = $obj['top'] ?? 0;
        $width = $obj['width'] ?? 100;
        $height = $obj['height'] ?? 100;
        $fill = $obj['fill'] ?? 'transparent';
        $stroke = $obj['stroke'] ?? 'none';
        $strokeWidth = $obj['strokeWidth'] ?? 0;
        $angle = $obj['angle'] ?? 0;

        return '
        <div class="fabric-object" style="
            left: ' . $left . 'px;
            top: ' . $top . 'px;
            width: ' . $width . 'px;
            height: ' . $height . 'px;
            background: ' . $fill . ';
            border: ' . $strokeWidth . 'px solid ' . $stroke . ';
            transform: rotate(' . $angle . 'deg);
        "></div>';
    }

    if ($obj['type'] === 'circle') {
        $left = $obj['left'] ?? 0;
        $top = $obj['top'] ?? 0;
        $radius = $obj['radius'] ?? 50;
        $fill = $obj['fill'] ?? 'transparent';
        $stroke = $obj['stroke'] ?? 'none';
        $strokeWidth = $obj['strokeWidth'] ?? 0;

        return '
        <div class="fabric-object" style="
            left: ' . ($left - $radius) . 'px;
            top: ' . ($top - $radius) . 'px;
            width: ' . ($radius * 2) . 'px;
            height: ' . ($radius * 2) . 'px;
            border-radius: 50%;
            background: ' . $fill . ';
            border: ' . $strokeWidth . 'px solid ' . $stroke . ';
        "></div>';
    }

    if ($obj['type'] === 'image') {
        $left = $obj['left'] ?? 0;
        $top = $obj['top'] ?? 0;
        $width = $obj['width'] ?? 100;
        $height = $obj['height'] ?? 100;
        $angle = $obj['angle'] ?? 0;
        $src = $obj['src'] ?? '';

        return '
        <div class="fabric-object" style="
            left: ' . $left . 'px;
            top: ' . $top . 'px;
            width: ' . $width . 'px;
            height: ' . $height . 'px;
            transform: rotate(' . $angle . 'deg);
        ">
            <img src="' . htmlspecialchars($src) . '" style="width:100%; height:100%; object-fit:cover;">
        </div>';
    }

    return '';
}
?>
