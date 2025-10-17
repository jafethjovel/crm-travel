<?php
// php/config.php - Funciones para manejar configuraciones del sistema

/**
 * Obtener todas las configuraciones de una sección
 */
function obtenerConfiguraciones($seccion) {
    global $conn;
    $configuraciones = [];
    
    if ($stmt = $conn->prepare("SELECT clave, valor, tipo FROM configuraciones WHERE seccion = ?")) {
        $stmt->bind_param("s", $seccion);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            // Convertir el valor según el tipo
            switch ($row['tipo']) {
                case 'number':
                    $configuraciones[$row['clave']] = is_numeric($row['valor']) ? (int)$row['valor'] : $row['valor'];
                    break;
                case 'boolean':
                    $configuraciones[$row['clave']] = (bool)$row['valor'];
                    break;
                case 'json':
                    $configuraciones[$row['clave']] = json_decode($row['valor'], true);
                    break;
                default:
                    $configuraciones[$row['clave']] = $row['valor'];
            }
        }
        $stmt->close();
    }
    
    return $configuraciones;
}

/**
 * Guardar una configuración
 */
function guardarConfiguracion($seccion, $clave, $valor, $tipo = 'string', $descripcion = '') {
    global $conn;
    
    // Verificar si la configuración ya existe
    if ($stmt = $conn->prepare("SELECT id FROM configuraciones WHERE seccion = ? AND clave = ?")) {
        $stmt->bind_param("ss", $seccion, $clave);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // Actualizar configuración existente
            $stmt->close();
            if ($stmt = $conn->prepare("UPDATE configuraciones SET valor = ?, tipo = ?, descripcion = ?, updated_at = NOW() WHERE seccion = ? AND clave = ?")) {
                $stmt->bind_param("sssss", $valor, $tipo, $descripcion, $seccion, $clave);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            }
        } else {
            // Insertar nueva configuración
            $stmt->close();
            if ($stmt = $conn->prepare("INSERT INTO configuraciones (seccion, clave, valor, tipo, descripcion) VALUES (?, ?, ?, ?, ?)")) {
                $stmt->bind_param("sssss", $seccion, $clave, $valor, $tipo, $descripcion);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            }
        }
    }
    
    return false;
}

/**
 * Guardar múltiples configuraciones a la vez
 */
function guardarConfiguraciones($seccion, $configuraciones, $tipos = []) {
    $resultados = [];
    
    foreach ($configuraciones as $clave => $valor) {
        $tipo = $tipos[$clave] ?? 'string';
        $resultados[$clave] = guardarConfiguracion($seccion, $clave, $valor, $tipo);
    }
    
    return $resultados;
}

/**
 * Obtener una configuración específica
 */
function obtenerConfiguracion($seccion, $clave, $valor_default = '') {
    global $conn;
    
    if ($stmt = $conn->prepare("SELECT valor, tipo FROM configuraciones WHERE seccion = ? AND clave = ?")) {
        $stmt->bind_param("ss", $seccion, $clave);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            
            // Convertir el valor según el tipo
            switch ($row['tipo']) {
                case 'number':
                    return is_numeric($row['valor']) ? (int)$row['valor'] : $row['valor'];
                case 'boolean':
                    return (bool)$row['valor'];
                case 'json':
                    return json_decode($row['valor'], true);
                default:
                    return $row['valor'];
            }
        }
        $stmt->close();
    }
    
    return $valor_default;
}

/**
 * Aplicar configuraciones al sistema
 */
function aplicarConfiguracionesSistema() {
    // Configurar zona horaria
    $timezone = obtenerConfiguracion('general', 'timezone', 'America/El_Salvador');
    date_default_timezone_set($timezone);
    
    // Configurar locale según el idioma
    $idioma = obtenerConfiguracion('general', 'idioma', 'es');
    setlocale(LC_TIME, $idioma . '_' . strtoupper($idioma) . '.UTF-6');
    
    // Otras configuraciones globales pueden aplicarse aquí
}