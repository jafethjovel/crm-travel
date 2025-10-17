<?php
// includes/country-flags.php - Componente para mostrar banderas de países

function obtenerBanderaPorPais($pais) {
    $codigosPaises = [
        'El Salvador' => 'sv',
        'Estados Unidos' => 'us',
        'España' => 'es',
        'México' => 'mx',
        'Canadá' => 'ca',
        'Reino Unido' => 'gb',
        'Francia' => 'fr',
        'Alemania' => 'de',
        'Italia' => 'it',
        'Japón' => 'jp',
        'China' => 'cn',
        'Australia' => 'au',
        'Brasil' => 'br',
        'Argentina' => 'ar',
        'Chile' => 'cl',
        'Colombia' => 'co',
        'Perú' => 'pe',
        'Tailandia' => 'th',
        'Singapur' => 'sg',
        'India' => 'in'
    ];
    
    $codigo = $codigosPaises[$pais] ?? 'unknown';
    return "https://flagcdn.com/w40/$codigo.png";
}

function mostrarBanderaPais($pais, $size = '20px') {
    $urlBandera = obtenerBanderaPorPais($pais);
    return "<img src='$urlBandera' width='$size' height='$size' class='me-2' alt='Bandera de $pais' title='$pais'>";
}