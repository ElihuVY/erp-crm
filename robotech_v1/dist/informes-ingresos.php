<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("<p class='text-red-600 font-semibold'>❌ Error: Usuario no válido.</p>");
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nombre = $_SESSION['usuario'] ?? 'Desconocido';

// Conexión
$conn = new mysqli("localhost", "root", "", "erp-crm");
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Organizaciones vinculadas
$organizaciones = $conn->query("
    SELECT o.id, o.nombre 
    FROM organizaciones o 
    JOIN usuario_organizacion uo ON uo.organizacion_id = o.id 
    WHERE uo.usuario_id = $usuario_id
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>📈 Informe de Ingresos</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="page-wrapper w-full px-4 pt-[64px]">
    <main class="pt-[90px] px-4 pb-16 max-w-6xl mx-auto">
        <div class="bg-white p-6 rounded shadow">
            <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📈 Informe de Ingresos</h1>
    <a href="facturas-vencidas.php"
       class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-all text-sm font-medium">
        ⏰ Ver Facturas Vencidas
    </a>
</div>


            <div class="text-sm text-gray-500 mb-4">
                👤 Usuario conectado: <strong><?= htmlspecialchars($usuario_nombre) ?></strong> (ID: <?= $usuario_id ?>)
            </div>

            <!-- FILTROS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <select id="organizacion" class="border border-gray-300 p-2 rounded">
                    <option value="">Selecciona organización</option>
                    <?php foreach ($organizaciones as $o): ?>
                        <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="periodo" class="border border-gray-300 p-2 rounded">
                    <option value="mensual">Mensual</option>
                    <option value="trimestral">Trimestral</option>
                    <option value="anual">Anual</option>
                </select>
                <select id="anio" class="border border-gray-300 p-2 rounded">
                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>"><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- GRÁFICO -->
            <canvas id="graficoIngresos" height="100"></canvas>
            <p id="mensaje" class="text-red-500 mt-4 font-semibold"></p>

            <!-- RESUMEN -->
            <div id="tablaDatos" class="mt-8 hidden">
                <h2 class="text-lg font-semibold mb-2">📊 Resumen de ingresos</h2>
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100 text-left text-sm font-medium text-gray-600">
                            <th class="py-2 px-4 border-b">Periodo</th>
                            <th class="py-2 px-4 border-b">Total Ingresado (€)</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyDatos" class="text-sm text-gray-800"></tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
const orgSelect = document.getElementById("organizacion");
const periodoSelect = document.getElementById("periodo");
const anioSelect = document.getElementById("anio");
const mensaje = document.getElementById("mensaje");
const tabla = document.getElementById("tablaDatos");
const tbody = document.getElementById("tbodyDatos");

let chart;

function cargarInforme() {
    const organizacion_id = orgSelect.value;
    const periodo = periodoSelect.value;
    const anio = anioSelect.value;

    if (!organizacion_id) {
        mensaje.textContent = "⚠️ Selecciona una organización.";
        if (chart) chart.destroy();
        tabla.classList.add("hidden");
        return;
    }

    fetch(`ingresos-datos.php?organizacion_id=${organizacion_id}&periodo=${periodo}&anio=${anio}`)
        .then(res => res.json())
        .then(data => {
            if (!data || data.length === 0) {
                mensaje.textContent = "ℹ️ No hay datos para este periodo.";
                if (chart) chart.destroy();
                tabla.classList.add("hidden");
                return;
            }

            mensaje.textContent = "";

            // GRÁFICO
            const labels = data.map(row => row.label);
            const ingresos = data.map(row => parseFloat(row.total));

            if (chart) chart.destroy();
            const ctx = document.getElementById('graficoIngresos').getContext('2d');
            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ingresos (€)',
                        data: ingresos,
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: value => '€' + value }
                        }
                    }
                }
            });

            // TABLA RESUMEN
            tbody.innerHTML = "";
            data.forEach(row => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td class="border-b py-2 px-4">${row.label}</td>
                    <td class="border-b py-2 px-4 font-semibold">€${row.total}</td>
                `;
                tbody.appendChild(tr);
            });
            tabla.classList.remove("hidden");
        });
}

orgSelect.addEventListener("change", cargarInforme);
periodoSelect.addEventListener("change", cargarInforme);
anioSelect.addEventListener("change", cargarInforme);
window.addEventListener("DOMContentLoaded", cargarInforme);
</script>
</body>
</html>
