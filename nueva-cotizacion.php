<?php
session_start();
require_once __DIR__ . '/php/database.php';
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/cotizaciones-crud.php';

// Verificar permisos
verificarAutenticacion();
verificarPermisos(ROLES_VENDEDOR);

$page_title = "Nueva Cotización";
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Generar código de cotización
$codigo_cotizacion = "COT-" . date("Ymd") . "-" . rand(100, 999);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Nueva Cotización <?php echo $codigo_cotizacion; ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="cotizaciones.php" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

<form id="form-cotizacion" action="php/guardar-cotizacion.php" method="POST">
    <input type="hidden" name="codigo_cotizacion" value="<?php echo $codigo_cotizacion; ?>">
    
    <!-- Información del Solicitante -->
<div class="card card-civit mb-4">
    <div class="card-header card-header-civit">
        <h5 class="mb-0">Información del Solicitante</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Tipo de Cliente *</label>
                    <select class="form-select" name="tipo_cliente" id="tipo-cliente" required>
                        <option value="nuevo">Cliente Nuevo</option>
                        <option value="existente">Cliente Existente</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3" id="campo-cliente-existente" style="display: none;">
                    <label class="form-label">Seleccionar Cliente Existente</label>
                    <select class="form-select" name="cliente_existente_id" id="cliente-existente">
                        <option value="">Buscar cliente...</option>
                        <?php
                        // Cargar clientes existentes
                        $clientes_stmt = $conn->prepare("SELECT id, nombre, email, numero_documento FROM clientes ORDER BY nombre");
                        $clientes_stmt->execute();
                        $clientes = $clientes_stmt->get_result();
                        
                        while ($cliente = $clientes->fetch_assoc()) {
                            echo "<option value=\"{$cliente['id']}\">{$cliente['nombre']} - {$cliente['email']} - {$cliente['numero_documento']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Campos para cliente nuevo -->
        <div id="campos-cliente-nuevo">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre completo *</label>
                        <input type="text" class="form-control" name="cliente_nombre" id="cliente_nombre" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="cliente_email" id="cliente_email">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" name="cliente_telefono" id="cliente_telefono">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Documento de identidad</label>
                        <input type="text" class="form-control" name="cliente_documento" id="cliente_documento">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Elegir Tipo de Servicio -->
    <div class="card card-civit mb-4">
        <div class="card-header card-header-civit">
            <h5 class="mb-0">Elegir Tipo de Servicio</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-civit-outline active" data-service-type="vuelos">
                            <i class="fas fa-plane me-1"></i> Vuelos
                        </button>
                        <button type="button" class="btn btn-civit-outline" data-service-type="hoteles">
                            <i class="fas fa-hotel me-1"></i> Hoteles
                        </button>
                        <button type="button" class="btn btn-civit-outline" data-service-type="paquetes">
                            <i class="fas fa-suitcase me-1"></i> Paquetes
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Formulario de Vuelos (visible por defecto) -->
            <div id="formulario-vuelos" class="service-form">
                <h5 class="mb-4">Información del Vuelo</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Aerolínea *</label>
                            <select class="form-select" name="aerolinea" required>
                                <option value="">Seleccionar aerolínea...</option>
                                <option value="iberia">Iberia</option>
                                <option value="avianca">Avianca</option>
                                <option value="united">United Airlines</option>
                                <option value="american">American Airlines</option>
                                <option value="delta">Delta Air Lines</option>
                                <option value="lufthansa">Lufthansa</option>
                                <option value="airfrance">Air France</option>
                                <option value="british">British Airways</option>
                                <option value="latam">LATAM</option>
                                <option value="copa">Copa Airlines</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Vuelo *</label>
                            <select class="form-select" name="tipo_vuelo" required>
                                <option value="">Seleccionar tipo...</option>
                                <option value="ida_vuelta">Ida y Vuelta</option>
                                <option value="solo_ida">Solo Ida</option>
                                <option value="multi_destino">Múltiples Destinos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Origen *</label>
                            <select class="form-select" name="origen" required>
                                <option value="">Seleccionar origen...</option>
                                <option value="SJO">San José, Costa Rica (SJO)</option>
                                <option value="MAD">Madrid, España (MAD)</option>
                                <option value="CDG">París, Francia (CDG)</option>
                                <option value="FRA">Fráncfort, Alemania (FRA)</option>
                                <option value="LHR">Londres, Reino Unido (LHR)</option>
                                <option value="JFK">Nueva York, USA (JFK)</option>
                                <option value="MIA">Miami, USA (MIA)</option>
                                <option value="LAX">Los Ángeles, USA (LAX)</option>
                                <option value="BOG">Bogotá, Colombia (BOG)</option>
                                <option value="MEX">Ciudad de México, México (MEX)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Destino *</label>
                            <select class="form-select" name="destino" required>
                                <option value="">Seleccionar destino...</option>
                                <option value="SJO">San José, Costa Rica (SJO)</option>
                                <option value="MAD">Madrid, España (MAD)</option>
                                <option value="CDG">París, Francia (CDG)</option>
                                <option value="FRA">Fráncfort, Alemania (FRA)</option>
                                <option value="LHR">Londres, Reino Unido (LHR)</option>
                                <option value="JFK">Nueva York, USA (JFK)</option>
                                <option value="MIA">Miami, USA (MIA)</option>
                                <option value="LAX">Los Ángeles, USA (LAX)</option>
                                <option value="BOG">Bogotá, Colombia (BOG)</option>
                                <option value="MEX">Ciudad de México, México (MEX)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Fecha de Salida *</label>
                            <input type="date" class="form-control" name="fecha_salida" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Fecha de Regreso</label>
                            <input type="date" class="form-control" name="fecha_regreso">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Clase *</label>
                            <select class="form-select" name="clase" required>
                                <option value="">Seleccionar clase...</option>
                                <option value="economica">Económica</option>
                                <option value="premium_economy">Premium Economy</option>
                                <option value="ejecutiva">Ejecutiva/Business</option>
                                <option value="primera">Primera Clase</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Adultos *</label>
                            <input type="number" class="form-control" name="adultos" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Precio por persona (USD) *</label>
                            <input type="number" class="form-control" name="precio_vuelo" min="0" step="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-civit-primary" id="btn-agregar-vuelo">
                        <i class="fas fa-plus me-1"></i> Agregar Vuelo
                    </button>
                </div>
            </div>
            
            <!-- Formulario de Hoteles (oculto por defecto) -->
            <div id="formulario-hoteles" class="service-form d-none">
                <h5 class="mb-4">Información del Hotel</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Hotel *</label>
                            <input type="text" class="form-control" name="nombre_hotel">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Ubicación *</label>
                            <input type="text" class="form-control" name="ubicacion_hotel">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Fecha de Check-in *</label>
                            <input type="date" class="form-control" name="checkin">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Fecha de Check-out *</label>
                            <input type="date" class="form-control" name="checkout">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Noches *</label>
                            <input type="number" class="form-control" name="noches" min="1" value="1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Habitaciones *</label>
                            <input type="number" class="form-control" name="habitaciones" min="1" value="1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Adultos *</label>
                            <input type="number" class="form-control" name="adultos_hotel" min="1" value="2">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Niños</label>
                            <input type="number" class="form-control" name="ninos_hotel" min="0" value="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Habitación *</label>
                            <select class="form-select" name="tipo_habitacion">
                                <option value="standard">Standard</option>
                                <option value="deluxe">Deluxe</option>
                                <option value="suite">Suite</option>
                                <option value="familiar">Familiar</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Regimen Alimenticio *</label>
                            <select class="form-select" name="regimen">
                                <option value="solo_alojamiento">Solo Alojamiento</option>
                                <option value="desayuno">Desayuno</option>
                                <option value="media_pension">Media Pensión</option>
                                <option value="pension_completa">Pensión Completa</option>
                                <option value="todo_incluido">Todo Incluido</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Precio por noche (USD) *</label>
                            <input type="number" class="form-control" name="precio_noche" min="0" step="0.01">
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-civit-primary" id="btn-agregar-hotel">
                        <i class="fas fa-plus me-1"></i> Agregar Hotel
                    </button>
                </div>
            </div>
            
            <!-- Formulario de Paquetes (oculto por defecto) -->
            <div id="formulario-paquetes" class="service-form d-none">
                <h5 class="mb-4">Información del Paquete</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Paquete *</label>
                            <input type="text" class="form-control" name="nombre_paquete">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Destino *</label>
                            <input type="text" class="form-control" name="destino_paquete">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Fecha de Inicio *</label>
                            <input type="date" class="form-control" name="inicio_paquete">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Fecha de Fin *</label>
                            <input type="date" class="form-control" name="fin_paquete">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Duración (noches) *</label>
                            <input type="number" class="form-control" name="duracion" min="1" value="7">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Adultos *</label>
                            <input type="number" class="form-control" name="adultos_paquete" min="1" value="2">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Niños</label>
                            <input type="number" class="form-control" name="ninos_paquete" min="0" value="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Habitaciones *</label>
                            <input type="number" class="form-control" name="habitaciones_paquete" min="1" value="1">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Servicios Incluidos *</label>
                            <textarea class="form-control" name="servicios_incluidos" rows="3" placeholder="Vuelo, hotel, traslados, tours..."></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Precio por adulto (USD) *</label>
                            <input type="number" class="form-control" name="precio_adulto" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Precio por niño (USD)</label>
                            <input type="number" class="form-control" name="precio_nino" min="0" step="0.01">
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-civit-primary" id="btn-agregar-paquete">
                        <i class="fas fa-plus me-1"></i> Agregar Paquete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Servicios Cotizados -->
    <div class="card card-civit mb-4">
        <div class="card-header card-header-civit">
            <h5 class="mb-0">Servicios Cotizados</h5>
        </div>
        <div class="card-body">
            <div id="sin-servicios" class="text-center py-4">
                <i class="fas fa-concierge-bell fa-3x text-muted mb-3"></i>
                <h5>No hay servicios agregados</h5>
                <p class="text-muted">Completa el formulario superior y agrega servicios a tu cotización</p>
            </div>
            
            <div id="lista-servicios-seleccionados" class="d-none">
                <div class="table-responsive">
                    <table class="table table-hover" id="tabla-servicios">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Detalles</th>
                                <th>Precio Unitario</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los servicios seleccionados se agregarán aquí dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Cotización -->
    <div class="card card-civit mb-4">
        <div class="card-header card-header-civit">
            <h5 class="mb-0">Resumen de Cotización</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Moneda *</label>
                        <select class="form-select" name="moneda" required>
                            <option value="USD">USD - Dólar Americano</option>
                            <option value="EUR">EUR - Euro</option>
                            <option value="CRC">CRC - Colón Costarricense</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Fecha de validez *</label>
                        <input type="date" class="form-control" name="fecha_validez" required>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td width="70%"><strong>Subtotal:</strong></td>
                        <td id="subtotal-cotizacion">$0.00</td>
                    </tr>
                    <tr>
                        <td><strong>Impuestos (13%):</strong></td>
                        <td id="impuestos-cotizacion">$0.00</td>
                    </tr>
                    <tr class="table-active">
                        <td><strong>Total:</strong></td>
                        <td id="total-cotizacion"><strong>$0.00</strong></td>
                    </tr>
                </table>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Notas adicionales</label>
                <textarea class="form-control" name="notas" rows="3"></textarea>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="row mb-5">
        <div class="col-12 text-end">
            <button type="reset" class="btn btn-outline-secondary">Cancelar</button>
            <button type="submit" class="btn btn-civit-primary">Guardar Cotización</button>
        </div>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>

<!-- JavaScript para gestionar la cotización -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const sinServicios = document.getElementById('sin-servicios');
    const listaServicios = document.getElementById('lista-servicios-seleccionados');
    const tablaServicios = document.getElementById('tabla-servicios').getElementsByTagName('tbody')[0];
    const serviceTypeButtons = document.querySelectorAll('[data-service-type]');
    const serviceForms = document.querySelectorAll('.service-form');
    
    // Cambiar entre tipos de servicio
    serviceTypeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const serviceType = this.getAttribute('data-service-type');
            
            // Actualizar botones activos
            serviceTypeButtons.forEach(btn => {
                if (btn === this) {
                    btn.classList.remove('btn-civit-outline');
                    btn.classList.add('btn-civit-primary');
                } else {
                    btn.classList.remove('btn-civit-primary');
                    btn.classList.add('btn-civit-outline');
                }
            });
            
            // Mostrar formulario correspondiente
            serviceForms.forEach(form => {
                if (form.id === 'formulario-' + serviceType) {
                    form.classList.remove('d-none');
                } else {
                    form.classList.add('d-none');
                }
            });
        });
    });
    
    // Agregar vuelo a la cotización
    document.getElementById('btn-agregar-vuelo').addEventListener('click', function() {
        const aerolinea = document.querySelector('select[name="aerolinea"]');
        const origen = document.querySelector('select[name="origen"]');
        const destino = document.querySelector('select[name="destino"]');
        const fechaSalida = document.querySelector('input[name="fecha_salida"]');
        const fechaRegreso = document.querySelector('input[name="fecha_regreso"]');
        const clase = document.querySelector('select[name="clase"]');
        const adultos = document.querySelector('input[name="adultos"]');
        const precio = document.querySelector('input[name="precio_vuelo"]');
        
        // Validación básica
        if (!aerolinea.value || !origen.value || !destino.value || !fechaSalida.value || !clase.value || !adultos.value || !precio.value) {
            alert('Por favor, complete todos los campos obligatorios del formulario de vuelo.');
            return;
        }
        
        const tipoVuelo = document.querySelector('select[name="tipo_vuelo"]').value;
        const descripcion = `${origen.options[origen.selectedIndex].text} - ${destino.options[destino.selectedIndex].text}`;
        const detalles = `Aerolínea: ${aerolinea.options[aerolinea.selectedIndex].text}, Clase: ${clase.options[clase.selectedIndex].text}, ${tipoVuelo === 'ida_vuelta' ? 'Ida y Vuelta' : 'Solo Ida'}`;
        const precioUnitario = parseFloat(precio.value);
        const cantidad = parseInt(adultos.value);
        const subtotal = precioUnitario * cantidad;
        
        // En el evento del botón agregar vuelo, después de calcular las variables:
agregarServicioATabla(
    'Vuelo',
    descripcion,
    detalles,
    precioUnitario,
    cantidad,
    subtotal
);

// Función para agregar servicio a la tabla
function agregarServicioATabla(tipo, descripcion, detalles, precioUnitario, cantidad, subtotal) {
    // Ocultar mensaje de "no hay servicios"
    sinServicios.classList.add('d-none');
    listaServicios.classList.remove('d-none');
    
    // Crear nueva fila en la tabla
    const nuevaFila = tablaServicios.insertRow();
    nuevaFila.setAttribute('data-servicio', JSON.stringify({
        tipo: tipo,
        descripcion: descripcion,
        detalles: detalles,
        precioUnitario: precioUnitario,
        cantidad: cantidad,
        subtotal: subtotal
    }));
    
    nuevaFila.innerHTML = `
        <td>${tipo}</td>
        <td>${descripcion}</td>
        <td>${detalles}</td>
        <td>$${precioUnitario.toFixed(2)}</td>
        <td>${cantidad}</td>
        <td class="subtotal-servicio">$${subtotal.toFixed(2)}</td>
        <td>
            <button class="btn btn-sm btn-outline-danger btn-eliminar-servicio">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    // Agregar evento para eliminar servicio
    const btnEliminar = nuevaFila.querySelector('.btn-eliminar-servicio');
    btnEliminar.addEventListener('click', function() {
        nuevaFila.remove();
        actualizarResumen();
        
        // Si no quedan servicios, mostrar el mensaje
        if (tablaServicios.rows.length === 0) {
            sinServicios.classList.remove('d-none');
            listaServicios.classList.add('d-none');
        }
    });
    
    // Actualizar resumen
    actualizarResumen();
}
        
        // Limpiar formulario o mantenerlo según preferencia
        // document.querySelector('select[name="tipo_vuelo"]').value = '';
        // document.querySelector('input[name="fecha_regreso"]').value = '';
    });
    
    // Agregar hotel a la cotización
    document.getElementById('btn-agregar-hotel').addEventListener('click', function() {
        const nombreHotel = document.querySelector('input[name="nombre_hotel"]');
        const ubicacion = document.querySelector('input[name="ubicacion_hotel"]');
        const checkin = document.querySelector('input[name="checkin"]');
        const checkout = document.querySelector('input[name="checkout"]');
        const noches = document.querySelector('input[name="noches"]');
        const habitaciones = document.querySelector('input[name="habitaciones"]');
        const adultos = document.querySelector('input[name="adultos_hotel"]');
        const tipoHabitacion = document.querySelector('select[name="tipo_habitacion"]');
        const regimen = document.querySelector('select[name="regimen"]');
        const precioNoche = document.querySelector('input[name="precio_noche"]');
        
        // Validación básica
        if (!nombreHotel.value || !ubicacion.value || !checkin.value || !checkout.value || !noches.value || !habitaciones.value || !adultos.value || !precioNoche.value) {
            alert('Por favor, complete todos los campos obligatorios del formulario de hotel.');
            return;
        }
        
        const descripcion = nombreHotel.value;
        const detalles = `Ubicación: ${ubicacion.value}, ${noches.value} noches, ${habitaciones.value} habitación(es), ${tipoHabitacion.options[tipoHabitacion.selectedIndex].text}, ${regimen.options[regimen.selectedIndex].text}`;
        const precioUnitario = parseFloat(precioNoche.value) * parseInt(noches.value) * parseInt(habitaciones.value);
        const cantidad = 1; // Se considera como un paquete de hotel
        const subtotal = precioUnitario;
        
        agregarServicioATabla(
            'Hotel',
            descripcion,
            detalles,
            precioUnitario,
            cantidad,
            subtotal
        );
    });
    
    // Agregar paquete a la cotización
    document.getElementById('btn-agregar-paquete').addEventListener('click', function() {
        const nombrePaquete = document.querySelector('input[name="nombre_paquete"]');
        const destino = document.querySelector('input[name="destino_paquete"]');
        const inicio = document.querySelector('input[name="inicio_paquete"]');
        const fin = document.querySelector('input[name="fin_paquete"]');
        const duracion = document.querySelector('input[name="duracion"]');
        const adultos = document.querySelector('input[name="adultos_paquete"]');
        const ninos = document.querySelector('input[name="ninos_paquete"]');
        const habitaciones = document.querySelector('input[name="habitaciones_paquete"]');
        const servicios = document.querySelector('textarea[name="servicios_incluidos"]');
        const precioAdulto = document.querySelector('input[name="precio_adulto"]');
        
        // Validación básica
        if (!nombrePaquete.value || !destino.value || !inicio.value || !fin.value || !duracion.value || !adultos.value || !habitaciones.value || !precioAdulto.value) {
            alert('Por favor, complete todos los campos obligatorios del formulario de paquete.');
            return;
        }
        
        const descripcion = nombrePaquete.value;
        const detalles = `Destino: ${destino.value}, ${duracion.value} noches, ${adultos.value} adulto(s), ${ninos.value || 0} niño(s), ${habitaciones.value} habitación(es)`;
        const precioUnitario = parseFloat(precioAdulto.value);
        const cantidad = parseInt(adultos.value) + parseInt(ninos.value || 0);
        const subtotal = precioUnitario * cantidad;
        
        agregarServicioATabla(
            'Paquete',
            descripcion,
            detalles,
            precioUnitario,
            cantidad,
            subtotal
        );
    });

 // Función para actualizar el resumen de la cotización
    function actualizarResumen() {
        let subtotal = 0;
        const filas = tablaServicios.querySelectorAll('tr');
        
        filas.forEach(fila => {
            const subtotalTexto = fila.querySelector('.subtotal-servicio').textContent;
            const valorSubtotal = parseFloat(subtotalTexto.replace('$', '')) || 0;
            subtotal += valorSubtotal;
        });
        
        const impuestos = subtotal * 0.13;
        const total = subtotal + impuestos;
        
        document.getElementById('subtotal-cotizacion').textContent = `$${subtotal.toFixed(2)}`;
        document.getElementById('impuestos-cotizacion').textContent = `$${impuestos.toFixed(2)}`;
        document.getElementById('total-cotizacion').innerHTML = `<strong>$${total.toFixed(2)}</strong>`;
        
        // Actualizar campos hidden para el formulario
        document.querySelector('input[name="subtotal"]')?.remove();
        document.querySelector('input[name="impuestos"]')?.remove();
        document.querySelector('input[name="total"]')?.remove();
        
        const form = document.getElementById('form-cotizacion');
        const subtotalInput = document.createElement('input');
        subtotalInput.type = 'hidden';
        subtotalInput.name = 'subtotal';
        subtotalInput.value = subtotal;
        form.appendChild(subtotalInput);
        
        const impuestosInput = document.createElement('input');
        impuestosInput.type = 'hidden';
        impuestosInput.name = 'impuestos';
        impuestosInput.value = impuestos;
        form.appendChild(impuestosInput);
        
        const totalInput = document.createElement('input');
        totalInput.type = 'hidden';
        totalInput.name = 'total';
        totalInput.value = total;
        form.appendChild(totalInput);
    }
    
// Manejar el envío del formulario
document.getElementById('form-cotizacion').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Verificar que hay al menos un servicio
    if (tablaServicios.rows.length === 0) {
        alert('Debe agregar al menos un servicio a la cotización.');
        return;
    }
    
    // Recolectar servicios de la tabla
    const servicios = [];
    const filas = tablaServicios.querySelectorAll('tr');
    
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        const servicio = {
            tipo: celdas[0].textContent,
            descripcion: celdas[1].textContent,
            detalles: celdas[2].textContent,
            precioUnitario: parseFloat(celdas[3].textContent.replace('$', '')),
            cantidad: parseInt(celdas[4].textContent),
            subtotal: parseFloat(celdas[5].textContent.replace('$', ''))
        };
        servicios.push(servicio);
    });
    
    // Agregar servicios como campo hidden
    document.querySelector('input[name="servicios_json"]')?.remove();
    const serviciosInput = document.createElement('input');
    serviciosInput.type = 'hidden';
    serviciosInput.name = 'servicios_json';
    serviciosInput.value = JSON.stringify(servicios);
    this.appendChild(serviciosInput);
    
    // Mostrar loading
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';
    submitBtn.disabled = true;
    
    // Enviar formulario via AJAX
    const formData = new FormData(this);
    
    fetch('php/guardar-cotizacion.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = 'ver-cotizacion.php?id=' + data.id;
        } else {
            alert('Error: ' + data.message);
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        alert('Error al guardar la cotización: ' + error);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
    // Establecer fecha de validez por defecto (7 días a partir de hoy)
    const fechaValidez = new Date();
    fechaValidez.setDate(fechaValidez.getDate() + 7);
    document.querySelector('input[name="fecha_validez"]').valueAsDate = fechaValidez;
    
    // Establecer fecha de salida por defecto (mañana)
    const fechaSalida = new Date();
    fechaSalida.setDate(fechaSalida.getDate() + 1);
    document.querySelector('input[name="fecha_salida"]').valueAsDate = fechaSalida;
    
    // Establecer fecha de regreso por defecto (8 días a partir de hoy)
    const fechaRegreso = new Date();
    fechaRegreso.setDate(fechaRegreso.getDate() + 8);
    document.querySelector('input[name="fecha_regreso"]').valueAsDate = fechaRegreso;
    
    // Establecer fechas de check-in/check-out por defecto
    document.querySelector('input[name="checkin"]').valueAsDate = fechaSalida;
    document.querySelector('input[name="checkout"]').valueAsDate = fechaRegreso;
    
    // Establecer fechas de paquete por defecto
    document.querySelector('input[name="inicio_paquete"]').valueAsDate = fechaSalida;
    document.querySelector('input[name="fin_paquete"]').valueAsDate = fechaRegreso;
});

// Manejar cambio entre cliente nuevo y existente
document.getElementById('tipo-cliente').addEventListener('change', function() {
    const tipoCliente = this.value;
    const camposNuevo = document.getElementById('campos-cliente-nuevo');
    const campoExistente = document.getElementById('campo-cliente-existente');
    
    if (tipoCliente === 'existente') {
        camposNuevo.style.display = 'none';
        campoExistente.style.display = 'block';
        // Hacer opcionales los campos de cliente nuevo
        document.getElementById('cliente_nombre').required = false;
    } else {
        camposNuevo.style.display = 'block';
        campoExistente.style.display = 'none';
        // Hacer obligatorio el nombre para cliente nuevo
        document.getElementById('cliente_nombre').required = true;
    }
});

// Cuando se selecciona un cliente existente, cargar sus datos
document.getElementById('cliente-existente').addEventListener('change', function() {
    const clienteId = this.value;
    if (clienteId) {
        // Opcional: Cargar datos del cliente para mostrar en pantalla
        fetch(`php/obtener-cliente.php?id=${clienteId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Cliente cargado:', data.cliente);
                    // Puedes mostrar la info del cliente en algún lugar si quieres
                }
            });
    }
});
</script>