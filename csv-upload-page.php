<?php
/**
 * CSV Upload Page
 * Accessible via: yoursite.com/adventi-events-csv-upload/
 */

if (!defined('ABSPATH')) {
    exit;
}

include_once dirname(__FILE__) . '/page-manager.php';
include_once dirname(__FILE__) . '/data-extractor.php';

// Register the rewrite rule and flush on activation
function ad_ev_add_csv_upload_rewrite_rule() {
    add_rewrite_rule(
        '^adventi-events-csv-upload/?$',
        'index.php?ad_ev_csv_upload=1',
        'top'
    );
    add_rewrite_tag('%ad_ev_csv_upload%', '([0-1]{1})');
}
add_action('init', 'ad_ev_add_csv_upload_rewrite_rule');

// Handle the CSV upload page request
add_action('template_redirect', function() {
    if (!get_query_var('ad_ev_csv_upload')) {
        return;
    }

    $options = get_option('ad_ev_options');
    $api_secret = $options[AD_EV_FIELD . 'api_secret'] ?? '';

    // Verify API secret from query parameter
    if (empty($_GET['ad_ev_secr']) || $_GET['ad_ev_secr'] !== $api_secret) {
        wp_die('Unauthorized', 'Unauthorized', array('response' => 403));
    }

    // Handle file upload
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
        ad_ev_process_csv_upload($_FILES['csv_file']);
        die;
    }

    // Display upload form
    ad_ev_display_csv_upload_form();
    exit;
});

function ad_ev_process_csv_upload($file) {
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        wp_die('Upload error: ' . $file['error']);
    }

    if ($file['type'] !== 'text/csv' && !str_ends_with($file['name'], '.csv')) {
        wp_die('Invalid file type. Please upload a CSV file.');
    }

    // Read and parse CSV
    $csv_data = file_get_contents($file['tmp_name']);
    $lines = explode("\n", $csv_data);
    $lines = array_map(function($line) {
        return array_map(function($entry) {
            return trim($entry, '"');
        }, explode(";", $line));
    }, $lines);

    // Create events from CSV
    $events = ad_ev_parse_csv_lines($lines);
    
    if (empty($events)) {
        wp_die('No valid events found in CSV.');
    }

    $manager = new AdventiEventsPageManager($events);
    $result = $manager->update();

    // Return success response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'added' => count($result['added']),
        'updated' => count($result['updated']),
        'message' => 'CSV imported successfully'
    ]);
}

function ad_ev_parse_csv_lines($lines) {
    // Implement your CSV parsing logic similar to data-extractor.php
    // This is a simplified version - adjust based on your CSV format
    $events = [];
    
    foreach (array_slice($lines, 4) as $line) {
        if (!is_array($line) || count($line) < 4) {
            continue;
        }

        // Parse event data from CSV line
        $event_data = ad_ev_create_event_from_csv_line($line);
        if ($event_data) {
            $events[] = $event_data;
        }
    }

    return $events;
}

function ad_ev_create_event_from_csv_line($line) {
    // Implement based on your CSV structure
    // Return AdventiEvent object or null
    return null;
}

function ad_ev_display_csv_upload_form() {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>CSV Upload - Adventi Events</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                max-width: 600px;
                margin: 50px auto;
                padding: 20px;
            }
            .upload-box {
                border: 2px dashed #ccc;
                padding: 40px;
                text-align: center;
                border-radius: 5px;
            }
            input[type="file"], button {
                padding: 10px 20px;
                margin: 10px 0;
            }
            .success { color: green; }
            .error { color: red; }
        </style>
    </head>
    <body>
        <h1>Import Events from CSV</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="upload-box">
                <label for="csv_file">Select CSV File:</label><br>
                <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                <button type="submit">Upload & Import</button>
            </div>
        </form>
    </body>
    </html>
    <?php
}