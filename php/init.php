<?php
// php/init.php - Script de inicialización del sistema

require_once 'database.php';
require_once 'auth.php';
require_once 'config.php';

// Aplicar configuraciones del sistema
aplicarConfiguracionesSistema();

// Obtener configuración del nombre del sistema
$nombre_sistema = obtenerConfiguracion('general', 'nombre_sistema', 'Civitur Travel');

// Obtener configuración de registros por página
$registros_por_pagina = obtenerConfiguracion('general', 'registros_pagina', 25);