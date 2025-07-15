<?php
include 'conexion.php';
if (!isset($_SESSION['organizacion_id'])) {

  $_SESSION['organizacion_id'] = 1; // Set a default value

}
$organizacion_id = $_SESSION['organizacion_id'];

if (isset($_POST['factura_id']) && isset($_POST['items']) && isset($_GET['crear_abono'])) {
  $factura_id = intval($_POST['factura_id']);
  $items = json_decode($_POST['items'], true);

  if ($factura_id <= 0 || empty($items)) {
    $mensaje_error = "Datos incompletos para crear el abono";
  } else {
    // Obtener datos de la factura original
    $stmt = $conn->prepare("SELECT * FROM facturas WHERE id = ?");
    $stmt->bind_param("i", $factura_id);
    $stmt->execute();
    $factura = $stmt->get_result()->fetch_assoc();

    if (!$factura) {
      $mensaje_error = "Factura original no encontrada";
    } else {
      // Calcular total del abono (negativo)
      $total_abono = 0;
      foreach ($items as $item) {
        $cantidad = floatval($item['cantidad']);
        $precio = floatval($item['precio_unitario']);
        $total_abono += $cantidad * $precio;
      }
      $total_abono = -abs($total_abono); // Siempre negativo

      // Crear la nueva factura tipo abono
      $stmt = $conn->prepare("INSERT INTO facturas 
    (cliente_id, organizacion_id, tipo, estado, fecha_emision, fecha_vencimiento, total, iva, irpf, impuestos_extra, notas, serie_id, numero_serie, numero_factura) 
    VALUES (?, ?, 'abono', 'emitida', CURDATE(), CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?)");

      $serie_id = isset($factura['serie_id']) ? intval($factura['serie_id']) : 0;
      $numero_serie = isset($factura['numero_serie']) ? intval($factura['numero_serie']) : 0;
      $numero_factura = isset($factura['numero_factura']) ? $factura['numero_factura'] : '';
      $impuestos_extra = isset($factura['impuestos_extra']) ? $factura['impuestos_extra'] : '';
      $notas = isset($factura['notas']) ? $factura['notas'] : '';

      $stmt = $conn->prepare("INSERT INTO facturas 
    (cliente_id, organizacion_id, tipo, estado, fecha_emision, fecha_vencimiento, total, iva, irpf, impuestos_extra, notas, serie_id, numero_serie, numero_factura) 
    VALUES (?, ?, 'abono', 'emitida', CURDATE(), CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?)");

      $stmt->bind_param(
        "iidddssiis", // total: 10 tipos
        $factura['cliente_id'],
        $factura['organizacion_id'],
        $total_abono,
        $factura['iva'],
        $factura['irpf'],
        $impuestos_extra,
        $notas,
        $serie_id,
        $numero_serie,
        $numero_factura
      );


      $stmt->execute();
      $nueva_factura_id = $stmt->insert_id;

      // Insertar los items seleccionados en la nueva factura de abono
      foreach ($items as $item) {
        if (!isset($item['precio'], $item['cantidad'])) {
          echo json_encode(['error' => 'Faltan datos en los ítems (precio o cantidad)']);
          exit;
        }

        $descripcion = $conn->real_escape_string($item['nombre']);
        $cantidad = floatval($item['cantidad']);
        $precio_unitario = floatval($item['precio']);
        $producto_id = intval($item['producto_id']);

        $stmt_item = $conn->prepare("INSERT INTO factura_items (factura_id, producto_id, descripcion, cantidad, precio_unitario) VALUES (?, ?, ?, ?, ?)");
        $stmt_item->bind_param("iisdd", $nueva_factura_id, $producto_id, $descripcion, $cantidad, $precio_unitario);
        $stmt_item->execute();
      }

      $mensaje_exito = "Abono creado correctamente con ID: " . $nueva_factura_id;
    }
  }
}

function generarFacturasRecurrentes($conn, $hoy, $organizacion_id)
{
  // Add a check to prevent null organizacion_id
  if ($organizacion_id === null) {
    return 0; // Return early if organizacion_id is null
  }

  $generadas = 0;

  $sql = "SELECT * FROM facturas 
          WHERE tipo = 'recurrente' 
          AND recurrente_hasta >= '$hoy' 
          AND recurrente_id IS NOT NULL";
  $result = $conn->query($sql);
  if (!$result) return 0;

  while ($factura = $result->fetch_assoc()) {
    $inicio = new DateTime($factura['fecha_emision']);
    $hasta = new DateTime($factura['recurrente_hasta']);
    $periodo = $factura['periodo_recurrente'];
    $cliente_id = $factura['cliente_id'];
    $total = $factura['total'];
    $recurrente_id = $factura['recurrente_id'];

    $interval = match ($periodo) {
      'mensual' => new DateInterval('P1M'),
      'trimestral' => new DateInterval('P3M'),
      'anual' => new DateInterval('P1Y'),
      default => null
    };

    if (!$interval) continue;

    $fechas_existentes = [];
    $check = $conn->prepare("SELECT fecha_emision FROM facturas WHERE recurrente_id = ?");
    $check->bind_param("i", $recurrente_id);
    $check->execute();
    $check_result = $check->get_result();
    while ($row = $check_result->fetch_assoc()) {
      $fechas_existentes[] = $row['fecha_emision'];
    }

    $fecha = clone $inicio;
    while ($fecha <= $hasta && $fecha <= new DateTime($hoy)) {
      $f_emision = $fecha->format('Y-m-d');

      if (!in_array($f_emision, $fechas_existentes)) {
        $f_vencimiento = (clone $fecha)->modify('+15 days')->format('Y-m-d');

        $stmt = $conn->prepare("INSERT INTO facturas 
                  (cliente_id, organizacion_id, tipo, estado, fecha_emision, fecha_vencimiento, total, periodo_recurrente, recurrente_hasta, recurrente_id) 
                  VALUES (?, ?, 'recurrente', 'emitida', ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
          "iissdssi",
          $cliente_id,
          $organizacion_id,
          $f_emision,
          $f_vencimiento,
          $total,
          $periodo,
          $factura['recurrente_hasta'],
          $recurrente_id
        );

        $stmt->execute();
        $generadas++;
      }

      $fecha->add($interval);
    }
  }

  return $generadas;
}

$hoy = date('Y-m-d');
$facturas_generadas = 0; // Initialize with default value

// Only call the function if organizacion_id is set
if (isset($_SESSION['organizacion_id']) && $_SESSION['organizacion_id'] !== null) {
  $facturas_generadas = generarFacturasRecurrentes($conn, $hoy, $organizacion_id);
}

$conn->query("UPDATE facturas SET estado = 'emitida' WHERE estado = 'borrador' AND fecha_emision <= '$hoy'");
$facturas_emitidas = $conn->affected_rows;

$conn->query("UPDATE facturas SET estado = 'vencida' WHERE estado = 'emitida' AND fecha_vencimiento < '$hoy'");
$facturas_vencidas = $conn->affected_rows;

$serie_id_filtrada = $_GET['serie_id'] ?? '';
$buscar_cliente = $_GET['buscar_cliente'] ?? '';
$tipo_filtrado = $_GET['tipo'] ?? '';
$estado_filtrado = $_GET['estado'] ?? '';

$facturas = [];
$sql = "SELECT f.*, c.nombre AS cliente, s.prefijo FROM facturas f
        JOIN clientes c ON f.cliente_id = c.id
        LEFT JOIN series s ON f.serie_id = s.id
        WHERE f.organizacion_id = ?";
$params = [$organizacion_id];
$types = "i";

if (!empty($serie_id_filtrada)) {
  $sql .= " AND f.serie_id = ?";
  $params[] = $serie_id_filtrada;
  $types .= "i";
}

if (!empty($buscar_cliente)) {
  $like = "%" . $conn->real_escape_string($buscar_cliente) . "%";
  $sql .= " AND c.nombre LIKE ?";
  $params[] = $like;
  $types .= "s";
}

if (!empty($tipo_filtrado)) {
  $sql .= " AND f.tipo = ?";
  $params[] = $tipo_filtrado;
  $types .= "s";
}

if (!empty($estado_filtrado)) {
  $sql .= " AND f.estado = ?";
  $params[] = $estado_filtrado;
  $types .= "s";
}

$sql .= " ORDER BY f.id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$facturas = $result->fetch_all(MYSQLI_ASSOC);

$series_disponibles = [];
$stmt = $conn->prepare("SELECT id, nombre, prefijo FROM series WHERE organizacion_id = ? ORDER BY nombre ASC");
$stmt->bind_param("i", $organizacion_id);
$stmt->execute();
$res = $stmt->get_result();
$series_disponibles = $res->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth group" data-sidebar="brand" dir="ltr">

<head>
  <style>
    .py-4 {
      padding-top: 2rem !important;
      padding-bottom: 3rem !important;
    }
  </style>
  <meta charset="utf-8" />
  <title>Robotech - Admin & Dashboard Template</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta content="Tailwind Multipurpose Admin & Dashboard Template" name="description" />
  <meta content="" name="Mannatthemes" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- App favicon -->
  <link rel="shortcut icon" href="assets/images/favicon.ico" />

  <!-- Css -->
  <!-- Main Css -->
  <link rel="stylesheet" href="assets/libs/icofont/icofont.min.css">
  <link href="assets/libs/flatpickr/flatpickr.min.css" type="text/css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/tailwind.min.css">


</head>

<body data-layout-mode="light" data-sidebar-size="default" data-theme-layout="vertical" class="bg-[#EEF0FC] dark:bg-gray-900">

  <!-- leftbar-tab-menu -->


  <?php
  include 'menu.php';
  ?>

  <?php
  if (isset($_SESSION['estado_envio'])):
    $estado = $_SESSION['estado_envio'];
    unset($_SESSION['estado_envio']); // Solo mostrar una vez
  ?>
    <div id="alerta-envio" class="p-4 mb-4 text-sm rounded-md transition-opacity duration-500
        <?= $estado === 'ok' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
      <?= $estado === 'ok'
        ? '✅ El correo se envió correctamente.'
        : '❌ No se pudo enviar el correo. Inténtalo nuevamente.' ?>
    </div>

    <script>
      setTimeout(function() {
        const alerta = document.getElementById('alerta-envio');
        if (alerta) {
          alerta.style.opacity = '0';
          setTimeout(() => alerta.remove(), 500); // Opcional: eliminar del DOM después
        }
      }, 5000); // 5000 ms = 5 segundos
    </script>
  <?php endif; ?>




  <div class="xl:w-full">
    <div class="flex flex-wrap">
      <div class="flex items-center py-4 w-full">
        <div class="w-full">
          <main class="lg:ms-[260px] pt-[90px] px-4 pb-16">



            <div class="max-w-full lg:max-w-6xl mx-auto bg-white p-4 sm:p-6 rounded shadow">

              <!-- TÍTULO Y BOTONES -->
              <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
                <h1 class="text-2xl font-bold">📄 Gestión de Facturas</h1>
                <div class="flex flex-wrap gap-2">
                  <a href="crear-factura-manual.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Crear factura manual
                    <span class="shortcut-badge">N</span>
                  </a>
                  <a href="crear-factura-desde-presupuesto.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Crear desde presupuesto
                  </a>
                </div>
              </div>


              <!-- FILTROS -->
              <form action="" method="GET" class="mb-4 flex flex-wrap gap-4 items-end">
                <div>
                  <label for="serie_id" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por serie:</label>
                  <select name="serie_id" id="serie_id" class="border rounded p-2" onchange="this.form.submit()">
                    <option value="">Todas las series</option>
                    <?php foreach ($series_disponibles as $serie): ?>
                      <option value="<?= $serie['id'] ?>" <?= $serie['id'] == ($serie_id_filtrada ?? '') ? 'selected' : '' ?>>
                        <?= htmlspecialchars($serie['nombre']) ?> (<?= $serie['prefijo'] ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div>
                  <label for="buscar_cliente" class="block text-sm font-medium text-gray-700 mb-1">Buscar cliente:<span class="shortcut-badge">F</span>
                  </label>
                  <input type="text" name="buscar_cliente" id="buscar_cliente"
                    value="<?= htmlspecialchars($buscar_cliente) ?>"
                    placeholder="Nombre del cliente"
                    class="border rounded p-2">
                </div>
                <div>
                  <button type="submit" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Buscar</button>
                </div>
                <div>
                  <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por tipo:</label>
                  <select name="tipo" id="tipo" class="border rounded p-2" onchange="this.form.submit()">
                    <option value="">Todos los tipos</option>
                    <option value="abono" <?= $tipo_filtrado === 'abono' ? 'selected' : '' ?>>Abono</option>
                    <option value="unica" <?= $tipo_filtrado === 'unica' ? 'selected' : '' ?>>Única</option>
                    <option value="recurrente" <?= $tipo_filtrado === 'recurrente' ? 'selected' : '' ?>>Recurrente</option>
                  </select>
                </div>
                <div>
                  <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por estado:</label>
                  <select name="estado" id="estado" class="border rounded p-2" onchange="this.form.submit()">
                    <option value="">Todos los estados</option>
                    <option value="borrador" <?= $estado_filtrado === 'borrador' ? 'selected' : '' ?>>Borrador</option>
                    <option value="emitida" <?= $estado_filtrado === 'emitida' ? 'selected' : '' ?>>Emitida</option>
                    <option value="pagada" <?= $estado_filtrado === 'pagada' ? 'selected' : '' ?>>Pagada</option>
                    <option value="vencida" <?= $estado_filtrado === 'vencida' ? 'selected' : '' ?>>Vencida</option>
                  </select>
                  <?php if (!empty($serie_id_filtrada) || !empty($buscar_cliente) || !empty($tipo_filtrado) || !empty($estado_filtrado)): ?>
                    <a href="facturas.php" class="ml-2 text-sm text-blue-600 hover:underline">Limpiar filtros</a>
                  <?php endif; ?>
                </div>
                <div>
                  <button type="button" onclick="enviarCorreo()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Enviar Correo
                  </button>
                </div>
              </form>

              <!-- ALERTAS -->
              <?php if ($facturas_generadas > 0): ?>
                <div class="bg-green-100 text-green-800 border border-green-300 p-4 rounded mb-4">
                  ✅ Se generaron <?= $facturas_generadas ?> factura(s) recurrente(s) automáticamente.
                </div>
              <?php endif; ?>

              <?php if ($facturas_emitidas > 0): ?>
                <div class="bg-blue-100 text-blue-800 border border-blue-300 p-4 rounded mb-4">
                  📤 <?= $facturas_emitidas ?> factura(s) pasaron de borrador a emitida automáticamente.
                </div>
              <?php endif; ?>

              <?php if ($facturas_vencidas > 0): ?>
                <div class="bg-red-100 text-red-800 border border-red-300 p-4 rounded mb-4">
                  ⚠️ <?= $facturas_vencidas ?> factura(s) vencida(s) actualizada(s) automáticamente.
                </div>
              <?php endif; ?>

              <?php if (isset($mensaje_exito)): ?>
                <div class="bg-green-100 text-green-800 border border-green-300 p-4 rounded mb-4">
                  ✅ <?= $mensaje_exito ?>
                </div>
              <?php endif; ?>

              <?php if (isset($mensaje_error)): ?>
                <div class="bg-red-100 text-red-800 border border-red-300 p-4 rounded mb-4">
                  ❌ Error: <?= $mensaje_error ?>
                </div>
              <?php endif; ?>
              <style>
                .modal-center {
                  position: fixed;
                  inset: 0;
                  display: flex;
                  justify-content: center;
                  align-items: center;
                  z-index: 50;
                  backdrop-filter: blur(1px);
                  background-color: rgba(0, 0, 0, 0.4);
                }
              </style>
              <!-- Modal con fondo difuminado y centrado con clase modal-center -->
              <div id="modal-abono" class="modal-center hidden">
                <div class="bg-white rounded-lg max-w-xl shadow-lg overflow-hidden">
                  <!-- Cabecera del modal -->
                  <div class="px-4 py-2 border-b flex justify-between items-center bg-gray-100">
                    <h2 class="text-lg font-semibold text-gray-800">Productos de la factura</h2>
                    <button onclick="cerrarModalAbono()" class="text-2xl font-bold text-gray-500 hover:text-gray-800">&times;</button>
                  </div>

                  <!-- Contenido del modal -->
                  <div id="contenedor-modal-productos-abono" class="p-4 max-h-[70vh] overflow-y-auto">
                    <div class="text-center text-gray-500 py-4">Cargando productos...</div>
                  </div>
                </div>
              </div>




              <!-- TABLA -->
              <form id="form-facturas" method="POST" action="vista_correo.php" target="_blank" onsubmit="return !isAbonoAction">
                <div class="overflow-x-auto">
                  <table class="w-full table-auto border-collapse border border-gray-300 text-sm">
                    <thead>
                      <tr class="bg-gray-100 text-left">
                        <th class="p-2 border">Seleccionar <div class="text-xs text-gray-500 mt-1">
                            <span class="shortcut-badge">Espacio</span>
                          </div>
                        </th> <!-- NUEVA COLUMNA -->
                        <th class="p-2 border">ID</th>
                        <th class="p-2 border">Serie</th>
                        <th class="p-2 border">Cliente</th>
                        <th class="p-2 border">Tipo</th>
                        <th class="p-2 border">Estado <div class="estado-shortcuts">
                            <span class="estado-shortcut borrador" title="Borrador">1</span>
                            <span class="estado-shortcut emitida" title="Emitida">2</span>
                            <span class="estado-shortcut pagada" title="Pagada">3</span>
                            <span class="estado-shortcut vencida" title="Vencida">4</span>
                          </div></th>
                        <th class="p-2 border">Emisión</th>
                        <th class="p-2 border">Vencimiento</th>
                        <th class="p-2 border">Total</th>
                        <th class="p-2 border">Recurrente</th>
                        <th class="p-2 border">Acciones<div class="text-xs text-gray-500 mt-1">
                            <span class="shortcut-badge">?</span> para ayuda
                          </div>
                        </th>
                      </tr>
                    </thead>
                    <tbody>

                      <?php foreach ($facturas as $row): ?>
                        <tr class="<?= $row['tipo'] === 'abono' ? 'bg-red-50' : '' ?>">

                          <td class="p-2 border text-center">
                            <input type="checkbox" name="facturas[]" value="<?= $row['id'] ?>">
                          </td>

                          <td class='p-2 border'><?= $row['id'] ?></td>
                          <td class='p-2 border'>
                            <?= htmlspecialchars($row['prefijo']) . '-' . $row['numero_factura'] ?>
                          </td>
                          <td class='p-2 border'><?= htmlspecialchars($row['cliente']) ?></td>
                          <td class='p-2 border'><?= $row['tipo'] ?></td>
                          <td class='p-2 border'>
                            <?php
                            $estado = $row['estado'];
                            $clase_estado = match ($estado) {
                              'pagada' => 'bg-green-100 text-green-800 border-green-300',
                              'emitida' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                              'vencida' => 'bg-red-100 text-red-800 border-red-300',
                              default => 'bg-gray-100 text-gray-800 border-gray-300'
                            };
                            ?>
                            <div>
                              <select
                                data-id="<?= $row['id'] ?>"
                                class="estado-select border p-1 rounded text-sm <?= $clase_estado ?>"
                                <?= $estado === 'pagada' ? 'disabled' : '' ?>>
                                <option value='borrador' <?= $estado === 'borrador' ? 'selected' : '' ?>>Borrador</option>
                                <option value='emitida' <?= $estado === 'emitida' ? 'selected' : '' ?>>Emitida</option>
                                <option value='pagada' <?= $estado === 'pagada' ? 'selected' : '' ?>>Pagada</option>
                                <option value='vencida' <?= $estado === 'vencida' ? 'selected' : '' ?>>Vencida</option>
                              </select>
                              <p class="text-xs text-red-600 mt-1 hidden" id="mensaje-error-<?= $row['id'] ?>"></p>

                            </div>
                          </td>
                          <td class='p-2 border'><?= $row['fecha_emision'] ?></td>
                          <td class='p-2 border'><?= $row['fecha_vencimiento'] ?></td>
                          <td class='p-2 border'>€<?= number_format($row['tipo'] === 'abono' ? $row['total'] * -1 : $row['total'], 2) ?></td>
                          <td class='p-2 border'><?= $row['tipo'] === 'recurrente' ? ucfirst($row['periodo_recurrente']) : '—' ?></td>
                          <td class='p-2 border'>
                            <a href='ver-factura.php?id=<?= $row['id'] ?>' class='text-blue-600 hover:underline'>Ver <span class="shortcut-badge shortcut-enter">↵</span>
                            </a>
                            <?php if ($row['estado'] === 'borrador'): ?>
                              | <a href='editar-factura.php?id=<?= $row['id'] ?>' class='text-orange-600 hover:underline'>Editar
                              </a>
                            <?php endif; ?>
                            | <a href='descargar-factura.php?id=<?= $row['id'] ?>' class='text-green-600 hover:underline'>PDF <span class="shortcut-badge shortcut-pdf">P</span>
                            </a>
                            | <button type="button" class="btn btn-danger btn-sm hover:underline" onclick="isAbonoAction=true; mostrarProductosAbono(<?= $row['id'] ?>, this)">Abono <span class="shortcut-badge shortcut-abono">A</span>
                            </button>
                            | <a href='editar-factura.php?id=<?= $row['id'] ?>' class='text-yellow-600 hover:underline'>Editar <span class="shortcut-badge shortcut-edit">E</span></a>
                            | <button type="button" onclick="eliminarFactura(<?= $row['id'] ?>)" class='text-red-600 hover:underline'>Eliminar <span class="shortcut-badge shortcut-delete">Supr</span>
                            </button>
                            <script>
                              let isAbonoAction = false;

                              function eliminarFactura(id) {
                                if (confirm('¿Estás seguro de que deseas eliminar esta factura?')) {
                                  fetch('eliminar-factura.php?id=' + id, {
                                      method: 'DELETE'
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                      if (data.success) {
                                        location.reload();
                                      } else {
                                        alert('Error al eliminar la factura');
                                      }
                                    })
                                    .catch(error => {
                                      console.error('Error:', error);
                                      alert('Error al eliminar la factura');
                                    });
                                }
                              }
                            </script>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </form>
              <script>
                function enviarCorreo() {
                  const checkboxes = document.querySelectorAll('input[name="facturas[]"]:checked');

                  if (checkboxes.length === 0) {
                    alert('Por favor, selecciona al menos una factura para enviar.');
                    return;
                  }

                  // Opcional: Confirmación
                  if (!confirm(`¿Enviar correo para ${checkboxes.length} factura(s) seleccionada(s)?`)) {
                    return;
                  }

                  // Enviar el formulario
                  document.getElementById('form-facturas').submit();
                }
              </script>



              <!-- VOLVER -->
              <div class="mt-6">
                <div class="text-sm text-gray-500">
                  Presiona <span class="shortcut-badge">R</span> para recargar
                </div>
              </div>


            </div>


          </main>



        </div>




      </div>
      <!-- footer -->
      <div class="block print:hidden border-t pt-8 pb-12 dark:border-slate-700/40">
        <div class="container mx-auto px-4">
          <!-- Footer Start -->
          <footer class="footer bg-transparent text-center font-medium text-slate-600 dark:text-slate-400 md:text-left">
            &copy;
            <script>
              document.write(new Date().getFullYear());
            </script>
            Robotech
            <span class="float-right hidden text-slate-600 dark:text-slate-400 md:inline-block">
              Crafted with <i class="ti ti-heart text-red-500"></i> by Mannatthemes
            </span>
          </footer>
          <!-- end Footer -->
        </div>
      </div>

    </div><!--end container-->
  </div>
  </div>

  <!-- JAVASCRIPTS -->
  <!-- <div class="menu-overlay"></div> -->
  <script src="assets/libs/lucide/umd/lucide.min.js"></script>
  <script src="assets/libs/simplebar/simplebar.min.js"></script>
  <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
  <script src="assets/libs/@frostui/tailwindcss/frostui.js"></script>

  <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
  <script src="assets/js/pages/analytics-index.init.js"></script>
  <script src="assets/js/app.js"></script>
  <!-- JAVASCRIPTS -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      fetch("get-total-clientes.php")
        .then(response => response.json())
        .then(data => {
          if (data.total_clientes !== undefined) {
            document.getElementById("total-clientes").textContent = data.total_clientes;
          } else {
            console.error("Respuesta inesperada:", data);
          }
        })
        .catch(error => {
          console.error("Error al obtener total de clientes:", error);
        });
    });
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Total Clientes
      fetch("get-total-clientes.php")
        .then(res => res.json())
        .then(data => {
          if (data.total_clientes !== undefined) {
            document.getElementById("total-clientes").textContent = data.total_clientes;
          }
        })
        .catch(err => console.error("Error clientes:", err));

      // Total Admins
      fetch("get-total-admins.php")
        .then(res => res.json())
        .then(data => {
          if (data.total_admins !== undefined) {
            document.getElementById("total-admins").textContent = data.total_admins;
          }
        })
        .catch(err => console.error("Error admins:", err));
    });
  </script>






</body>



</html>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Código existente para cambio de estado
    const estadoSelects = document.querySelectorAll('.estado-select');
    estadoSelects.forEach(select => {
      select.addEventListener('change', function() {
        const facturaId = this.getAttribute('data-id');
        const nuevoEstado = this.value;
        const mensajeError = document.getElementById(`mensaje-error-${facturaId}`);

        fetch('cambiar-estado-factura.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${facturaId}&estado=${nuevoEstado}`
          })
          .then(response => response.json())
          .then(data => {
            if (data.error) {
              mensajeError.textContent = data.error;
              mensajeError.classList.remove('hidden');
              // Revertir al estado anterior
              this.value = data.estado_actual;
            } else {
              mensajeError.classList.add('hidden');
              // Actualizar la clase del select según el nuevo estado
              this.className = 'estado-select border p-1 rounded text-sm';
              switch (nuevoEstado) {
                case 'pagada':
                  this.classList.add('bg-green-100', 'text-green-800', 'border-green-300');
                  this.disabled = true;
                  break;
                case 'emitida':
                  this.classList.add('bg-yellow-100', 'text-yellow-800', 'border-yellow-300');
                  break;
                case 'vencida':
                  this.classList.add('bg-red-100', 'text-red-800', 'border-red-300');
                  break;
                default:
                  this.classList.add('bg-gray-100', 'text-gray-800', 'border-gray-300');
              }
            }
          })
          .catch(error => {
            console.error('Error:', error);
            mensajeError.textContent = 'Error al actualizar el estado';
            mensajeError.classList.remove('hidden');
          });
      });
    });

    // Nuevo código para búsqueda de clientes
    const buscarClienteInput = document.getElementById('buscar_cliente');
    if (buscarClienteInput) {
      buscarClienteInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          this.form.submit();
        }
      });
    }
  });
</script>

<script>
  function mostrarProductosAbono(facturaId) {
    const modal = document.getElementById('modal-abono');
    const contenedor = document.getElementById('contenedor-modal-productos-abono');

    // Mostrar el modal
    modal.classList.remove('hidden');
    contenedor.innerHTML = '<div class="text-center text-gray-500 py-4">Cargando productos...</div>';

    // Fetch de productos vía AJAX
    fetch('get-productos-factura.php?id=' + facturaId)
      .then(res => res.json())
      .then(data => {
        if (data.productos && data.productos.length > 0) {
          let html = '<form id="form-abono-' + facturaId + '" class="p-2">';
          html += '<table class="w-full text-sm border-collapse"><thead><tr class="bg-gray-100"><th class="p-2 border"></th><th class="p-2 border">Producto</th><th class="p-2 border">Cantidad</th><th class="p-2 border">Precio</th></tr></thead><tbody>';

          data.productos.forEach(prod => {
            html += '<tr class="border">';
            html += '<td class="p-2 text-center">';
            html += '<input type="checkbox" name="productos[]" value="' + prod.id + '" class="cursor-pointer">';
            html += '<input type="hidden" name="producto_id" value="' + prod.producto_id + '">';
            html += '<input type="hidden" name="nombre" value="' + prod.nombre + '">';
            html += '<input type="hidden" name="cantidad" value="' + prod.cantidad + '">';
            html += '<input type="hidden" name="precio" value="' + prod.precio + '">';
            html += '</td>';
            html += '<td class="p-2 text-center">' + prod.nombre + '</td>';
            html += '<td class="p-2 text-center">' + prod.cantidad + '</td>';
            html += '<td class="p-2 text-center">€' + prod.precio + '</td>';
            html += '</tr>';
          });

          html += '</tbody></table>';
          html += '<div class="text-right mt-2 space-x-2">';
          html += '<button type="button" class="bg-gray-500 text-white px-4 py-1 rounded hover:bg-gray-600" onclick="cerrarModalAbono()">Cancelar</button>';
          html += '<button type="button" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700" onclick="crearAbono(' + facturaId + ')">Crear abono</button>';
          html += '</div>';
          html += '</form>';

          contenedor.innerHTML = html;
        } else {
          contenedor.innerHTML = '<div class="text-center text-gray-500 py-1">No hay productos en esta factura.</div>';
        }
      })
      .catch(() => {
        contenedor.innerHTML = '<div class="text-center text-red-500 py-1">Error al cargar productos.</div>';
      });
  }


  function cerrarModalAbono() {
    document.getElementById('modal-abono').classList.add('hidden');
  }



  async function crearAbono(facturaId) {
    let datosFactura = null;

    try {
      const response = await fetch('obtener-factura.php?id=' + facturaId);
      datosFactura = await response.json();
    } catch (error) {
      console.error('Error al obtener la factura:', error);
      alert('Error al obtener los datos de la factura original');
      return;
    }

    const form = document.getElementById('form-abono-' + facturaId);

    if (!form) {
      alert('Error: No se encontró el formulario para crear el abono');
      return;
    }

    const items = [];
    const checkboxes = form.querySelectorAll('input[type="checkbox"]:checked');

    if (checkboxes.length === 0) {
      alert('Debes seleccionar al menos un producto para crear el abono');
      return;
    }

    let datosIncompletos = false;

    checkboxes.forEach(checkbox => {
      const row = checkbox.closest('tr');
      if (!row) {
        datosIncompletos = true;
        return;
      }

      const cantidad = row.querySelector('input[name="cantidad"]')?.value || "1.00";
      const precio = row.querySelector('input[name="precio"]')?.value || "0.00";
      const nombre = row.querySelector('input[name="nombre"]')?.value || row.children[1]?.innerText;
      const producto_id = checkbox.value;

      if (cantidad && precio && nombre && producto_id) {
        items.push({
          cantidad,
          precio_unitario: precio,
          descripcion: nombre,
          producto_id
        });
      } else {
        datosIncompletos = true;
      }
    });

    if (datosIncompletos) {
      alert('Algunos productos seleccionados no tienen datos completos');
      return;
    }

    if (items.length === 0) {
      alert('No se pudo recopilar información de los productos seleccionados');
      return;
    }

    // Clonamos y preparamos la factura
    const nuevaFactura = {
      ...datosFactura
    };
    delete nuevaFactura.id;
    nuevaFactura.tipo = "abono";

    const payload = {
      factura_origen_id: facturaId,
      datosFactura: nuevaFactura,
      items
    };

    console.log("Datos enviados a crear-abono.php:", payload);

    const response = await fetch('crear-abono.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    });

    const text = await response.text();
    console.log("Respuesta cruda del servidor:", text);

    try {
      const result = JSON.parse(text);
      if (result.success) {
        alert('Abono creado correctamente');
        location.reload();
      } else {
        alert('Error al crear el abono: ' + result.message);
      }
    } catch (e) {
      alert('Error al interpretar la respuesta JSON del servidor');
      console.error('JSON parse error:', e);
    }

  }



  function cerrarAbono(facturaId) {
    var fila = document.getElementById('abono-productos-' + facturaId);
    if (fila) {
      fila.classList.add('hidden');
    }
  }







  // Sistema de Shortcuts para Facturas - facturas.php

  document.addEventListener('DOMContentLoaded', function() {
    let currentRowIndex = -1;
    let isModalOpen = false;
    let isSearchFocused = false;

    // Obtener todas las filas de la tabla (excluyendo header)
    function getTableRows() {
      return document.querySelectorAll('tbody tr');
    }

    // Resaltar fila actual
    function highlightRow(index) {
      const rows = getTableRows();

      // Remover highlight anterior
      rows.forEach(row => {
        row.classList.remove('bg-blue-100', 'border-blue-300');
      });

      // Agregar highlight a la fila actual
      if (index >= 0 && index < rows.length) {
        const currentRow = rows[index];
        currentRow.classList.add('bg-blue-100', 'border-blue-300');

        // Scroll suave hacia la fila si no está visible
        currentRow.scrollIntoView({
          behavior: 'smooth',
          block: 'center'
        });
      }
    }

    // Obtener ID de factura de la fila actual
    function getCurrentInvoiceId() {
      const rows = getTableRows();
      if (currentRowIndex >= 0 && currentRowIndex < rows.length) {
        const row = rows[currentRowIndex];
        return row.querySelector('td:nth-child(2)').textContent.trim();
      }
      return null;
    }

    // Mostrar ayuda de shortcuts
    function showShortcutsHelp() {
      const helpModal = document.createElement('div');
      helpModal.id = 'shortcuts-help-modal';
      helpModal.className = 'modal-center';
      helpModal.innerHTML = `
            <div class="bg-white rounded-lg max-w-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-100">
                    <h2 class="text-xl font-semibold text-gray-800">⌨️ Atajos de Teclado</h2>
                </div>
                <div class="p-6 max-h-96 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold mb-2 text-gray-700">Navegación</h3>
                            <div class="space-y-1 text-sm">
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">↑</kbd> Fila anterior</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">↓</kbd> Fila siguiente</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">Home</kbd> Primera fila</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">End</kbd> Última fila</div>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2 text-gray-700">Acciones</h3>
                            <div class="space-y-1 text-sm">
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">Enter</kbd> Ver factura</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">E</kbd> Editar factura</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">P</kbd> Descargar PDF</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">A</kbd> Crear abono</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">Supr</kbd> Eliminar factura</div>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2 text-gray-700">Estados</h3>
                            <div class="space-y-1 text-sm">
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">1</kbd> Marcar como Borrador</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">2</kbd> Marcar como Emitida</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">3</kbd> Marcar como Pagada</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">4</kbd> Marcar como Vencida</div>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold mb-2 text-gray-700">General</h3>
                            <div class="space-y-1 text-sm">
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">N</kbd> Nueva factura</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">F</kbd> Buscar cliente</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">R</kbd> Recargar página</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">?</kbd> Mostrar ayuda</div>
                                <div><kbd class="bg-gray-200 px-2 py-1 rounded">Esc</kbd> Cerrar modal</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t bg-gray-50 text-right">
                    <button onclick="closeShortcutsHelp()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Cerrar
                    </button>
                </div>
            </div>
        `;

      document.body.appendChild(helpModal);
      isModalOpen = true;
    }

    // Cerrar ayuda de shortcuts
    window.closeShortcutsHelp = function() {
      const modal = document.getElementById('shortcuts-help-modal');
      if (modal) {
        modal.remove();
        isModalOpen = false;
      }
    }

    // Cambiar estado de factura
    function changeInvoiceStatus(status) {
      const invoiceId = getCurrentInvoiceId();
      if (!invoiceId) return;

      const statusSelect = document.querySelector(`select[data-id="${invoiceId}"]`);
      if (statusSelect && !statusSelect.disabled) {
        statusSelect.value = status;
        statusSelect.dispatchEvent(new Event('change'));

        // Mostrar notificación
        showNotification(`Estado cambiado a: ${status.charAt(0).toUpperCase() + status.slice(1)}`);
      }
    }

    // Mostrar notificación temporal
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded shadow-lg text-white transition-all duration-300 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-blue-500'
        }`;
      notification.textContent = message;

      document.body.appendChild(notification);

      setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
      }, 2000);
    }

    // Seleccionar/deseleccionar factura actual
    function toggleCurrentRowSelection() {
      const rows = getTableRows();
      if (currentRowIndex >= 0 && currentRowIndex < rows.length) {
        const checkbox = rows[currentRowIndex].querySelector('input[type="checkbox"]');
        if (checkbox) {
          checkbox.checked = !checkbox.checked;
          showNotification(`Factura ${checkbox.checked ? 'seleccionada' : 'deseleccionada'}`);
        }
      }
    }

    // Event listener principal para shortcuts
    document.addEventListener('keydown', function(e) {
      // Si hay un modal abierto, solo procesar Escape
      if (isModalOpen) {
        if (e.key === 'Escape') {
          closeShortcutsHelp();
          const abonoModal = document.getElementById('modal-abono');
          if (abonoModal && !abonoModal.classList.contains('hidden')) {
            cerrarModalAbono();
          }
        }
        return;
      }

      // Si el foco está en un input, solo procesar Escape
      const activeElement = document.activeElement;
      if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'SELECT' || activeElement.tagName === 'TEXTAREA')) {
        if (e.key === 'Escape') {
          activeElement.blur();
          isSearchFocused = false;
        }
        return;
      }

      const rows = getTableRows();
      if (rows.length === 0) return;

      // Prevenir comportamiento por defecto para teclas que vamos a manejar
      if (['ArrowUp', 'ArrowDown', 'Home', 'End', 'Enter', 'Delete'].includes(e.key) || ['n', 'f', 'r', 'e', 'p', 'a', 'c', '1', '2', '3', '4', '?', ' '].includes(e.key.toLowerCase())) {
        e.preventDefault();
      }

      switch (e.key) {
        // Navegación
        case 'ArrowUp':
          currentRowIndex = Math.max(0, currentRowIndex - 1);
          highlightRow(currentRowIndex);
          break;

        case 'ArrowDown':
          currentRowIndex = Math.min(rows.length - 1, currentRowIndex + 1);
          highlightRow(currentRowIndex);
          break;

        case 'Home':
          currentRowIndex = 0;
          highlightRow(currentRowIndex);
          break;

        case 'End':
          currentRowIndex = rows.length - 1;
          highlightRow(currentRowIndex);
          break;

          // Acciones sobre factura seleccionada
        case 'Enter':
          if (currentRowIndex >= 0) {
            const invoiceId = getCurrentInvoiceId();
            if (invoiceId) {
              window.open(`ver-factura.php?id=${invoiceId}`, '_blank');
            }
          }
          break;

        case 'Delete':
          if (currentRowIndex >= 0) {
            const invoiceId = getCurrentInvoiceId();
            if (invoiceId) {
              eliminarFactura(invoiceId);
            }
          }
          break;

        case ' ': // Espacio para seleccionar/deseleccionar
          toggleCurrentRowSelection();
          break;
      }

      // Shortcuts con letras (case insensitive)
      const key = e.key.toLowerCase();

      switch (key) {
        case 'n': // Nueva factura
          window.location.href = 'crear-factura-manual.php';
          break;

        case 'f': // Focus en búsqueda
          const searchInput = document.getElementById('buscar_cliente');
          if (searchInput) {
            searchInput.focus();
            isSearchFocused = true;
          }
          break;

        case 'r': // Recargar
          if (e.ctrlKey || e.metaKey) return; // Permitir Ctrl+R normal
          window.location.reload();
          break;

        case 'e': // Editar
          if (currentRowIndex >= 0) {
            const invoiceId = getCurrentInvoiceId();
            if (invoiceId) {
              window.location.href = `editar-factura.php?id=${invoiceId}`;
            }
          }
          break;

        case 'p': // PDF
          if (currentRowIndex >= 0) {
            const invoiceId = getCurrentInvoiceId();
            if (invoiceId) {
              window.open(`descargar-factura.php?id=${invoiceId}`, '_blank');
            }
          }
          break;

        case 'a': // Abono
          if (currentRowIndex >= 0) {
            const invoiceId = getCurrentInvoiceId();
            if (invoiceId) {
              mostrarProductosAbono(invoiceId);
            }
          }
          break;

        case 'c': // Send email
          enviarCorreo();
          break;

          // Estados
        case '1':
          changeInvoiceStatus('borrador');
          break;
        case '2':
          changeInvoiceStatus('emitida');
          break;
        case '3':
          changeInvoiceStatus('pagada');
          break;
        case '4':
          changeInvoiceStatus('vencida');
          break;

        case '?': // Ayuda
          showShortcutsHelp();
          break;
      }
    });

    // Inicializar con la primera fila seleccionada
    if (getTableRows().length > 0) {
      currentRowIndex = 0;
      highlightRow(currentRowIndex);
    }

    // Mostrar indicador de shortcuts en la interfaz
    const shortcutsIndicator = document.createElement('div');
    shortcutsIndicator.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-3 py-2 rounded-lg shadow-lg text-sm z-40';
    shortcutsIndicator.innerHTML = 'Presiona <kbd class="bg-gray-600 px-1 rounded">?</kbd> para ver atajos';
    document.body.appendChild(shortcutsIndicator);

    // Ocultar indicador después de unos segundos
    setTimeout(() => {
      shortcutsIndicator.style.opacity = '0.7';
      shortcutsIndicator.style.transition = 'opacity 0.3s';
    }, 3000);

    console.log('🎹 Sistema de shortcuts cargado correctamente');
  });
</script>

<style>
  /* Estilos para los indicadores de shortcuts */
  .shortcut-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 18px;
    background-color: #374151;
    color: white;
    font-size: 10px;
    font-weight: 600;
    border-radius: 3px;
    margin-left: 4px;
    padding: 0 4px;
    font-family: monospace;
    line-height: 1;
    opacity: 0.7;
    transition: opacity 0.2s;
  }


  .shortcut-badge:hover {
    opacity: 1;
  }

  .shortcut-badge.shortcut-enter {
    background-color: #3b82f6;
  }

  .shortcut-badge.shortcut-edit {
    background-color: #d97706;
  }

  .shortcut-badge.shortcut-pdf {
    background-color: #059669;
  }

  .shortcut-badge.shortcut-abono {
    background-color: #dc2626;
  }

  .shortcut-badge.shortcut-delete {
    background-color: #991b1b;
  }

  /* Estilos para los estados con shortcuts */
  .estado-container {
    position: relative;
    display: inline-block;
  }

  .estado-shortcuts {
    display: flex;
    gap: 2px;
    margin-top: 2px;
    flex-wrap: wrap;
  }

  .estado-shortcut {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    background-color: #6b7280;
    color: white;
    font-size: 9px;
    font-weight: 600;
    border-radius: 2px;
    font-family: monospace;
    opacity: 0.6;
  }

  .estado-shortcut.borrador {
    background-color: #6b7280;
  }

  .estado-shortcut.emitida {
    background-color: #d97706;
  }

  .estado-shortcut.pagada {
    background-color: #059669;
  }

  .estado-shortcut.vencida {
    background-color: #dc2626;
  }
</style>

</body>