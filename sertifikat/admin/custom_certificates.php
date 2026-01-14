<?php require_once 'check_login.php'; ?>
<?php require_once '../config/database.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Certificates - Sistem Sertifikat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header h1 {
            color: white;
            font-size: 28px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            font-size: 14px;
        }

        .btn-primary {
            background: white;
            color: #667eea;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
        }

        .card-header h3 {
            font-size: 18px;
            margin-bottom: 5px;
            word-break: break-word;
        }

        .card-header .status {
            font-size: 12px;
            background: rgba(255, 255, 255, 0.3);
            padding: 3px 8px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 5px;
        }

        .card-body {
            padding: 20px;
        }

        .cert-info {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
        }

        .cert-info strong {
            color: #333;
        }

        .cert-description {
            font-size: 13px;
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-footer {
            display: flex;
            gap: 8px;
            padding: 15px 20px;
            background: #f9f9f9;
            border-top: 1px solid #eee;
        }

        .card-btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .card-btn.edit {
            background: #667eea;
            color: white;
        }

        .card-btn.edit:hover {
            background: #5568d3;
        }

        .card-btn.generate {
            background: #27ae60;
            color: white;
        }

        .card-btn.generate:hover {
            background: #229954;
        }

        .card-btn.delete {
            background: #e74c3c;
            color: white;
        }

        .card-btn.delete:hover {
            background: #c0392b;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .empty-state i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h2 {
            color: #999;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .empty-state p {
            color: #bbb;
            margin-bottom: 20px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
        }

        .modal-header {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #555;
            font-size: 13px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 13px;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
        }

        .modal-btn.primary {
            background: #667eea;
            color: white;
        }

        .modal-btn.primary:hover {
            background: #5568d3;
        }

        .modal-btn.secondary {
            background: #f0f0f0;
            color: #333;
        }

        .modal-btn.secondary:hover {
            background: #e0e0e0;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .back-link:hover {
            gap: 12px;
        }

        @media (max-width: 768px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="generate.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Original Certificates
        </a>

        <div class="header">
            <h1>Custom Certificates (Builder)</h1>
            <button class="btn btn-primary" onclick="openNewCertificateModal()">
                <i class="fas fa-plus"></i> Create New
            </button>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success">
                <strong>Success!</strong> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error">
                <strong>Error!</strong> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="cards-grid" id="certificatesGrid">
            <!-- Cards will be loaded here -->
        </div>

        <div id="emptyState" class="empty-state" style="display:none;">
            <i class="fas fa-certificate"></i>
            <h2>No Custom Certificates Yet</h2>
            <p>Create your first custom certificate using Fabric.js builder</p>
            <button class="btn btn-primary" onclick="openNewCertificateModal()">
                <i class="fas fa-plus"></i> Create Custom Certificate
            </button>
        </div>
    </div>

    <!-- New Certificate Modal -->
    <div id="newCertModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Create New Certificate</div>
            <div class="form-group">
                <label>Certificate Name *</label>
                <input type="text" id="newCertName" placeholder="e.g., Seminar Certificate 2026" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea id="newCertDesc" placeholder="Optional description..."></textarea>
            </div>
            <div class="modal-footer">
                <button class="modal-btn secondary" onclick="closeModal('newCertModal')">Cancel</button>
                <button class="modal-btn primary" onclick="createNewCertificate()">Create & Edit</button>
            </div>
        </div>
    </div>

    <!-- Add Participants Modal -->
    <div id="participantsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Add Participant & Generate PDF</div>
            <div id="participantAlerts"></div>
            <div class="form-group">
                <label>Participant Name *</label>
                <input type="text" id="participantName" placeholder="Full name" required>
            </div>
            <div class="form-group">
                <label>Institution/Organization</label>
                <input type="text" id="participantInst" placeholder="Company or organization">
            </div>
            <div class="form-group">
                <label>Role/Title</label>
                <input type="text" id="participantRole" placeholder="Position or role">
            </div>
            <div class="form-group">
                <label>Keterangan (Optional)</label>
                <input type="text" id="participantKeterangan" placeholder="Additional notes or description">
            </div>
            <div class="modal-footer">
                <button class="modal-btn secondary" onclick="closeModal('participantsModal')">Cancel</button>
                <button class="modal-btn primary" onclick="generatePdfForParticipant()">Generate PDF</button>
            </div>
        </div>
    </div>

    <script>
        let currentCertId = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadCertificates();
        });

        function loadCertificates() {
            fetch('builder_api.php?action=list')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.certificates.length > 0) {
                        renderCertificates(data.certificates);
                        document.getElementById('emptyState').style.display = 'none';
                    } else {
                        document.getElementById('certificatesGrid').innerHTML = '';
                        document.getElementById('emptyState').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error loading certificates:', error);
                    alert('Error loading certificates');
                });
        }

        function renderCertificates(certificates) {
            let html = '';
            certificates.forEach(cert => {
                html += `
                    <div class="card">
                        <div class="card-header">
                            <h3>${escapeHtml(cert.nama)}</h3>
                            <span class="status">${cert.status === 'draft' ? '📝 Draft' : '✓ Published'}</span>
                        </div>
                        <div class="card-body">
                            <div class="cert-info">
                                <strong>Created:</strong> ${new Date(cert.dibuat_pada).toLocaleDateString()}
                            </div>
                            ${cert.deskripsi ? `<div class="cert-description">${escapeHtml(cert.deskripsi)}</div>` : ''}
                        </div>
                        <div class="card-footer">
                            <button class="card-btn edit" onclick="editCertificate(${cert.id})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="card-btn generate" onclick="openGenerateModal(${cert.id})">
                                <i class="fas fa-file-pdf"></i> Generate
                            </button>
                            <button class="card-btn delete" onclick="deleteCertificate(${cert.id})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                `;
            });
            document.getElementById('certificatesGrid').innerHTML = html;
        }

        function editCertificate(certId) {
            window.location.href = `builder.php?edit=${certId}`;
        }

        function openGenerateModal(certId) {
            currentCertId = certId;
            document.getElementById('participantName').value = '';
            document.getElementById('participantInst').value = '';
            document.getElementById('participantRole').value = '';
            document.getElementById('participantKeterangan').value = '';
            document.getElementById('participantAlerts').innerHTML = '';
            document.getElementById('participantsModal').classList.add('active');
        }

        function generatePdfForParticipant() {
            if (!currentCertId) return;

            const name = document.getElementById('participantName').value.trim();
            if (!name) {
                showAlert('participantAlerts', 'Participant name is required', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'generate_pdf');
            formData.append('cert_id', currentCertId);
            formData.append('participant_name', name);
            formData.append('institution', document.getElementById('participantInst').value.trim());
            formData.append('role', document.getElementById('participantRole').value.trim());
            formData.append('keterangan', document.getElementById('participantKeterangan').value.trim());

            fetch('builder_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('participantAlerts', 'PDF generated successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = data.download_url;
                        closeModal('participantsModal');
                    }, 1000);
                } else {
                    showAlert('participantAlerts', data.message || 'Error generating PDF', 'error');
                }
            })
            .catch(error => {
                showAlert('participantAlerts', 'Error: ' + error.message, 'error');
            });
        }

        function deleteCertificate(certId) {
            if (!confirm('Are you sure you want to delete this certificate? This action cannot be undone.')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', certId);

            fetch('builder_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadCertificates();
                } else {
                    alert(data.message || 'Error deleting certificate');
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function openNewCertificateModal() {
            document.getElementById('newCertName').value = '';
            document.getElementById('newCertDesc').value = '';
            document.getElementById('newCertModal').classList.add('active');
        }

        function createNewCertificate() {
            const name = document.getElementById('newCertName').value.trim();
            if (!name) {
                alert('Certificate name is required');
                return;
            }

            // Create a new certificate with empty design
            const emptyDesign = {
                version: '5.3.0',
                objects: [],
                background: '#ffffff',
                width: 800,
                height: 566
            };

            const formData = new FormData();
            formData.append('action', 'save');
            formData.append('name', name);
            formData.append('description', document.getElementById('newCertDesc').value.trim());
            formData.append('design_data', JSON.stringify(emptyDesign));
            formData.append('canvas_width', 800);
            formData.append('canvas_height', 566);

            fetch('builder_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = `builder.php?edit=${data.cert_id}`;
                } else {
                    alert(data.message || 'Error creating certificate');
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function showAlert(elementId, message, type) {
            const alertHtml = `<div class="alert ${type}"><strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}</div>`;
            document.getElementById(elementId).innerHTML = alertHtml;
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    </script>
</body>
</html>
