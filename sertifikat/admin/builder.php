<?php require_once 'check_login.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Certificate Builder - Sistem Sertifikat</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
        }

        .builder-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid #ddd;
            overflow-y: auto;
            box-shadow: 2px 0 8px rgba(0,0,0,0.05);
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 2px solid #667eea;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .sidebar-header h2 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .sidebar-section {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .sidebar-section h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #667eea;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .tool-btn {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 10px 12px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            margin-bottom: 8px;
            transition: all 0.2s;
            text-align: left;
        }

        .tool-btn:hover {
            background: #f0f4ff;
            border-color: #667eea;
            color: #667eea;
        }

        .tool-btn i {
            margin-right: 8px;
            width: 16px;
        }

        /* Canvas Area */
        .canvas-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #f5f7fa;
        }

        .toolbar {
            background: white;
            border-bottom: 1px solid #ddd;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .toolbar-left {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .toolbar-right {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .action-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .action-btn.secondary {
            background: #f0f4ff;
            color: #667eea;
            border: 1px solid #667eea;
        }

        .action-btn.secondary:hover {
            background: #e8ecff;
        }

        .action-btn.danger {
            background: #fee;
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }

        .action-btn.danger:hover {
            background: #fdd;
        }

        .canvas-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow: auto;
        }

        #canvas {
            background: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            cursor: default;
        }

        .properties-panel {
            width: 280px;
            background: white;
            border-left: 1px solid #ddd;
            overflow-y: auto;
            box-shadow: -2px 0 8px rgba(0,0,0,0.05);
        }

        .property-section {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .property-section h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #667eea;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .property-item {
            margin-bottom: 12px;
        }

        .property-item label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        .property-item input,
        .property-item select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 12px;
            font-family: inherit;
        }

        .property-item input:focus,
        .property-item select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }

        .color-picker-wrapper {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .color-picker-wrapper input[type="color"] {
            width: 50px;
            height: 35px;
            cursor: pointer;
        }

        .color-value {
            flex: 1;
            font-size: 12px;
            color: #666;
            word-break: break-all;
        }

        /* Placeholder Menu */
        .placeholder-menu {
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .placeholder-menu-content {
            background: white;
            border-radius: 8px;
            padding: 20px;
            min-width: 280px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.2s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: scale(0.95);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .placeholder-menu-content h4 {
            margin-bottom: 15px;
            color: #333;
            font-size: 14px;
            text-align: center;
        }

        .placeholder-option {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 12px;
            margin-bottom: 8px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            color: #333;
            transition: all 0.2s;
            font-family: inherit;
            text-align: left;
        }

        .placeholder-option:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .placeholder-option i {
            margin-right: 10px;
            width: 16px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }

        .modal.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
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

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
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

        .alert {
            padding: 12px 16px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 13px;
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

        .tabs {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #eee;
            margin-bottom: 15px;
        }

        .tab-button {
            padding: 10px 15px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-weight: 600;
            color: #999;
            font-size: 13px;
            transition: all 0.2s;
        }

        .tab-button.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        @media (max-width: 1200px) {
            .sidebar {
                width: 200px;
            }
            .properties-panel {
                width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="builder-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Certificate Builder</h2>
                <small>Canva-like Designer</small>
            </div>

            <!-- Shapes -->
            <div class="sidebar-section">
                <h3>Shapes</h3>
                <button class="tool-btn" onclick="addRectangle()">
                    <i class="fas fa-square"></i> Rectangle
                </button>
                <button class="tool-btn" onclick="addCircle()">
                    <i class="fas fa-circle"></i> Circle
                </button>
                <button class="tool-btn" onclick="addLine()">
                    <i class="fas fa-minus"></i> Line
                </button>
            </div>

            <!-- Text -->
            <div class="sidebar-section">
                <h3>Text</h3>
                <button class="tool-btn" onclick="addText()">
                    <i class="fas fa-font"></i> Add Text
                </button>
                <button class="tool-btn" onclick="showPlaceholderMenu()">
                    <i class="fas fa-tag"></i> Add Placeholder
                </button>
            </div>

            <!-- Upload -->
            <div class="sidebar-section">
                <h3>Media</h3>
                <button class="tool-btn" onclick="document.getElementById('imageUpload').click()">
                    <i class="fas fa-image"></i> Upload Image
                </button>
                <input type="file" id="imageUpload" accept="image/*" style="display:none" onchange="addImage(event)">
            </div>

            <!-- Canvas Actions -->
            <div class="sidebar-section">
                <h3>Canvas</h3>
                <button class="tool-btn" onclick="changeCanvasSize()">
                    <i class="fas fa-expand"></i> Canvas Size
                </button>
                <button class="tool-btn" onclick="changeCanvasColor()">
                    <i class="fas fa-fill-drip"></i> Background
                </button>
                <button class="tool-btn" onclick="clearCanvas()">
                    <i class="fas fa-trash"></i> Clear
                </button>
            </div>
        </div>

        <!-- Main Canvas Area -->
        <div class="canvas-area">
            <!-- Toolbar -->
            <div class="toolbar">
                <div class="toolbar-left">
                    <h3 style="margin:0; color:#333; font-size:16px;">
                        <span id="designName">New Certificate</span>
                    </h3>
                </div>
                <div class="toolbar-right">
                    <button class="action-btn secondary" onclick="undoAction()">
                        <i class="fas fa-undo"></i> Undo
                    </button>
                    <button class="action-btn secondary" onclick="redoAction()">
                        <i class="fas fa-redo"></i> Redo
                    </button>
                    <button class="action-btn secondary" onclick="downloadDesign()">
                        <i class="fas fa-download"></i> Export
                    </button>
                    <button class="action-btn primary" onclick="saveCertificateDesign()">
                        <i class="fas fa-save"></i> Save Design
                    </button>
                    <button class="action-btn danger" onclick="goBack()">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                </div>
            </div>

            <!-- Canvas -->
            <div class="canvas-wrapper">
                <canvas id="canvas" width="800" height="566"></canvas>
            </div>
        </div>

        <!-- Properties Panel -->
        <div class="properties-panel" id="propertiesPanel">
            <div style="padding:15px; text-align:center; color:#999;">
                <p><i class="fas fa-mouse"></i></p>
                <p style="font-size:12px;">Select an object to edit properties</p>
            </div>
        </div>
    </div>

    <!-- Save Modal -->
    <div id="saveModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Save Certificate Design</div>
            <div id="saveAlerts"></div>
            <div class="form-group">
                <label>Design Name *</label>
                <input type="text" id="designNameInput" placeholder="e.g., Seminar Certificate 2026" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea id="designDescInput" placeholder="Optional description..."></textarea>
            </div>
            <div class="tabs">
                <button class="tab-button active" onclick="switchTab('save-private')">Private</button>
                <button class="tab-button" onclick="switchTab('save-template')">Save as Template</button>
            </div>
            <div id="save-private" style="display:block;">
                <p style="font-size:12px; color:#666; margin-bottom:15px;">
                    This certificate will be saved for your use only.
                </p>
            </div>
            <div id="save-template" style="display:none;">
                <p style="font-size:12px; color:#666; margin-bottom:15px;">
                    Save this as a template that you can reuse for multiple participants.
                </p>
            </div>
            <div class="modal-footer">
                <button class="action-btn secondary" onclick="closeModal('saveModal')">Cancel</button>
                <button class="action-btn primary" onclick="confirmSave()">Save Design</button>
            </div>
        </div>
    </div>

    <!-- Add Participants Modal -->
    <div id="participantsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Add Participants</div>
            <div id="participantsAlerts"></div>
            <div class="form-group">
                <label>Participant Name *</label>
                <input type="text" id="participantName" placeholder="Full name" required>
            </div>
            <div class="form-group">
                <label>Institution/Organization</label>
                <input type="text" id="participantInstitution" placeholder="Company or organization">
            </div>
            <div class="form-group">
                <label>Role/Title</label>
                <input type="text" id="participantRole" placeholder="Position or role">
            </div>
            <div class="modal-footer">
                <button class="action-btn secondary" onclick="closeModal('participantsModal')">Cancel</button>
                <button class="action-btn primary" onclick="confirmAddParticipant()">Add & Generate PDF</button>
            </div>
        </div>
    </div>

    <script>
        let canvas;
        let history = [];
        let historyStep = 0;
        const maxHistorySteps = 50;
        let currentCertificateId = null;

        // Initialize Fabric Canvas
        document.addEventListener('DOMContentLoaded', function() {
            canvas = new fabric.Canvas('canvas', {
                width: 800,
                height: 566,
                backgroundColor: 'white'
            });

            // Set default paper size (landscape A4)
            updateCanvasSize(800, 566);

            // Setup event listeners
            canvas.on('object:added', saveHistory);
            canvas.on('object:modified', saveHistory);
            canvas.on('object:removed', saveHistory);
            canvas.on('selection:created', updatePropertiesPanel);
            canvas.on('selection:updated', updatePropertiesPanel);
            canvas.on('selection:cleared', clearPropertiesPanel);

            // Initial history save
            saveHistory();

            // Load design if editing
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('edit')) {
                loadDesignForEditing(urlParams.get('edit'));
            }
        });

        // Shape Adding Functions
        function addRectangle() {
            const rect = new fabric.Rect({
                left: 100,
                top: 100,
                width: 200,
                height: 100,
                fill: '#667eea',
                stroke: '#333',
                strokeWidth: 2
            });
            canvas.add(rect);
            canvas.setActiveObject(rect);
            canvas.renderAll();
        }

        function addCircle() {
            const circle = new fabric.Circle({
                left: 100,
                top: 100,
                radius: 50,
                fill: '#764ba2',
                stroke: '#333',
                strokeWidth: 2
            });
            canvas.add(circle);
            canvas.setActiveObject(circle);
            canvas.renderAll();
        }

        function addLine() {
            const line = new fabric.Line([50, 100], [200, 100], {
                stroke: '#333',
                strokeWidth: 3,
                fill: 'transparent'
            });
            canvas.add(line);
            canvas.setActiveObject(line);
            canvas.renderAll();
        }

        // Text Functions
        function addText() {
            const text = new fabric.IText('Sample Text', {
                left: 100,
                top: 100,
                fontSize: 24,
                fill: '#333',
                fontFamily: 'Arial'
            });
            canvas.add(text);
            canvas.setActiveObject(text);
            canvas.renderAll();
        }

        function addPlaceholder() {
            const placeholder = new fabric.IText('[NAMA_PESERTA]', {
                left: 100,
                top: 100,
                fontSize: 32,
                fill: '#000',
                fontFamily: 'Arial',
                fontWeight: 'bold',
                backgroundColor: '#fff3cd'
            });
            canvas.add(placeholder);
            canvas.setActiveObject(placeholder);
            canvas.renderAll();
        }

        // Placeholder Menu dengan pilihan tipe
        function showPlaceholderMenu() {
            const menu = document.createElement('div');
            menu.className = 'placeholder-menu';
            menu.innerHTML = `
                <div class="placeholder-menu-content">
                    <h4>Pilih Placeholder:</h4>
                    <button class="placeholder-option" onclick="addPlaceholderType('nama')">
                        <i class="fas fa-user"></i> Nama Peserta
                    </button>
                    <button class="placeholder-option" onclick="addPlaceholderType('instansi')">
                        <i class="fas fa-building"></i> Institusi
                    </button>
                    <button class="placeholder-option" onclick="addPlaceholderType('peran')">
                        <i class="fas fa-briefcase"></i> Peran / Jabatan
                    </button>
                    <button class="placeholder-option" onclick="addPlaceholderType('keterangan')">
                        <i class="fas fa-file-alt"></i> Keterangan
                    </button>
                    <hr style="margin: 10px 0; border: none; border-top: 1px solid #ddd;">
                    <button class="placeholder-option" style="background: #f8f9fa; color: #666;" onclick="closePlaceholderMenu()">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            `;
            document.body.appendChild(menu);
            menu.style.display = 'block';
        }

        function closePlaceholderMenu() {
            const menu = document.querySelector('.placeholder-menu');
            if (menu) menu.remove();
        }

        function addPlaceholderType(type) {
            const placeholderMap = {
                'nama': { text: '{nama}', label: 'Nama Peserta' },
                'instansi': { text: '{instansi}', label: 'Institusi' },
                'peran': { text: '{peran}', label: 'Peran/Jabatan' },
                'keterangan': { text: '{keterangan}', label: 'Keterangan' }
            };

            const config = placeholderMap[type] || placeholderMap['nama'];

            const placeholder = new fabric.IText(config.text, {
                left: 100,
                top: 100,
                fontSize: 32,
                fill: '#000',
                fontFamily: 'Arial',
                fontWeight: 'bold',
                backgroundColor: '#fff3cd',
                metadata: {
                    type: 'placeholder',
                    placeholderType: type,
                    label: config.label
                }
            });
            
            canvas.add(placeholder);
            canvas.setActiveObject(placeholder);
            canvas.renderAll();
            closePlaceholderMenu();
        }

        // Image Upload
        function addImage(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                fabric.Image.fromURL(e.target.result, function(img) {
                    img.scaleToWidth(200);
                    canvas.add(img);
                    canvas.setActiveObject(img);
                    canvas.renderAll();
                });
            };
            reader.readAsDataURL(file);
        }

        // Properties Panel
        function updatePropertiesPanel() {
            const activeObject = canvas.getActiveObject();
            if (!activeObject) return;

            let html = '<div class="property-section">';
            html += '<h3>Position & Size</h3>';
            html += '<div class="property-item">';
            html += '<label>X Position</label>';
            html += `<input type="number" value="${Math.round(activeObject.left)}" onchange="updateObjectProperty('left', this.value)">`;
            html += '</div>';
            html += '<div class="property-item">';
            html += '<label>Y Position</label>';
            html += `<input type="number" value="${Math.round(activeObject.top)}" onchange="updateObjectProperty('top', this.value)">`;
            html += '</div>';
            html += '<div class="property-item">';
            html += '<label>Width</label>';
            html += `<input type="number" value="${Math.round(activeObject.width)}" onchange="updateObjectProperty('width', this.value)">`;
            html += '</div>';
            html += '<div class="property-item">';
            html += '<label>Height</label>';
            html += `<input type="number" value="${Math.round(activeObject.height)}" onchange="updateObjectProperty('height', this.value)">`;
            html += '</div>';
            html += '</div>';

            // Fill & Stroke
            if (activeObject.fill) {
                html += '<div class="property-section">';
                html += '<h3>Style</h3>';
                html += '<div class="property-item">';
                html += '<label>Fill Color</label>';
                html += '<div class="color-picker-wrapper">';
                html += `<input type="color" value="${getColorValue(activeObject.fill)}" onchange="updateObjectProperty('fill', this.value)">`;
                html += `<span class="color-value">${getColorValue(activeObject.fill)}</span>`;
                html += '</div></div>';

                if (activeObject.stroke) {
                    html += '<div class="property-item">';
                    html += '<label>Stroke Color</label>';
                    html += '<div class="color-picker-wrapper">';
                    html += `<input type="color" value="${getColorValue(activeObject.stroke)}" onchange="updateObjectProperty('stroke', this.value)">`;
                    html += `<span class="color-value">${getColorValue(activeObject.stroke)}</span>`;
                    html += '</div></div>';

                    html += '<div class="property-item">';
                    html += '<label>Stroke Width</label>';
                    html += `<input type="number" value="${activeObject.strokeWidth || 1}" onchange="updateObjectProperty('strokeWidth', this.value)">`;
                    html += '</div>';
                }
                html += '</div>';
            }

            // Text Properties
            if (activeObject.type === 'text' || activeObject.type === 'i-text') {
                html += '<div class="property-section">';
                html += '<h3>Text</h3>';
                html += '<div class="property-item">';
                html += '<label>Font Size</label>';
                html += `<input type="number" value="${activeObject.fontSize}" onchange="updateObjectProperty('fontSize', this.value)">`;
                html += '</div>';
                html += '<div class="property-item">';
                html += '<label>Font Family</label>';
                html += `<select onchange="updateObjectProperty('fontFamily', this.value)">
                    <option value="Arial" ${activeObject.fontFamily === 'Arial' ? 'selected' : ''}>Arial</option>
                    <option value="Courier" ${activeObject.fontFamily === 'Courier' ? 'selected' : ''}>Courier</option>
                    <option value="Georgia" ${activeObject.fontFamily === 'Georgia' ? 'selected' : ''}>Georgia</option>
                    <option value="Times" ${activeObject.fontFamily === 'Times' ? 'selected' : ''}>Times New Roman</option>
                    <option value="Verdana" ${activeObject.fontFamily === 'Verdana' ? 'selected' : ''}>Verdana</option>
                </select>`;
                html += '</div>';
                html += '</div>';
            }

            html += '<div class="property-section">';
            html += '<h3>Actions</h3>';
            html += '<button class="tool-btn" onclick="deleteObject()"><i class="fas fa-trash"></i> Delete</button>';
            html += '<button class="tool-btn" onclick="duplicateObject()"><i class="fas fa-copy"></i> Duplicate</button>';
            html += '</div>';

            document.getElementById('propertiesPanel').innerHTML = html;
        }

        function clearPropertiesPanel() {
            document.getElementById('propertiesPanel').innerHTML = `
                <div style="padding:15px; text-align:center; color:#999;">
                    <p><i class="fas fa-mouse"></i></p>
                    <p style="font-size:12px;">Select an object to edit properties</p>
                </div>
            `;
        }

        function updateObjectProperty(property, value) {
            const activeObject = canvas.getActiveObject();
            if (!activeObject) return;

            if (['left', 'top', 'width', 'height', 'fontSize', 'strokeWidth'].includes(property)) {
                activeObject[property] = parseInt(value);
            } else {
                activeObject[property] = value;
            }

            canvas.renderAll();
            saveHistory();
        }

        function getColorValue(color) {
            if (!color) return '#000000';
            if (color.startsWith('#')) return color;
            // Convert rgb/rgba to hex if needed
            return color;
        }

        function deleteObject() {
            const activeObject = canvas.getActiveObject();
            if (activeObject) {
                canvas.remove(activeObject);
                canvas.renderAll();
                saveHistory();
            }
        }

        function duplicateObject() {
            const activeObject = canvas.getActiveObject();
            if (!activeObject) return;

            const cloned = fabric.util.object.clone(activeObject);
            cloned.left += 10;
            cloned.top += 10;
            canvas.add(cloned);
            canvas.setActiveObject(cloned);
            canvas.renderAll();
            saveHistory();
        }

        // Canvas Controls
        function changeCanvasSize() {
            const sizes = [
                { label: 'A4 Landscape (800x566)', w: 800, h: 566 },
                { label: 'A4 Portrait (566x800)', w: 566, h: 800 },
                { label: 'Custom Size', w: 0, h: 0 }
            ];

            let size = prompt('Select canvas size:\n1. A4 Landscape (800x566)\n2. A4 Portrait (566x800)\n3. Custom (enter as: width,height)');
            if (!size) return;

            if (size === '1') {
                updateCanvasSize(800, 566);
            } else if (size === '2') {
                updateCanvasSize(566, 800);
            } else if (size.includes(',')) {
                const [w, h] = size.split(',').map(x => parseInt(x.trim()));
                if (w > 0 && h > 0) {
                    updateCanvasSize(w, h);
                }
            }
        }

        function updateCanvasSize(width, height) {
            canvas.setWidth(width);
            canvas.setHeight(height);
            canvas.renderAll();
            saveHistory();
        }

        function changeCanvasColor() {
            const color = prompt('Enter background color (hex, rgb, or color name):');
            if (color) {
                canvas.backgroundColor = color;
                canvas.renderAll();
                saveHistory();
            }
        }

        function clearCanvas() {
            if (confirm('Are you sure you want to clear the entire canvas?')) {
                canvas.clear();
                canvas.backgroundColor = 'white';
                canvas.renderAll();
                saveHistory();
            }
        }

        // History Management
        function saveHistory() {
            historyStep++;
            if (historyStep < history.length) {
                history.length = historyStep;
            }
            history.push(JSON.stringify(canvas.toJSON()));
            if (history.length > maxHistorySteps) {
                history.shift();
                historyStep--;
            }
        }

        function undoAction() {
            if (historyStep > 0) {
                historyStep--;
                loadHistory(history[historyStep]);
            }
        }

        function redoAction() {
            if (historyStep < history.length - 1) {
                historyStep++;
                loadHistory(history[historyStep]);
            }
        }

        function loadHistory(json) {
            canvas.loadFromJSON(json, function() {
                canvas.renderAll();
            });
        }

        // Save & Export
        function saveCertificateDesign() {
            document.getElementById('designNameInput').value = '';
            document.getElementById('designDescInput').value = '';
            document.getElementById('saveAlerts').innerHTML = '';
            document.getElementById('saveModal').classList.add('active');
        }

        function confirmSave() {
            const name = document.getElementById('designNameInput').value.trim();
            if (!name) {
                showAlert('saveAlerts', 'Design name is required', 'error');
                return;
            }

            const description = document.getElementById('designDescInput').value.trim();
            const isTemplate = document.getElementById('save-template').style.display !== 'none';
            const designData = JSON.stringify(canvas.toJSON());

            const formData = new FormData();
            formData.append('action', 'save');
            formData.append('name', name);
            formData.append('description', description);
            formData.append('is_template', isTemplate ? 1 : 0);
            formData.append('design_data', designData);
            formData.append('canvas_width', canvas.width);
            formData.append('canvas_height', canvas.height);

            fetch('builder_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('saveAlerts', 'Design saved successfully!', 'success');
                    currentCertificateId = data.cert_id;
                    setTimeout(() => {
                        closeModal('saveModal');
                        document.getElementById('designName').textContent = name;
                    }, 1000);
                } else {
                    showAlert('saveAlerts', data.message || 'Error saving design', 'error');
                }
            })
            .catch(error => {
                showAlert('saveAlerts', 'Error: ' + error.message, 'error');
            });
        }

        function downloadDesign() {
            const filename = document.getElementById('designName').textContent.replace(/\s+/g, '_') + '.json';
            const dataStr = JSON.stringify(canvas.toJSON(), null, 2);
            const dataBlob = new Blob([dataStr], { type: 'application/json' });
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.click();
            URL.revokeObjectURL(url);
        }

        // Modal & UI Helpers
        function switchTab(tabId) {
            document.querySelectorAll('[id^="save-"]').forEach(el => el.style.display = 'none');
            document.getElementById(tabId).style.display = 'block';

            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function showAlert(elementId, message, type) {
            const alertHtml = `<div class="alert ${type}"><strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}</div>`;
            document.getElementById(elementId).innerHTML = alertHtml;
        }

        function loadDesignForEditing(designId) {
            // Load design from server
            fetch(`builder_api.php?action=load&id=${designId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentCertificateId = data.cert_id;
                        document.getElementById('designName').textContent = data.name;
                        canvas.loadFromJSON(data.design_data, function() {
                            canvas.renderAll();
                            canvas.setWidth(data.canvas_width);
                            canvas.setHeight(data.canvas_height);
                        });
                    }
                });
        }

        function goBack() {
            window.location.href = 'custom_certificates.php';
        }
    </script>
</body>
</html>
