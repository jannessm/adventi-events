<?php
/**
 * CSV Upload Page
 * Accessible via: yoursite.com/adventi-events-csv-upload/?ad_ev_secr=YOUR_API_SECRET
 */

if (!defined('ABSPATH')) {
    exit;
}

include_once dirname(__FILE__) . '/page-manager.php';
include_once dirname(__FILE__) . '/ajax-handler.php';

// Register the rewrite rule
function ad_ev_add_csv_upload_rewrite_rule() {
    add_rewrite_rule(
        '^adventi-events-csv-upload/?$',
        'index.php?ad_ev_csv_upload=1',
        'top'
    );
    add_rewrite_tag('%ad_ev_csv_upload%', '([0-1]{1})');
}
add_action('init', 'ad_ev_add_csv_upload_rewrite_rule');

// Handle CSV upload BEFORE template loads
add_action('wp_loaded', function() {
    if (!get_query_var('ad_ev_csv_upload')) {
        return;
    }

    $options = get_option('ad_ev_options');
    $api_secret = $options[AD_EV_FIELD . 'api_secret'] ?? '';

    // Verify API secret from query parameter
    if (empty($_GET['ad_ev_secr']) || $_GET['ad_ev_secr'] !== $api_secret) {
        http_response_code(403);
        wp_die('Unauthorized', 'Unauthorized');
    }

    // Handle file upload
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
        ad_ev_process_csv_upload_external($_FILES['csv_file']);
    } else {
        // Display upload form
        ad_ev_display_csv_upload_form();
    }
    
    // Exit BEFORE theme tries to load
    exit;
});

function ad_ev_process_csv_upload_external($file) {
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        wp_die('Upload error: ' . $file['error']);
    }

    $file_type = wp_check_filetype_and_ext($file['tmp_name'], $file['name']);
    if (!isset($file_type['ext']) || $file_type['ext'] !== 'csv') {
        wp_die('Invalid file type. Please upload a CSV file.');
    }

    // Read file content
    $content = file_get_contents($file['tmp_name']);
    if ($content === false || trim($content) === '') {
        wp_die('CSV file is empty or invalid.');
    }

    // Use existing CSV parsing function from ajax-handler.php
    $events = ad_ev_events_from_uploaded_csv($content);
    
    if (is_wp_error($events)) {
        wp_die('Error: ' . $events->get_error_message());
    }

    if (empty($events)) {
        wp_die('No valid events found in CSV.');
    }

    // Import the events
    $manager = new AdventiEventsPageManager($events);
    $result = $manager->update();

    // Return success response as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'added' => count($result['added']),
        'updated' => count($result['updated']),
        'message' => 'CSV imported successfully',
        'details' => [
            'added' => array_map(function($e) {
                return $e->preacher . ' (' . $e->date->format('d.m.Y H:i') . ')';
            }, $result['added']),
            'updated' => array_map(function($e) {
                return $e->preacher . ' (' . $e->date->format('d.m.Y H:i') . ')';
            }, $result['updated'])
        ]
    ]);
}

function ad_ev_display_csv_upload_form() {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CSV Upload - Adventi Events</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            
            .container {
                background: white;
                border-radius: 8px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.1);
                padding: 40px;
                max-width: 500px;
                width: 100%;
            }
            
            h1 {
                color: #333;
                margin-bottom: 10px;
                font-size: 28px;
            }
            
            .subtitle {
                color: #666;
                margin-bottom: 30px;
                font-size: 14px;
            }
            
            .upload-box {
                border: 2px dashed #ddd;
                border-radius: 8px;
                padding: 40px 20px;
                text-align: center;
                background: #f9f9f9;
                transition: all 0.3s ease;
                cursor: pointer;
            }
            
            .upload-box:hover {
                border-color: #667eea;
                background: #f0f4ff;
            }
            
            .upload-box.drag-over {
                border-color: #667eea;
                background: #f0f4ff;
                transform: scale(1.02);
            }
            
            .upload-icon {
                font-size: 48px;
                margin-bottom: 15px;
                display: block;
            }
            
            label {
                display: block;
                color: #333;
                font-weight: 600;
                margin-bottom: 8px;
            }
            
            .file-input-wrapper {
                position: relative;
                overflow: hidden;
                display: inline-block;
                width: 100%;
            }
            
            input[type="file"] {
                position: absolute;
                left: -9999px;
            }
            
            .file-label {
                display: inline-block;
                padding: 10px 20px;
                background: #667eea;
                color: white;
                border-radius: 4px;
                cursor: pointer;
                font-weight: 500;
                transition: background 0.3s ease;
            }
            
            .file-label:hover {
                background: #5568d3;
            }
            
            .file-name {
                display: block;
                margin-top: 10px;
                color: #666;
                font-size: 14px;
            }
            
            button[type="submit"] {
                width: 100%;
                padding: 12px 20px;
                margin-top: 20px;
                background: #667eea;
                color: white;
                border: none;
                border-radius: 4px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.3s ease;
            }
            
            button[type="submit"]:hover:not(:disabled) {
                background: #5568d3;
            }
            
            button[type="submit"]:disabled {
                background: #ccc;
                cursor: not-allowed;
            }
            
            .response {
                margin-top: 20px;
                padding: 15px;
                border-radius: 4px;
                display: none;
            }
            
            .response.success {
                background: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }
            
            .response.error {
                background: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>📅 Import Events</h1>
            <p class="subtitle">Upload a CSV file to import events</p>
            
            <form method="POST" enctype="multipart/form-data" id="upload-form">
                <div class="upload-box" id="upload-box">
                    <span class="upload-icon">📁</span>
                    <label for="csv_file">Select CSV File</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="csv_file" id="csv_file" accept=".csv,text/csv" required>
                        <label for="csv_file" class="file-label">Choose File</label>
                        <span class="file-name" id="file-name"></span>
                    </div>
                </div>
                
                <button type="submit" id="submit-btn">Upload & Import</button>
                <div class="response" id="response"></div>
            </form>
        </div>

        <script>
            const fileInput = document.getElementById('csv_file');
            const fileName = document.getElementById('file-name');
            const uploadBox = document.getElementById('upload-box');
            const form = document.getElementById('upload-form');
            const submitBtn = document.getElementById('submit-btn');
            const response = document.getElementById('response');

            // File selection
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    fileName.textContent = e.target.files[0].name;
                }
            });

            // Drag and drop
            uploadBox.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadBox.classList.add('drag-over');
            });

            uploadBox.addEventListener('dragleave', () => {
                uploadBox.classList.remove('drag-over');
            });

            uploadBox.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadBox.classList.remove('drag-over');
                fileInput.files = e.dataTransfer.files;
                if (fileInput.files.length > 0) {
                    fileName.textContent = fileInput.files[0].name;
                }
            });

            // Form submission
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const formData = new FormData(form);
                submitBtn.disabled = true;
                response.style.display = 'none';

                try {
                    const result = await fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    });

                    const data = await result.json();
                    
                    response.className = 'response success';
                    response.innerHTML = `
                        <strong>✓ Success!</strong><br>
                        Added: ${data.added} events<br>
                        Updated: ${data.updated} events<br>
                        ${data.details.added.length > 0 ? '<br><strong>Added:</strong><ul>' + data.details.added.map(e => '<li>' + e + '</li>').join('') + '</ul>' : ''}
                        ${data.details.updated.length > 0 ? '<strong>Updated:</strong><ul>' + data.details.updated.map(e => '<li>' + e + '</li>').join('') + '</ul>' : ''}
                    `;
                    response.style.display = 'block';
                    form.reset();
                    fileName.textContent = '';
                } catch (error) {
                    response.className = 'response error';
                    response.textContent = '❌ Error: ' + error.message;
                    response.style.display = 'block';
                } finally {
                    submitBtn.disabled = false;
                }
            });
        </script>
    </body>
    </html>
    <?php
}