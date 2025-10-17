<?php
// php/currency-helpers.php - Funciones para manejo de monedas internacionales

/**
 * Obtener tipos de moneda aceptados
 */
function obtenerMonedasAceptadas() {
    return [
        'USD' => ['símbolo' => '$', 'nombre' => 'Dólar Estadounidense', 'países' => ['Estados Unidos', 'El Salvador', 'Ecuador', 'Panamá']],
        'EUR' => ['símbolo' => '€', 'nombre' => 'Euro', 'países' => ['España', 'Francia', 'Alemania', 'Italia', 'Países Bajos']],
        'GBP' => ['símbolo' => '£', 'nombre' => 'Libra Esterlina', 'países' => ['Reino Unido']],
        'JPY' => ['símbolo' => '¥', 'nombre' => 'Yen Japonés', 'países' => ['Japón']],
        'CAD' => ['símbolo' => 'C$', 'nombre' => 'Dólar Canadiense', 'países' => ['Canadá']],
        'AUD' => ['símbolo' => 'A$', 'nombre' => 'Dólar Australiano', 'países' => ['Australia']],
        'MXN' => ['símbolo' => 'MX$', 'nombre' => 'Peso Mexicano', 'países' => ['México']],
        'BRL' => ['símbolo' => 'R$', 'nombre' => 'Real Brasileño', 'países' => ['Brasil']],
        'CNY' => ['símbolo' => '¥', 'nombre' => 'Yuan Chino', 'países' => ['China']],
        'SGD' => ['símbolo' => 'S$', 'nombre' => 'Dólar de Singapur', 'países' => ['Singapur']],
        'THB' => ['símbolo' => '฿', 'nombre' => 'Baht Tailandés', 'países' => ['Tailandia']]
    ];
}

/**
 * Formatear precio según la moneda
 */
function formatearPrecio($precio, $moneda = 'USD') {
    $monedas = obtenerMonedasAceptadas();
    $simbolo = $monedas[$moneda]['símbolo'] ?? '$';
    
    return $simbolo . number_format($precio, 2) . ' ' . $moneda;
}

/**
 * Convertir entre monedas (usando tasas de cambio aproximadas)
 */
function convertirMoneda($monto, $deMoneda, $aMoneda) {
    // Tasas de cambio aproximadas (en una app real, usarías una API)
    $tasas = [
        'USD' => 1.00,
        'EUR' => 0.93,
        'GBP' => 0.79,
        'JPY' => 147.50,
        'CAD' => 1.36,
        'AUD' => 1.54,
        'MXN' => 17.20,
        'BRL' => 5.20,
        'CNY' => 7.25,
        'SGD' => 1.35,
        'THB' => 35.50
    ];
    
    if (!isset($tasas[$deMoneda]) || !isset($tasas[$aMoneda])) {
        return $monto; // Devolver sin conversión si no se encuentra la moneda
    }
    
    // Convertir a USD primero, luego a la moneda destino
    $enUSD = $monto / $tasas[$deMoneda];
    return $enUSD * $tasas[$aMoneda];
}

/**
 * Obtener moneda predeterminada para un país
 */
function obtenerMonedaPorPais($pais) {
    $monedasPorPais = [
        'Estados Unidos' => 'USD', 'El Salvador' => 'USD', 'Ecuador' => 'USD', 'Panamá' => 'USD',
        'España' => 'EUR', 'Francia' => 'EUR', 'Alemania' => 'EUR', 'Italia' => 'EUR',
        'Reino Unido' => 'GBP', 'Japón' => 'JPY', 'Canadá' => 'CAD', 'Australia' => 'AUD',
        'México' => 'MXN', 'Brasil' => 'BRL', 'China' => 'CNY', 'Singapur' => 'SGD',
        'Tailandia' => 'THB'
    ];
    
    return $monedasPorPais[$pais] ?? 'USD';
}

// Agregar estas funciones al final del archivo
function calcularSubtotal($servicios) {
    $subtotal = 0;
    foreach ($servicios as $servicio) {
        $subtotal += $servicio['subtotal'];
    }
    return $subtotal;
}

function calcularImpuestos($subtotal, $porcentaje = 13) {
    return $subtotal * ($porcentaje / 100);
}

function calcularTotal($subtotal, $impuestos) {
    return $subtotal + $impuestos;
}

function generarCodigoCotizacion() {
    return 'COT-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}