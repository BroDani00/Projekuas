<?php
// debug.php - Tools untuk debugging dan informasi sistem

// Pastikan hanya bisa diakses di localhost/development
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die('Debug mode hanya tersedia di localhost!');
}

// Error reporting maksimal
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();
ob_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Panel - Manajemen Tugas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Consolas', 'Monaco', monospace;
        }
        
        body {
            background: #1a1a1a;
            color: #f0f0f0;
            line-height: 1.6;
            padding: 20px;
            font-size: 14px;
        }
        
        .debug-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .debug-header {
            background: linear-gradient(135deg, #ff6b8b, #ff8e9e);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(255, 107, 139, 0.3);
        }
        
        .debug-header h1 {
            font-size: 24px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .debug-header h1 i {
            font-size: 28px;
        }
        
        .debug-nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            padding: 15px;
            background: #2a2a2a;
            border-radius: 8px;
        }
        
        .debug-nav button {
            background: #3a3a3a;
            color: #f0f0f0;
            border: 1px solid #4a4a4a;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .debug-nav button:hover {
            background: #4a4a4a;
            border-color: #ff6b8b;
        }
        
        .debug-nav button.active {
            background: #ff6b8b;
            color: white;
            border-color: #ff6b8b;
        }
        
        .debug-section {
            background: #2a2a2a;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            display: none;
        }
        
        .debug-section.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .section-title {
            color: #ff6b8b;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3a3a3a;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-card {
            background: #3a3a3a;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #ff6b8b;
        }
        
        .info-card h3 {
            color: #ff8e9e;
            margin-bottom: 8px;
            font-size: 16px;
        }
        
        .info-content {
            color: #ccc;
            font-family: 'Consolas', monospace;
            word-break: break-all;
        }
        
        .info-content pre {
            white-space: pre-wrap;
            background: #1a1a1a;
            padding: 10px;
            border-radius: 5px;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 8px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 5px;
        }
        
        .status-success {
            background: #4CAF50;
            color: white;
        }
        
        .status-error {
            background: #f44336;
            color: white;
        }
        
        .status-warning {
            background: #ff9800;
            color: white;
        }
        
        .status-info {
            background: #2196f3;
            color: white;
        }
        
        .query-result {
            background: #1a1a1a;
            border: 1px solid #4a4a4a;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 10px;
        }
        
        .query-header {
            background: #3a3a3a;
            padding: 10px 15px;
            font-weight: bold;
            color: #ff8e9e;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .query-body {
            max-height: 400px;
            overflow-y: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        
        th {
            background: #4a4a4a;
            color: #ff8e9e;
            padding: 10px;
            text-align: left;
            position: sticky;
            top: 0;
        }
        
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #3a3a3a;
        }
        
        tr:hover {
            background: #3a3a3a;
        }
        
        .btn {
            background: #ff6b8b;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn:hover {
            background: #ff4d6d;
        }
        
        .btn-danger {
            background: #f44336;
        }
        
        .btn-danger:hover {
            background: #d32f2f;
        }
        
        .sql-test {
            background: #3a3a3a;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
        
        .sql-test textarea {
            width: 100%;
            background: #1a1a1a;
            color: #f0f0f0;
            border: 1px solid #4a4a4a;
            padding: 10px;
            border-radius: 5px;
            font-family: 'Consolas', monospace;
            resize: vertical;
            min-height: 100px;
            margin-bottom: 10px;
        }
        
        .sql-test button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .sql-test button:hover {
            background: #45a049;
        }
        
        .log-item {
            padding: 10px;
            border-bottom: 1px solid #3a3a3a;
            font-family: 'Consolas', monospace;
            font-size: 13px;
        }
        
        .log-time {
            color: #4CAF50;
        }
        
        .log-message {
            color: #f0f0f0;
        }
        
        .log-error {
            color: #f44336;
        }
        
        .log-warning {
            color: #ff9800;
        }
        
        .log-info {
            color: #2196f3;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="debug-container">
        <!-- Header -->
        <div class="debug-header">
            <h1>
                <i class="fas fa-bug"></i>
                Debug Panel - Manajemen Tugas Harian
            </h1>
            <p>Development Tools | Only accessible from localhost</p>
        </div>
        
        <!-- Navigation -->
        <div class="debug-nav">
            <button onclick="showSection('system')" class="active">
                <i class="fas fa-info-circle"></i> System Info
            </button>
            <button onclick="showSection('database')">
                <i class="fas fa-database"></i> Database
            </button>
            <button onclick="showSection('session')">
                <i class="fas fa-user-circle"></i> Session & Server
            </button>
            <button onclick="showSection('phpinfo')">
                <i class="fas fa-cogs"></i> PHP Info
            </button>
            <button onclick="showSection('tests')">
                <i class="fas fa-vial"></i> Tests
            </button>
            <button onclick="showSection('logs')">
                <i class="fas fa-clipboard-list"></i> Logs
            </button>
            <button onclick="location.reload()" class="btn-danger">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
        
        <!-- System Info Section -->
        <div id="system-section" class="debug-section active">
            <h2 class="section-title"><i class="fas fa-info-circle"></i> System Information</h2>
            
            <div class="info-grid">
                <div class="info-card">
                    <h3>Application Status</h3>
                    <div class="info-content">
                        <span class="status-badge status-success">RUNNING</span>
                        <span class="status-badge status-info">DEBUG MODE</span>
                        <div style="margin-top: 10px;">
                            <strong>Base URL:</strong> <?php echo 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']); ?><br>
                            <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?><br>
                            <strong>Project Path:</strong> <?php echo __DIR__; ?>
                        </div>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>PHP Configuration</h3>
                    <div class="info-content">
                        <strong>PHP Version:</strong> <?php echo phpversion(); ?><br>
                        <strong>Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?><br>
                        <strong>Max Execution Time:</strong> <?php echo ini_get('max_execution_time'); ?>s<br>
                        <strong>Upload Max Filesize:</strong> <?php echo ini_get('upload_max_filesize'); ?><br>
                        <strong>Post Max Size:</strong> <?php echo ini_get('post_max_size'); ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>Database Connection</h3>
                    <div class="info-content">
                        <?php
                        try {
                            include 'includes/koneksi.php';
                            echo '<span class="status-badge status-success">CONNECTED</span><br>';
                            echo '<strong>Host:</strong> ' . $host . '<br>';
                            echo '<strong>Database:</strong> ' . $db . '<br>';
                            echo '<strong>MySQL Version:</strong> ' . mysqli_get_server_info($conn) . '<br>';
                            echo '<strong>Charset:</strong> ' . mysqli_character_set_name($conn);
                            
                            // Test query
                            $test_query = "SELECT 1 as test";
                            if (mysqli_query($conn, $test_query)) {
                                echo '<br><span class="status-badge status-success">QUERY TEST PASSED</span>';
                            }
                        } catch (Exception $e) {
                            echo '<span class="status-badge status-error">CONNECTION FAILED</span><br>';
                            echo '<strong>Error:</strong> ' . $e->getMessage();
                        }
                        ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>File Permissions</h3>
                    <div class="info-content">
                        <?php
                        $files_to_check = [
                            'includes/koneksi.php' => 'Database config',
                            'proses.php' => 'CRUD operations',
                            'assets/' => 'Assets folder',
                            'pages/' => 'Pages folder'
                        ];
                        
                        foreach ($files_to_check as $file => $desc) {
                            $status = file_exists($file) ? 
                                (is_writable($file) ? 'status-success' : 'status-warning') : 
                                'status-error';
                            $text = file_exists($file) ? 
                                (is_writable($file) ? 'WRITABLE' : 'READ-ONLY') : 
                                'NOT FOUND';
                            echo "<span class='status-badge $status'>$text</span> $desc<br>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Database Section -->
        <div id="database-section" class="debug-section">
            <h2 class="section-title"><i class="fas fa-database"></i> Database Information</h2>
            
            <?php
            if ($conn) {
                // Show all tables
                $tables_query = "SHOW TABLES";
                $tables_result = mysqli_query($conn, $tables_query);
                $tables = [];
                
                while ($row = mysqli_fetch_array($tables_result)) {
                    $tables[] = $row[0];
                }
                
                echo '<div class="info-grid">';
                foreach ($tables as $table) {
                    echo '<div class="info-card">';
                    echo "<h3>Table: $table</h3>";
                    
                    // Get table structure
                    $structure_query = "DESCRIBE $table";
                    $structure_result = mysqli_query($conn, $structure_query);
                    
                    echo '<div class="info-content">';
                    echo '<strong>Columns:</strong><br>';
                    while ($col = mysqli_fetch_assoc($structure_result)) {
                        echo "• {$col['Field']} ({$col['Type']})";
                        if ($col['Key'] == 'PRI') echo ' <span class="status-badge status-info">PK</span>';
                        if ($col['Null'] == 'NO') echo ' <span class="status-badge status-warning">NOT NULL</span>';
                        echo '<br>';
                    }
                    
                    // Count rows
                    $count_query = "SELECT COUNT(*) as total FROM $table";
                    $count_result = mysqli_query($conn, $count_query);
                    $count = mysqli_fetch_assoc($count_result)['total'];
                    echo "<strong>Total Rows:</strong> $count";
                    
                    echo '</div></div>';
                }
                echo '</div>';
                
                // Query tester
                echo '<div class="sql-test">';
                echo '<h3><i class="fas fa-terminal"></i> SQL Query Tester</h3>';
                echo '<form method="POST">';
                echo '<textarea name="sql_query" placeholder="SELECT * FROM tugas LIMIT 10">';
                echo isset($_POST['sql_query']) ? htmlspecialchars($_POST['sql_query']) : '';
                echo '</textarea><br>';
                echo '<button type="submit" name="run_query"><i class="fas fa-play"></i> Run Query</button>';
                echo '</form>';
                
                if (isset($_POST['run_query']) && !empty($_POST['sql_query'])) {
                    $query = $_POST['sql_query'];
                    echo '<div class="query-result">';
                    echo '<div class="query-header">';
                    echo 'Query Result';
                    echo '<span>' . date('H:i:s') . '</span>';
                    echo '</div>';
                    echo '<div class="query-body">';
                    
                    try {
                        $start_time = microtime(true);
                        $result = mysqli_query($conn, $query);
                        $end_time = microtime(true);
                        $execution_time = round(($end_time - $start_time) * 1000, 2);
                        
                        if ($result) {
                            if (mysqli_num_rows($result) > 0) {
                                echo '<table>';
                                // Header
                                echo '<thead><tr>';
                                while ($field = mysqli_fetch_field($result)) {
                                    echo '<th>' . $field->name . '</th>';
                                }
                                echo '</tr></thead>';
                                
                                // Data
                                echo '<tbody>';
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<tr>';
                                    foreach ($row as $value) {
                                        echo '<td>' . htmlspecialchars($value ?? 'NULL') . '</td>';
                                    }
                                    echo '</tr>';
                                }
                                echo '</tbody></table>';
                                
                                echo "<div style='padding: 10px; background: #2a2a2a;'>";
                                echo "<strong>Rows returned:</strong> " . mysqli_num_rows($result);
                                echo " | <strong>Execution time:</strong> {$execution_time}ms";
                                echo '</div>';
                            } else {
                                echo '<div style="padding: 20px; text-align: center; color: #ff9800;">';
                                echo '<i class="fas fa-info-circle"></i> Query executed successfully but returned no rows.';
                                echo "<br><strong>Affected rows:</strong> " . mysqli_affected_rows($conn);
                                echo '</div>';
                            }
                        } else {
                            echo '<div style="padding: 20px; color: #f44336;">';
                            echo '<i class="fas fa-exclamation-triangle"></i> Query failed: ' . mysqli_error($conn);
                            echo '</div>';
                        }
                    } catch (Exception $e) {
                        echo '<div style="padding: 20px; color: #f44336;">';
                        echo '<i class="fas fa-exclamation-triangle"></i> Error: ' . $e->getMessage();
                        echo '</div>';
                    }
                    
                    echo '</div></div>';
                }
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Session & Server Section -->
        <div id="session-section" class="debug-section">
            <h2 class="section-title"><i class="fas fa-user-circle"></i> Session & Server Variables</h2>
            
            <div class="info-grid">
                <div class="info-card">
                    <h3>Session Data</h3>
                    <div class="info-content">
                        <?php
                        if (session_status() === PHP_SESSION_ACTIVE && !empty($_SESSION)) {
                            echo '<pre>' . print_r($_SESSION, true) . '</pre>';
                        } else {
                            echo '<span class="status-badge status-warning">NO SESSION DATA</span>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>POST Data</h3>
                    <div class="info-content">
                        <?php
                        if (!empty($_POST)) {
                            echo '<pre>' . print_r($_POST, true) . '</pre>';
                        } else {
                            echo '<span class="status-badge status-info">NO POST DATA</span>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>GET Data</h3>
                    <div class="info-content">
                        <?php
                        if (!empty($_GET)) {
                            echo '<pre>' . print_r($_GET, true) . '</pre>';
                        } else {
                            echo '<span class="status-badge status-info">NO GET DATA</span>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>Server Variables</h3>
                    <div class="info-content">
                        <strong>REMOTE_ADDR:</strong> <?php echo $_SERVER['REMOTE_ADDR']; ?><br>
                        <strong>SERVER_NAME:</strong> <?php echo $_SERVER['SERVER_NAME']; ?><br>
                        <strong>REQUEST_METHOD:</strong> <?php echo $_SERVER['REQUEST_METHOD']; ?><br>
                        <strong>REQUEST_URI:</strong> <?php echo $_SERVER['REQUEST_URI']; ?><br>
                        <strong>HTTP_USER_AGENT:</strong> <?php echo $_SERVER['HTTP_USER_AGENT']; ?><br>
                        <strong>SCRIPT_FILENAME:</strong> <?php echo $_SERVER['SCRIPT_FILENAME']; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- PHP Info Section -->
        <div id="phpinfo-section" class="debug-section">
            <h2 class="section-title"><i class="fas fa-cogs"></i> PHP Information</h2>
            
            <div class="info-grid">
                <div class="info-card">
                    <h3>Loaded Extensions</h3>
                    <div class="info-content">
                        <?php
                        $extensions = get_loaded_extensions();
                        sort($extensions);
                        foreach ($extensions as $ext) {
                            echo "• $ext<br>";
                        }
                        ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>PHP Environment</h3>
                    <div class="info-content">
                        <?php
                        echo '<pre>';
                        print_r([
                            'PHP_SAPI' => PHP_SAPI,
                            'PHP_OS' => PHP_OS,
                            'DEFAULT_INCLUDE_PATH' => DEFAULT_INCLUDE_PATH,
                            'PEAR_INSTALL_DIR' => PEAR_INSTALL_DIR,
                            'PEAR_EXTENSION_DIR' => PEAR_EXTENSION_DIR,
                            'PHP_EXTENSION_DIR' => PHP_EXTENSION_DIR,
                            'PHP_PREFIX' => PHP_PREFIX,
                            'PHP_BINDIR' => PHP_BINDIR
                        ]);
                        echo '</pre>';
                        ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>Ini Settings</h3>
                    <div class="info-content">
                        <?php
                        $important_ini = [
                            'error_reporting', 'display_errors', 'log_errors',
                            'error_log', 'max_execution_time', 'memory_limit',
                            'post_max_size', 'upload_max_filesize',
                            'session.save_handler', 'session.save_path',
                            'date.timezone', 'default_charset'
                        ];
                        
                        foreach ($important_ini as $ini) {
                            $value = ini_get($ini);
                            echo "<strong>$ini:</strong> $value<br>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 20px;">
                <a href="?phpinfo=1" class="btn">
                    <i class="fas fa-external-link-alt"></i> View Full phpinfo()
                </a>
                <?php
                if (isset($_GET['phpinfo'])) {
                    echo '<div style="margin-top: 20px; background: white; color: black; padding: 20px;">';
                    ob_start();
                    phpinfo();
                    $phpinfo = ob_get_clean();
                    echo $phpinfo;
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        
        <!-- Tests Section -->
        <div id="tests-section" class="debug-section">
            <h2 class="section-title"><i class="fas fa-vial"></i> System Tests</h2>
            
            <div class="info-grid">
                <div class="info-card">
                    <h3>Database Tests</h3>
                    <div class="info-content">
                        <?php
                        if ($conn) {
                            echo '<div style="margin-bottom: 10px;">';
                            
                            // Test 1: Connection
                            echo '<span class="status-badge status-success">✓</span> Database Connection<br>';
                            
                            // Test 2: Table exists
                            $table_test = mysqli_query($conn, "SHOW TABLES LIKE 'tugas'");
                            if (mysqli_num_rows($table_test) > 0) {
                                echo '<span class="status-badge status-success">✓</span> Table "tugas" exists<br>';
                            } else {
                                echo '<span class="status-badge status-error">✗</span> Table "tugas" missing<br>';
                            }
                            
                            // Test 3: Insert test
                            $test_insert = "INSERT INTO tugas (judul, deskripsi, deadline, status) 
                                          VALUES ('Test Task', 'Debug test task', CURDATE(), 'Pending')";
                            if (mysqli_query($conn, $test_insert)) {
                                $test_id = mysqli_insert_id($conn);
                                echo '<span class="status-badge status-success">✓</span> Insert test passed (ID: ' . $test_id . ')<br>';
                                
                                // Clean up
                                mysqli_query($conn, "DELETE FROM tugas WHERE id = $test_id");
                            } else {
                                echo '<span class="status-badge status-error">✗</span> Insert test failed<br>';
                            }
                            
                            echo '</div>';
                        }
                        ?>
                        <form method="POST" style="margin-top: 10px;">
                            <button type="submit" name="run_db_tests" class="btn">
                                <i class="fas fa-redo"></i> Run Database Tests
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>File System Tests</h3>
                    <div class="info-content">
                        <?php
                        $test_dirs = ['includes', 'assets', 'assets/css', 'assets/js', 'pages'];
                        foreach ($test_dirs as $dir) {
                            if (is_dir($dir) && is_readable($dir)) {
                                echo '<span class="status-badge status-success">✓</span> Directory: ' . $dir . '<br>';
                            } else {
                                echo '<span class="status-badge status-error">✗</span> Directory: ' . $dir . '<br>';
                            }
                        }
                        
                        $test_files = ['includes/koneksi.php', 'proses.php', 'index.php'];
                        foreach ($test_files as $file) {
                            if (file_exists($file) && is_readable($file)) {
                                echo '<span class="status-badge status-success">✓</span> File: ' . $file . '<br>';
                            } else {
                                echo '<span class="status-badge status-error">✗</span> File: ' . $file . '<br>';
                            }
                        }
                        ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>Function Tests</h3>
                    <div class="info-content">
                        <?php
                        // Test various PHP functions
                        $functions_to_test = [
                            'mysqli_connect' => 'Database',
                            'file_get_contents' => 'File read',
                            'json_encode' => 'JSON',
                            'session_start' => 'Session',
                            'date' => 'Date/time'
                        ];
                        
                        foreach ($functions_to_test as $func => $desc) {
                            if (function_exists($func)) {
                                echo '<span class="status-badge status-success">✓</span> ' . $desc . ' (' . $func . ')<br>';
                            } else {
                                echo '<span class="status-badge status-error">✗</span> ' . $desc . ' (' . $func . ')<br>';
                            }
                        }
                        ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>Quick Actions</h3>
                    <div class="info-content">
                        <form method="POST" style="display: flex; flex-direction: column; gap: 10px;">
                            <button type="submit" name="clear_sessions" class="btn">
                                <i class="fas fa-trash"></i> Clear All Sessions
                            </button>
                            
                            <button type="submit" name="reset_sample_data" class="btn" onclick="return confirm('Reset semua data ke sample?')">
                                <i class="fas fa-database"></i> Reset to Sample Data
                            </button>
                            
                            <button type="submit" name="check_updates" class="btn">
                                <i class="fas fa-sync"></i> Check for Updates
                            </button>
                        </form>
                        
                        <?php
                        if (isset($_POST['clear_sessions'])) {
                            session_destroy();
                            echo '<div class="log-item log-info">';
                            echo '<span class="log-time">[' . date('H:i:s') . ']</span> ';
                            echo '<span class="log-message">Sessions cleared</span>';
                            echo '</div>';
                        }
                        
                        if (isset($_POST['reset_sample_data']) && $conn) {
                            // Delete all data
                            mysqli_query($conn, "DELETE FROM tugas");
                            
                            // Re-insert sample data
                            include 'database.sql';
                            echo '<div class="log-item log-info">';
                            echo '<span class="log-time">[' . date('H:i:s') . ']</span> ';
                            echo '<span class="log-message">Sample data reset</span>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Logs Section -->
        <div id="logs-section" class="debug-section">
            <h2 class="section-title"><i class="fas fa-clipboard-list"></i> System Logs</h2>
            
            <div class="info-grid">
                <div class="info-card">
                    <h3>Error Log</h3>
                    <div class="info-content">
                        <?php
                        $error_log = ini_get('error_log');
                        if ($error_log && file_exists($error_log)) {
                            $log_content = file_get_contents($error_log);
                            $log_lines = array_slice(explode("\n", $log_content), -20); // Last 20 lines
                            
                            foreach ($log_lines as $line) {
                                if (!empty(trim($line))) {
                                    echo '<div class="log-item">';
                                    echo htmlspecialchars($line);
                                    echo '</div>';
                                }
                            }
                        } else {
                            echo '<span class="status-badge status-info">No error log found</span>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>Custom Logs</h3>
                    <div class="info-content">
                        <div class="log-item log-info">
                            <span class="log-time">[<?php echo date('H:i:s'); ?>]</span>
                            <span class="log-message">Debug panel accessed</span>
                        </div>
                        <div class="log-item log-info">
                            <span class="log-time">[<?php echo date('H:i:s'); ?>]</span>
                            <span class="log-message">PHP Version: <?php echo phpversion(); ?></span>
                        </div>
                        <?php
                        if ($conn) {
                            echo '<div class="log-item log-info">';
                            echo '<span class="log-time">[' . date('H:i:s') . ']</span> ';
                            echo '<span class="log-message">Database connected: ' . mysqli_get_server_info($conn) . '</span>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 20px;">
                <form method="POST">
                    <textarea name="custom_log" placeholder="Add custom log message..." style="width: 100%; height: 80px; margin-bottom: 10px;"></textarea>
                    <button type="submit" name="add_log" class="btn">
                        <i class="fas fa-plus"></i> Add Log Entry
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.debug-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.debug-nav button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(sectionId + '-section').classList.add('active');
            
            // Mark button as active
            event.target.classList.add('active');
            
            // Store in URL hash
            window.location.hash = sectionId;
        }
        
        // Load section from URL hash
        window.addEventListener('load', function() {
            const hash = window.location.hash.substring(1);
            if (hash) {
                showSection(hash);
            }
        });
        
        // Auto-refresh logs every 30 seconds
        setInterval(function() {
            if (document.getElementById('logs-section').classList.contains('active')) {
                location.reload();
            }
        }, 30000);
    </script>
</body>
</html>
<?php
ob_end_flush();
?>