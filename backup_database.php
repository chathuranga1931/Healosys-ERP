<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once('database.php');

class BackupDatabase {
    private $db;
    private $connection;
    private $backupDir = __DIR__ . '/backups/';
    private $timezone = 'UTC';
    private $dbHost;
    private $dbUser;
    private $dbPass;
    private $dbName;

    public function __construct() {
        $config = include('config.php');
        $this->dbHost = $config['host'];
        $this->dbUser = $config['username'];
        $this->dbPass = $config['password'];
        $this->dbName = $config['database'];

        $this->db = new Database();
        $this->connection = $this->db->getConnection();
        $this->ensureBackupDirExists();
    }

    private function ensureBackupDirExists() {
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0777, true);
        }
    }

    public function getBackups() {
        $backups = [];
        foreach (glob($this->backupDir . '*.sql') as $filename) {
            $backups[] = basename($filename);
        }
        return $backups;
    }

    public function backup() {
        $timezone = new DateTimeZone($this->timezone);
        $dateTime = new DateTime('now', $timezone);
        $timestamp = $dateTime->format('Y-m-d_H-i-s');
        $filename = "{$this->backupDir}{$this->dbName}_{$timestamp}.sql";

        $command = sprintf(
            'mysqldump --host=%s --user=%s --password=%s %s > %s 2>&1',
            escapeshellarg($this->dbHost),
            escapeshellarg($this->dbUser),
            escapeshellarg($this->dbPass),
            escapeshellarg($this->dbName),
            escapeshellarg($filename)
        );

        // Log the command for debugging purposes
        file_put_contents(__DIR__ . '/backup_log.txt', "Running command: $command\n", FILE_APPEND);

        exec($command, $output, $returnVar);

        // Log the output and return code for debugging purposes
        file_put_contents(__DIR__ . '/backup_log.txt', "Output: " . implode("\n", $output) . "\nReturn code: $returnVar\n", FILE_APPEND);

        if ($returnVar !== 0) {
            die('Error exporting database. Check backup_log.txt for details.');
        }

        echo "Database backed up successfully to $filename<br>";
    }

    public function __destruct() {
        $this->connection->close();
    }
}

// Handle the backup request
if (isset($_POST['backup'])) {
    $backup = new BackupDatabase();
    $backup->backup();
    header("Location: backup_database.php");
    exit;
}

// List available backups
$backup = new BackupDatabase();
$backups = $backup->getBackups();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Backup</title>
</head>
<body>

<h1>Database Backup</h1>

<h2>Available Backups</h2>
<ul>
    <?php if (empty($backups)) : ?>
        <li>No backups available.</li>
    <?php else : ?>
        <?php foreach ($backups as $backupFile) : ?>
            <li><a href="backups/<?php echo $backupFile; ?>" download><?php echo $backupFile; ?></a></li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>

<h2>Create a New Backup</h2>
<form method="post">
    <button type="submit" name="backup">Create Backup</button>
</form>

<p><a href="logout.php">Logout</a></p>

</body>
</html>
