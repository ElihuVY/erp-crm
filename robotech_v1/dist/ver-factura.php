<?php
include 'conexion.php';

if (!isset($_GET['id'])) {
    echo "❌ ID de factura no especificado.";
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT f.*, 
               c.nombre AS cliente, c.email, c.telefono, c.empresa, c.direccion,
               o.nombre AS org_nombre, o.cif AS org_cif, o.direccion AS org_direccion,
               s.prefijo
        FROM facturas f
        JOIN clientes c ON f.cliente_id = c.id
        JOIN organizaciones o ON f.organizacion_id = o.id
        JOIN series s ON f.serie_id = s.id
        WHERE f.id = $id";

$result = $conn->query($sql);
if ($result->num_rows === 0) {
    echo "❌ Factura no encontrada.";
    exit();
}

$factura = $result->fetch_assoc();

// Obtener productos de la factura
$items = [];
$stmt = $conn->prepare("SELECT descripcion, cantidad, precio_unitario FROM factura_items WHERE factura_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $items[] = $row;
}

// Historial de facturas recurrentes (si aplica)
$pagos_realizados = [];
$total_pagado = 0;

if ($factura['tipo'] === 'recurrente' && !empty($factura['recurrente_id'])) {
    $recurrente_id = $factura['recurrente_id'];
    $stmt = $conn->prepare("SELECT id, fecha_emision, total, estado FROM facturas WHERE recurrente_id = ? ORDER BY fecha_emision ASC");
    $stmt->bind_param("i", $recurrente_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $pagos_realizados[] = $row;
        if ($row['estado'] === 'pagada') {
            $total_pagado += $row['total'];
        }
    }
}


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
        <meta  name="viewport"  content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
        <meta  content="Tailwind Multipurpose Admin & Dashboard Template"  name="description"/>
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
    
    <body data-layout-mode="light"  data-sidebar-size="default" data-theme-layout="vertical" class="bg-[#EEF0FC] dark:bg-gray-900">
    
        <!-- leftbar-tab-menu -->
        

        <?php
  include 'menu.php';
  ?>

  <div class="xl:w-full">
    <div class="flex flex-wrap">
      <div class="flex items-center py-4 w-full">
        <div class="w-full">
          <main class="lg:ms-[260px] pt-[90px] px-4 pb-16">
            <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
              <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">
                  📄 Factura <?php echo htmlspecialchars($factura['prefijo']) . '-' . $factura['numero_factura']; ?>
                </h1>
                <div>
                  <a href="descargar-factura.php?id=<?php echo $factura['id']; ?>" class="bg-blue-600 text-white px-4 py-1 rounded">📄 PDF</a>
                  <a href="enviar-factura.php?id=<?php echo $factura['id']; ?>" class="bg-green-600 text-white px-4 py-1 rounded">✉️ Enviar</a>
                </div>
              </div>

              <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Organización emisora</h2>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($factura['org_nombre']); ?></p>
                <p><strong>CIF:</strong> <?php echo htmlspecialchars($factura['org_cif']); ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($factura['org_direccion']); ?></p>
              </div>

              <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Cliente</h2>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($factura['cliente']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($factura['email']); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($factura['telefono']); ?></p>
                <p><strong>Empresa:</strong> <?php echo htmlspecialchars($factura['empresa']); ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($factura['direccion']); ?></p>
              </div>

              <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Artículos</h2>
                <table class="w-full text-left border">
                  <thead>
                    <tr class="bg-gray-100">
                      <th class="p-2">Descripción</th>
                      <th class="p-2">Cantidad</th>
                      <th class="p-2">Precio unitario</th>
                      <th class="p-2">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $subtotal = 0;
                    foreach ($items as $item): 
                      $total_item = $item['cantidad'] * $item['precio_unitario'];
                      $subtotal += $total_item;
                    ?>
                      <tr class="border-t">
                        <td class="p-2"><?php echo htmlspecialchars($item['descripcion']); ?></td>
                        <td class="p-2"><?php echo $item['cantidad']; ?></td>
                        <td class="p-2">€<?php echo number_format($item['precio_unitario'], 2); ?></td>
                        <td class="p-2">€<?php echo number_format($total_item, 2); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <?php
                    $iva_total = ($subtotal * $factura['iva']) / 100;
                    $irpf_total = ($subtotal * $factura['irpf']) / 100;
                    $total_final = $subtotal + $iva_total - $irpf_total;
                    ?>
                    <tr class="bg-gray-50 font-semibold">
                      <td colspan="3" class="border px-3 py-2 text-right">Subtotal</td>
                      <td class="border px-3 py-2 text-right">€<?= number_format($subtotal, 2) ?></td>
                    </tr>
                    <tr>
                      <td colspan="3" class="border px-3 py-2 text-right">IVA (<?= $factura['iva'] ?>%)</td>
                      <td class="border px-3 py-2 text-right">€<?= number_format($iva_total, 2) ?></td>
                    </tr>
                    <tr>
                      <td colspan="3" class="border px-3 py-2 text-right">IRPF (<?= $factura['irpf'] ?>%)</td>
                      <td class="border px-3 py-2 text-right">-€<?= number_format($irpf_total, 2) ?></td>
                    </tr>
                    <tr class="bg-gray-100 font-bold">
                      <td colspan="3" class="border px-3 py-2 text-right">Total Final</td>
                      <td class="border px-3 py-2 text-right">€<?= number_format($total_final, 2) ?></td>
                    </tr>
                  </tfoot>
                </table>
              </div>

              <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Detalles de la factura</h2>
                <p><strong>Tipo:</strong> <?php echo $factura['tipo']; ?></p>
                <p><strong>Estado:</strong> <?php echo ucfirst($factura['estado']); ?></p>
                <p><strong>Emitida:</strong> <?php echo $factura['fecha_emision']; ?></p>
                <p><strong>Vencimiento:</strong> <?php echo $factura['fecha_vencimiento'] ?? '—'; ?></p>
                <p><strong>Total:</strong> €<?php echo number_format($factura['total'], 2); ?></p>
                <p><strong>IVA:</strong> <?php echo $factura['iva']; ?>%</p>
                <p><strong>IRPF:</strong> <?php echo $factura['irpf']; ?>%</p>
                <p><strong>Impuestos adicionales:</strong> <?php echo $factura['impuestos_extra'] ?? '—'; ?></p>
              </div>

              <?php if ($factura['tipo'] === 'recurrente'): ?>
                <div class="mb-6">
                  <h2 class="text-xl font-semibold text-gray-700 mb-2">Historial de facturas recurrentes</h2>
                  <p><strong>Periodo:</strong> <?php echo $factura['periodo_recurrente']; ?></p>
                  <p><strong>Recurrente hasta:</strong> <?php echo $factura['recurrente_hasta'] ?? '—'; ?></p>
                  <p><strong>Pagos realizados:</strong> <?php echo count($pagos_realizados); ?></p>
                  <p><strong>Total pagado:</strong> €<?php echo number_format($total_pagado, 2); ?></p>
                  <ul class="list-disc ml-6 text-sm text-gray-600">
                    <?php foreach ($pagos_realizados as $pago): ?>
                      <li>
                        <?= $pago['fecha_emision']; ?> – €<?= number_format($pago['total'], 2); ?> 
                        (<?= ucfirst($pago['estado']); ?>)
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>

              <?php if (!empty($factura['notas'])): ?>
                <div class="mb-6">
                  <h2 class="text-xl font-semibold text-gray-700 mb-2">Notas</h2>
                  <p class="text-sm text-gray-800 whitespace-pre-line">
                    <?= nl2br(htmlspecialchars($factura['notas'])) ?>
                  </p>
                </div>
              <?php endif; ?>

              <div class="mt-6">
                <a href="facturas.php" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">⬅ Volver</a>
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
            document.addEventListener("DOMContentLoaded", function () {
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
                document.addEventListener("DOMContentLoaded", function () {
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
document.querySelectorAll('.estado-select').forEach(select => {
  select.addEventListener('change', async function () {
    const facturaId = this.dataset.id;
    const nuevoEstado = this.value;
    const mensajeError = document.getElementById('mensaje-error-' + facturaId);

    const formData = new FormData();
    formData.append('factura_id', facturaId);
    formData.append('estado', nuevoEstado);

    const res = await fetch('ajax-cambiar-estado.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();

    if (data.success) {
      mensajeError.classList.add('hidden');
      mensajeError.innerText = '';
      location.reload();
    } else {
      mensajeError.classList.remove('hidden');
      mensajeError.innerText = data.message;
      this.value = data.estado_actual;
    }
  });
});
</script>
<?php


