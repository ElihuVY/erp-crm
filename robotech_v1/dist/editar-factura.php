<?php
include 'conexion.php';

if (!isset($_GET['id'])) {
    die("Factura no especificada.");
}

$factura_id = intval($_GET['id']);
$usuario_id = $_SESSION['usuario_id'];

// Obtener organizaciones del usuario
$organizaciones = $conn->query("SELECT o.* FROM organizaciones o JOIN usuario_organizacion uo ON o.id = uo.organizacion_id WHERE uo.usuario_id = '$usuario_id'");

// Obtener la factura
$res = $conn->query("SELECT * FROM facturas WHERE id = $factura_id");
if ($res->num_rows === 0) {
    die("Factura no encontrada.");
}
$factura = $res->fetch_assoc();

// Obtener items
$items = $conn->query("SELECT * FROM factura_items WHERE factura_id = $factura_id")->fetch_all(MYSQLI_ASSOC);

// Al guardar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $organizacion_id = $_POST['organizacion_id'];
    $estado = $_POST['estado'];
    $fecha_emision = $_POST['fecha_emision'];
    $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
    $iva = $_POST['iva'];
    $irpf = $_POST['irpf'];
    $impuestos_extra = $_POST['impuestos_extra'] ?? null;

    $productos = $_POST['productos'];
    $cantidades = $_POST['cantidades'];

    // Calcular total
    $total = 0;
    $productos_data = [];
    foreach ($productos as $i => $producto_id) {
        $cantidad = $cantidades[$i];
        $stmt = $conn->prepare("SELECT nombre, precio FROM productos WHERE id = ? AND organizacion_id = ?");
        $stmt->bind_param("ii", $producto_id, $organizacion_id);
        $stmt->execute();
        $r = $stmt->get_result();
        if ($p = $r->fetch_assoc()) {
            $subtotal = $p['precio'] * $cantidad;
            $total += $subtotal;
            $productos_data[] = [
                'descripcion' => $p['nombre'],
                'cantidad' => $cantidad,
                'precio' => $p['precio']
            ];
        }
    }

    // Actualizar factura
    $stmt = $conn->prepare("UPDATE facturas SET cliente_id = ?, organizacion_id = ?, estado = ?, fecha_emision = ?, fecha_vencimiento = ?, total = ?, iva = ?, irpf = ?, impuestos_extra = ? WHERE id = ?");
    $stmt->bind_param("iisssddssi", $cliente_id, $organizacion_id, $estado, $fecha_emision, $fecha_vencimiento, $total, $iva, $irpf, $impuestos_extra, $factura_id);
    $stmt->execute();

    // Eliminar items anteriores
    $conn->query("DELETE FROM factura_items WHERE factura_id = $factura_id");

    // Insertar nuevos items
    foreach ($productos_data as $item) {
        $stmt = $conn->prepare("INSERT INTO factura_items (factura_id, descripcion, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isid", $factura_id, $item['descripcion'], $item['cantidad'], $item['precio']);
        $stmt->execute();
    }

    header("Location: ver-factura.php?id=$factura_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth group" data-sidebar="brand" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Editar Factura</title>
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta content="Tailwind Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="Mannatthemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- Main Css -->
    <link rel="stylesheet" href="assets/libs/icofont/icofont.min.css">
    <link href="assets/libs/flatpickr/flatpickr.min.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/tailwind.min.css">
</head>


<body data-layout-mode="light" data-sidebar-size="default" data-theme-layout="vertical" class="bg-[#EEF0FC] dark:bg-gray-900">

    <script>
        function agregarProducto() {
            const contenedor = document.getElementById('productos-container');
            const template = document.querySelector('.producto-item.hidden');
            const clon = template.cloneNode(true);
            clon.classList.remove('hidden');
            contenedor.appendChild(clon);
        }
    </script>
    <?php
    include 'menu.php';
    ?>
    <div class="xl:w-full">
        <div class="flex flex-wrap">
            <div class="flex items-center py-4 w-full">
                <div class="w-full">
                    <main class="lg:ms-[260px] pt-[90px] px-4 pb-16">
                        <div class="max-w-full lg:max-w-6xl mx-auto bg-white p-4 sm:p-6 rounded shadow">


                            <div class="flex justify-between items-center mb-6 flex-wrap gap-4">
                                <h2 class="text-xl font-bold mb-4">✏️ Editar Factura</h2>

                                <form method="POST" class="space-y-4">
                                    <label>Organización emisora:
                                        <select name="organizacion_id" class="border p-2 w-full" required>
                                            <?php while ($org = $organizaciones->fetch_assoc()): ?>
                                                <option value="<?= $org['id'] ?>" <?= $factura['organizacion_id'] == $org['id'] ? 'selected' : '' ?>><?= htmlspecialchars($org['nombre']) ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </label>

                                    <label>Cliente:
                                        <select name="cliente_id" class="border p-2 w-full" required>
                                            <?php
                                            $res = $conn->query("SELECT id, nombre FROM clientes ORDER BY nombre ASC");
                                            while ($cli = $res->fetch_assoc()) {
                                                $selected = $cli['id'] == $factura['cliente_id'] ? 'selected' : '';
                                                echo "<option value='{$cli['id']}' $selected>" . htmlspecialchars($cli['nombre']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </label>

                                    <label>Estado:
                                        <select name="estado" class="border p-2 w-full" required>
                                            <?php
                                            $estados = ['borrador', 'emitida', 'pagada', 'vencida'];
                                            foreach ($estados as $estado) {
                                                $selected = $factura['estado'] === $estado ? 'selected' : '';
                                                echo "<option value='$estado' $selected>" . ucfirst($estado) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </label>

                                    <label>Fecha de emisión:
                                        <input type="date" name="fecha_emision" class="border p-2 w-full" value="<?= $factura['fecha_emision'] ?>" required>
                                    </label>

                                    <label>Fecha de vencimiento:
                                        <input type="date" name="fecha_vencimiento" class="border p-2 w-full" value="<?= $factura['fecha_vencimiento'] ?>">
                                    </label>

                                    <label>IVA (%):
                                        <input type="number" step="0.01" name="iva" class="border p-2 w-full" value="<?= $factura['iva'] ?>">
                                    </label>

                                    <label>IRPF (%):
                                        <input type="number" step="0.01" name="irpf" class="border p-2 w-full" value="<?= $factura['irpf'] ?>">
                                    </label>

                                    <label>Impuestos adicionales:
                                        <input type="text" name="impuestos_extra" class="border p-2 w-full" value="<?= htmlspecialchars($factura['impuestos_extra']) ?>">
                                    </label>

                                    <div class="border p-4 rounded bg-gray-50">
                                        <h3 class="font-semibold mb-2">🧺 Productos</h3>
                                        <div id="productos-container" class="space-y-2">
                                            <?php foreach ($items as $item): ?>
                                                <div class="producto-item space-y-2">
                                                    <label>Producto:
                                                        <select name="productos[]" class="border p-2 w-full">
                                                            <?php
                                                            $res = $conn->query("SELECT id, nombre FROM productos WHERE organizacion_id = {$factura['organizacion_id']} ORDER BY nombre ASC");
                                                            while ($p = $res->fetch_assoc()) {
                                                                $selected = $item['descripcion'] === $p['nombre'] ? 'selected' : '';
                                                                echo "<option value='{$p['id']}' $selected>" . htmlspecialchars($p['nombre']) . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </label>
                                                    <label>Cantidad:
                                                        <input type="number" name="cantidades[]" class="border p-2 w-full" value="<?= $item['cantidad'] ?>" min="1">
                                                    </label>
                                                    <hr>
                                                </div>
                                            <?php endforeach; ?>

                                            <!-- plantilla oculta -->
                                            <div class="producto-item space-y-2 hidden">
                                                <label>Producto:
                                                    <select name="productos[]" class="border p-2 w-full">
                                                        <?php
                                                        $res = $conn->query("SELECT id, nombre FROM productos ORDER BY nombre ASC");
                                                        while ($p = $res->fetch_assoc()) {
                                                            echo "<option value='{$p['id']}'>" . htmlspecialchars($p['nombre']) . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </label>
                                                <label>Cantidad:
                                                    <input type="number" name="cantidades[]" class="border p-2 w-full" min="1" value="1">
                                                </label>
                                                <hr>
                                            </div>
                                        </div>
                                        <button type="button" onclick="agregarProducto()" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 mt-2">+ Añadir producto</button>
                                    </div>

                                    <div class="flex justify-between items-center mt-6">
                                        <a href="facturas.php" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">Cancelar</a>
                                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
    <!-- JAVASCRIPTS -->

    <script src="assets/libs/lucide/umd/lucide.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="assets/libs/@frostui/tailwindcss/frostui.js"></script>

    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="assets/js/pages/analytics-index.init.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>