<style>
    .project-header {
        background: linear-gradient(135deg, #004ADF 0%, #00A2E9 100%);
        color: #fff;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .project-info h1 {
        font-size: 28px;
        font-weight: 600;
        margin: 0 0 8px 0;
    }

    .project-meta {
        display: flex;
        gap: 24px;
        font-size: 14px;
        opacity: 0.9;
    }

    .documentation-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .project-name {
        font-size: 24px;
        font-weight: 600;
        color: #2c3e50;
    }

    .controls-section {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn-upload {
        background: #00AEEF !important;
        color: #fff !important;
        border: none !important;
        border-radius: 8px;
        padding: 12px 24px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none !important;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }

    .btn-upload:hover {
        background: #0080c7 !important;
        color: #fff !important;
        text-decoration: none !important;
    }

    .period-select {
        background: #00AEEF;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 16px;
        font-weight: 500;
        min-width: 200px;
        cursor: pointer;
    }

    .period-select option {
        background: #004ADF;
        color: #fff;
    }

    /* Photo Grid Layout */
    .photos-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-top: 24px;
    }

    .photo-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
    }

    .photo-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }

    .photo-container {
        aspect-ratio: 4/3;
        background: #f1f1f1;
        position: relative;
        cursor: pointer;
        overflow: hidden;
    }

    .photo-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity 0.2s;
    }

    .photo-placeholder {
        width: 100%;
        height: 100%;
        background: #f1f1f1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 14px;
        text-align: center;
        padding: 16px;
    }

    .photo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
        font-size: 14px;
        text-align: center;
        pointer-events: none;
    }

    .photo-container:hover .photo-overlay {
        opacity: 1;
    }

    /* Photo description area dengan actions */
    .photo-description {
        padding: 12px 16px;
        background: #fff;
        border-top: 1px solid #f0f0f0;
        position: relative;
    }

    .photo-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }

    .photo-text {
        flex: 1;
        min-width: 0;
    }

    .photo-title {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
        line-height: 1.3;
        min-height: 18px;
    }

    .photo-desc-text {
        font-size: 13px;
        color: #666;
        line-height: 1.4;
        word-wrap: break-word;
        min-height: 36px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Photo actions di area deskripsi */
    .photo-actions {
        display: flex;
        flex-direction: column;
        gap: 2px;
        opacity: 0;
        transition: opacity 0.2s;
        flex-shrink: 0;
    }

    .photo-card:hover .photo-actions {
        opacity: 1;
    }

    .btn-photo-action {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        color: #6c757d;
        transition: all 0.2s;
    }

    .btn-photo-action:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-photo-action.download {
        color: #28a745;
        border-color: #28a745;
    }

    .btn-photo-action.download:hover {
        background: #28a745;
        color: white;
    }

    .btn-photo-action.edit {
        color: #ffc107;
        border-color: #ffc107;
    }

    .btn-photo-action.edit:hover {
        background: #ffc107;
        color: #212529;
    }

    .btn-photo-action.delete {
        color: #dc3545;
        border-color: #dc3545;
    }

    .btn-photo-action.delete:hover {
        background: #dc3545;
        color: white;
    }

    /* Empty state */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
        background: #f8f9fa;
        border-radius: 12px;
        border: 2px dashed #e0e7ef;
    }

    .empty-icon {
        font-size: 48px;
        color: #00AEEF;
        margin-bottom: 16px;
    }

    .empty-text {
        font-size: 18px;
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: 600;
    }

    .empty-subtext {
        font-size: 14px;
        opacity: 0.7;
        margin-bottom: 20px;
    }

    .empty-upload-btn {
        background: #00AEEF;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }

    .empty-upload-btn:hover {
        background: #0080c7;
    }

    /* Upload Modal */
    .upload-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1001;
        align-items: center;
        justify-content: center;
    }

    .upload-modal.active {
        display: flex;
    }

    .upload-modal-content {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        min-width: 600px;
        max-width: 800px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .upload-modal-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .upload-area {
        border: 2px dashed #00AEEF;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        background: rgba(0, 174, 239, 0.05);
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 20px;
    }

    .upload-area:hover {
        border-color: #0080c7;
        background: rgba(0, 174, 239, 0.1);
    }

    .upload-area.dragover {
        border-color: #004ADF;
        background: rgba(0, 174, 239, 0.15);
    }

    .upload-icon {
        font-size: 48px;
        color: #00AEEF;
        margin-bottom: 16px;
    }

    .upload-text {
        font-size: 16px;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .upload-subtext {
        font-size: 14px;
        color: #6c757d;
    }

    .selected-files {
        margin-top: 20px;
        max-height: 400px;
        overflow-y: auto;
    }

    .file-item {
        display: flex;
        align-items: flex-start;
        padding: 16px;
        border: 1px solid #e0e7ef;
        border-radius: 8px;
        margin-bottom: 12px;
        background: #f8f9fa;
        position: relative;
    }

    .file-preview {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
        margin-right: 16px;
        flex-shrink: 0;
    }

    .file-info {
        flex: 1;
        min-width: 0;
    }

    .file-name {
        font-weight: 600;
        margin-bottom: 12px;
        font-size: 14px;
        word-break: break-word;
    }

    .file-remove {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #e74c3c;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
    }

    .file-remove:hover {
        background: #c0392b;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: #2c3e50;
        font-size: 14px;
    }

    .form-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e0e7ef;
        border-radius: 6px;
        font-size: 14px;
        resize: vertical;
    }

    .form-input:focus {
        outline: none;
        border-color: #00AEEF;
        box-shadow: 0 0 0 3px rgba(0, 174, 239, 0.1);
    }

    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e0e7ef;
    }

    .btn-secondary {
        background: #6c757d;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 14px;
        cursor: pointer;
    }

    .btn-primary {
        background: #00AEEF;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 14px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #0080c7;
    }

    .btn-primary:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    /* Enhanced Edit Modal */
    .edit-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1002;
        align-items: center;
        justify-content: center;
    }

    .edit-modal.active {
        display: flex;
    }

    .edit-modal-content {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        min-width: 500px;
        max-width: 600px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .edit-modal-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    /* Current photo preview in edit modal */
    .current-photo-section {
        margin-bottom: 20px;
    }

    .current-photo-label {
        font-size: 14px;
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 8px;
        display: block;
    }

    .current-photo-preview {
        text-align: center;
        margin-bottom: 16px;
    }

    .current-photo-preview img {
        max-width: 200px;
        max-height: 150px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #e0e7ef;
    }

    /* Replace photo section */
    .replace-photo-section {
        margin-bottom: 20px;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .replace-photo-label {
        font-size: 14px;
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .replace-upload-area {
        border: 2px dashed #6c757d;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 12px;
    }

    .replace-upload-area:hover {
        border-color: #00AEEF;
        background: rgba(0, 174, 239, 0.05);
    }

    .replace-upload-area.dragover {
        border-color: #00AEEF;
        background: rgba(0, 174, 239, 0.1);
    }

    .replace-upload-area.has-file {
        border-color: #28a745;
        background: rgba(40, 167, 69, 0.05);
    }

    .replace-upload-icon {
        font-size: 24px;
        color: #6c757d;
        margin-bottom: 8px;
    }

    .replace-upload-text {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .replace-upload-subtext {
        font-size: 12px;
        color: #9ca3af;
    }

    /* New photo preview */
    .new-photo-preview {
        display: none;
        text-align: center;
        margin-top: 12px;
        padding: 12px;
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e0e7ef;
    }

    .new-photo-preview img {
        max-width: 150px;
        max-height: 120px;
        border-radius: 6px;
        object-fit: cover;
    }

    .new-photo-info {
        margin-top: 8px;
        font-size: 12px;
        color: #6c757d;
    }

    .remove-new-photo {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 12px;
        cursor: pointer;
        margin-top: 8px;
    }

    .remove-new-photo:hover {
        background: #c82333;
    }

    /* Checkbox untuk ganti foto */
    .replace-photo-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        padding: 12px;
        background: #e3f2fd;
        border-radius: 8px;
        border: 1px solid #bbdefb;
    }

    .replace-photo-toggle input[type="checkbox"] {
        width: 16px;
        height: 16px;
    }

    .replace-photo-toggle label {
        font-size: 14px;
        font-weight: 500;
        color: #1976d2;
        cursor: pointer;
        margin: 0;
    }

    /* Hide file input */
    .edit-file-input {
        display: none;
    }

    /* Progress bar untuk edit */
    .edit-progress {
        width: 100%;
        height: 4px;
        background: #e0e7ef;
        border-radius: 2px;
        overflow: hidden;
        margin-top: 12px;
        display: none;
    }

    .edit-progress-bar {
        height: 100%;
        background: #00AEEF;
        width: 0%;
        transition: width 0.3s;
    }

    /* Photo Modal - View Full Size */
    .photo-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.9);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .photo-modal.active {
        display: flex;
    }

    .photo-modal-content {
        max-width: 90vw;
        max-height: 90vh;
        position: relative;
        display: flex;
        flex-direction: column;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
    }

    .photo-modal img {
        max-width: 100%;
        max-height: 70vh;
        object-fit: contain;
        background: #000;
    }

    .photo-modal-close {
        position: absolute;
        top: -40px;
        right: 0;
        background: rgba(0,0,0,0.7);
        border: none;
        color: #fff;
        font-size: 24px;
        cursor: pointer;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .photo-modal-close:hover {
        background: rgba(0,0,0,0.9);
    }

    .photo-modal-info {
        padding: 20px;
        background: #fff;
    }

    .photo-modal-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #2c3e50;
    }

    .photo-modal-description {
        font-size: 14px;
        color: #6c757d;
        line-height: 1.5;
        margin-bottom: 16px;
    }

    .photo-modal-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e0e7ef;
        font-size: 14px;
        color: #6c757d;
    }

    .photo-modal-actions {
        display: flex;
        gap: 8px;
    }

    .photo-modal-btn {
        flex: 1;
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: background 0.2s;
    }

    .photo-modal-btn.download {
        background: #28a745;
        color: white;
    }

    .photo-modal-btn.download:hover {
        background: #218838;
    }

    .photo-modal-btn.edit {
        background: #ffc107;
        color: #212529;
    }

    .photo-modal-btn.edit:hover {
        background: #e0a800;
    }

    .photo-modal-btn.delete {
        background: #dc3545;
        color: white;
    }

    .photo-modal-btn.delete:hover {
        background: #c82333;
    }

    .file-input {
        display: none;
    }

    /* Loading state */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1003;
        display: none;
    }

    .loading-overlay.active {
        display: flex;
    }

    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #00AEEF;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Toast notifications */
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: #fff;
        padding: 12px 20px;
        border-radius: 8px;
        z-index: 1004;
        transform: translateX(100%);
        transition: transform 0.3s;
    }

    .toast.show {
        transform: translateX(0);
    }

    .toast.error {
        background: #dc3545;
    }

    .toast.info {
        background: #17a2b8;
    }

    /* Back Button Styles */
    .back-button {
        background: rgba(255,255,255,0.95) !important;
        color: #004ADF !important;
        border: 2px solid rgba(255,255,255,1) !important;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        margin-right: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .back-button:hover {
        background: rgba(255,255,255,1) !important;
        color: #002e8f !important;
        border-color: rgba(255,255,255,1) !important;
        text-decoration: none !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .back-button i {
        font-size: 16px;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .photos-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 768px) {
        .photos-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .documentation-controls {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .controls-section {
            width: 100%;
            justify-content: space-between;
        }

        .upload-modal-content {
            min-width: 90vw;
            margin: 20px;
        }

        .photo-content {
            flex-direction: column;
            gap: 8px;
        }

        .photo-actions {
            flex-direction: row;
            align-self: flex-end;
        }
    }

    @media (max-width: 480px) {
        .photos-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .delete-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1003;
    align-items: center;
    justify-content: center;
    }

    .delete-modal.active {
        display: flex;
    }

    .delete-modal-content {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        min-width: 400px;
        max-width: 500px;
        text-align: center;
    }

    .delete-modal-icon {
        font-size: 48px;
        color: #dc3545;
        margin-bottom: 16px;
    }

    .delete-modal-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 12px;
        color: #2c3e50;
    }

    .delete-modal-message {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .delete-modal-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .btn-delete-cancel {
        background: #6c757d;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 14px;
        cursor: pointer;
        min-width: 100px;
    }

    .btn-delete-cancel:hover {
        background: #5a6268;
    }

    .btn-delete-confirm {
        background: #dc3545;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 14px;
        cursor: pointer;
        min-width: 100px;
    }

    .btn-delete-confirm:hover {
        background: #c82333;
    }

    .btn-delete-confirm:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
</style>