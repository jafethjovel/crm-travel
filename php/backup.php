<?php
session_start();
require_once 'database.php';
require_once 'auth.php';

// Verificar permisos
verificarAutenticacion();
verificarPermisos(ROLES_ADMIN);

// Directorio de backups
$backup_dir = __DIR__ . '/../backups/';
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Nombre del archivo de backup
$backup_file = $backup_dir . 'backup-' . date('Y-m-d-H-i-s') . '.sql';

// Comando para hacer backup de MySQL
$command = "mysqldump --user=" . DB_USERNAME . " --password=" . DB_PASSWORD . " --host=" . DB_HOST . " " . DB_NAME . " > " . $backup_file;

// Ejecutar el comando
system($command, $output);

if ($output === 0) {
    // Comprimir el backup
    $compressed_file = $backup_file . '.gz';
    $data = file_get_contents($backup_file);
    $gzdata = gzencode($data, 9);
    file_put_contents($compressed_file, $gzdata);
    
    // Eliminar el archivo sin comprimir
    unlink($backup_file);
    
    echo json_encode([
        'success' => true,
        'message' => 'Backup creado exitosamente',
        'file' => basename($compressed_file)
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear el backup'
    ]);
}
?>