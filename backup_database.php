<?php

require_once('Database.php');

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

        $command = "mysqldump --host={$this->dbHost} --user={$this->dbUser} --password={$this->dbPass} {$this->dbName} > {$filename}";

        system($command, $output);

        if ($output !== 0) {
            die('Error exporting database: ' . $output);
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

</body>
</html>
