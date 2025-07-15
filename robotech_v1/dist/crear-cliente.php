<?php
include 'conexion.php';

// Crear cliente
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $empresa = trim($_POST['empresa']);
    $direccion = trim($_POST['direccion']);
    $notas = trim($_POST['notas']);
    $nif = trim($_POST['nif']);
    $forma_pago = $_POST['forma_pago'];
    $plazo_pago = $_POST['plazo_pago'];
    $cuenta_bancaria = trim($_POST['cuenta_bancaria']);

    // Validación básica en servidor
    $errors = [];
    if (empty($nombre)) $errors[] = "El nombre es obligatorio";
    if (empty($email)) $errors[] = "El email es obligatorio";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "El email no es válido";
    if (empty($telefono)) $errors[] = "El teléfono es obligatorio";

    if (empty($errors)) {
        // Preparar insert
        $stmt = $conn->prepare("INSERT INTO clientes (nombre, email, telefono, empresa, direccion, notas, nif, forma_pago, plazo_pago, cuenta_bancaria) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $nombre, $email, $telefono, $empresa, $direccion, $notas, $nif, $forma_pago, $plazo_pago, $cuenta_bancaria);

        if ($stmt->execute()) {
            // Obtener ID recién insertado
            $cliente_id = $conn->insert_id;

            // Generar identificador_cliente con formato 430000XX
            $identificador = "430000" . str_pad($cliente_id, 2, "0", STR_PAD_LEFT);

            // Actualizar identificador_cliente
            $update = $conn->prepare("UPDATE clientes SET identificador_cliente = ? WHERE id = ?");
            $update->bind_param("si", $identificador, $cliente_id);
            $update->execute();

            header("Location: clientes.php?creado=1");
            exit();
        } else {
            $errors[] = "Error al crear el cliente: " . $conn->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es" class="scroll-smooth group" data-sidebar="brand" dir="ltr">

<head>
    <style>
        .py-4 {
            padding-top: 2rem !important;
            padding-bottom: 3rem !important;
        }

        .form-group {
            position: relative;
        }

        .form-group.error input,
        .form-group.error select,
        .form-group.error textarea {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .form-group.error .error-message {
            display: block;
        }

        .success-message {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-loading {
            position: relative;
        }

        .btn-loading::after {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }
    </style>
    <meta charset="utf-8" />
    <title>Crear Nuevo Cliente - Robotech</title>
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
    <?php include 'menu.php' ?>

    <main class="lg:ms-[260px] pt-[90px] px-4 pb-16">
        <div class="max-w-4xl mx-auto">
            <!-- Header Card -->
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="icofont-users text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Crear Nuevo Cliente</h1>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Complete los datos del cliente</p>
                        </div>
                    </div>
                    <a href="clientes.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <i class="icofont-arrow-left"></i>
                        Volver
                    </a>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6">
                    <!-- Success/Error Messages -->
                    <?php if (isset($error)): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-3" role="alert">
                            <i class="icofont-warning text-red-500"></i>
                            <span><?php echo $error; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="icofont-warning text-red-500"></i>
                                <span class="font-medium">Por favor, corrija los siguientes errores:</span>
                            </div>
                            <ul class="list-disc list-inside text-sm">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" id="clientForm" novalidate>
                        <!-- Información Básica -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <i class="icofont-user text-blue-600"></i>
                                Información Básica
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nombre del Cliente <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="nombre" name="nombre" required
                                        class="form-input w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:text-slate-300 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="Ej: Juan Pérez García"
                                        value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                                    <div class="error-message">El nombre es obligatorio</div>
                                </div>

                                <div class="form-group">
                                    <label for="nif" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        NIF/CIF
                                    </label>
                                    <input type="text" id="nif" name="nif"
                                        class="form-input w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:text-slate-300 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="Ej: 12345678A"
                                        value="<?php echo isset($_POST['nif']) ? htmlspecialchars($_POST['nif']) : ''; ?>">
                                    <div class="error-message">Formato de NIF/CIF inválido</div>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" required
                                        class="form-input w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:text-slate-300 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="correo@ejemplo.com"
                                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                    <div class="error-message">Ingrese un email válido</div>
                                </div>

                                <div class="form-group">
                                    <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Teléfono <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" id="telefono" name="telefono" required
                                        class="form-input w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:text-slate-300 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="Ej: +34 600 123 456"
                                        value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
                                    <div class="error-message">El teléfono es obligatorio</div>
                                </div>

                                <div class="form-group md:col-span-2">
                                    <label for="empresa" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Empresa
                                    </label>
                                    <input type="text" id="empresa" name="empresa"
                                        class="form-input w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:text-slate-300 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="Nombre de la empresa (opcional)"
                                        value="<?php echo isset($_POST['empresa']) ? htmlspecialchars($_POST['empresa']) : ''; ?>">
                                </div>

                                <div class="form-group md:col-span-2">
                                    <label for="direccion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Dirección
                                    </label>
                                    <input type="text" id="direccion" name="direccion"
                                        class="form-input w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:text-slate-300 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="Calle, número, ciudad, código postal"
                                        value="<?php echo isset($_POST['direccion']) ? htmlspecialchars($_POST['direccion']) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Información de Pago -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <i class="icofont-credit-card text-green-600"></i>
                                Información de Pago
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="form-group">
                                    <label for="forma_pago" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Forma de Pago
                                    </label>
                                    <select id="forma_pago" name="forma_pago"
                                        class="form-select w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:text-slate-300 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="efectivo" <?php echo (isset($_POST['forma_pago']) && $_POST['forma_pago'] == 'efectivo') ? 'selected' : ''; ?>>💵 Efectivo</option>
                                        <option value="recibo domiciliado" <?php echo (isset($_POST['forma_pago']) && $_POST['forma_pago'] == 'recibo domiciliado') ? 'selected' : ''; ?>>🏦 Recibo domiciliado</option>
                                        <option value="bizum" <?php echo (isset($_POST['forma_pago']) && $_POST['forma_pago'] == 'bizum') ? 'selected' : ''; ?>>📱 Bizum</option>
                                        <option value="transferencia" <?php echo (isset($_POST['forma_pago']) && $_POST['forma_pago'] == 'transferencia') ? 'selected' : ''; ?>>💳 Transferencia</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="plazo_pago" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Plazo de Pago
                                    </label>
                                    <select id="plazo_pago" name="plazo_pago"
                                        class="form-select w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:text-slate-300 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="Recibo al cobro 15 dias" <?php echo (isset($_POST['plazo_pago']) && $_POST['plazo_pago'] == 'Recibo al cobro 15 dias') ? 'selected' : ''; ?>>15 días</option>
                                        <option value="Recibo a 30 dias" <?php echo (isset($_POST['plazo_pago']) && $_POST['plazo_pago'] == 'Recibo a 30 dias') ? 'selected' : ''; ?>>30 días</option>
                                        <option value="Recibo a 30,60 dias" <?php echo (isset($_POST['plazo_pago']) && $_POST['plazo_pago'] == 'Recibo a 30,60 dias') ? 'selected' : ''; ?>>30-60 días</option>
                                        <option value="Recibo a 60 dias" <?php echo (isset($_POST['plazo_pago']) && $_POST['plazo_pago'] == 'Recibo a 60 dias') ? 'selected' : ''; ?>>60 días</option>
                                        <option value="Recibo a 60,90 dias" <?php echo (isset($_POST['plazo_pago']) && $_POST['plazo_pago'] == 'Recibo a 60,90 dias') ? 'selected' : ''; ?>>60-90 días</option>
                                        <option value="Recibo a 60,90 dias" <?php echo (isset($_POST['plazo_pago']) && $_POST['plazo_pago'] == 'Recibo a 30,60,90 dias') ? 'selected' : ''; ?>>30-60-90 días</option>

                                        <option value="Recibo 90 dias" <?php echo (isset($_POST['plazo_pago']) && $_POST['plazo_pago'] == 'Recibo 90 dias') ? 'selected' : ''; ?>>90 días</option>
                                        <option value="Recibo a 120 dias" <?php echo (isset($_POST['plazo_pago']) && $_POST['plazo_pago'] == 'Recibo a 120 dias') ? 'selected' : ''; ?>>120 días</option>
                                    </select>
                                </div>

                                <div class="form-group md:col-span-2">
                                    <label for="cuenta_bancaria" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Cuenta Bancaria (IBAN)
                                    </label>
                                    <input type="text" id="cuenta_bancaria" name="cuenta_bancaria" maxlength="34"
                                        class="form-input w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:text-slate-300 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="ES00 0000 0000 0000 0000 0000"
                                        value="<?php echo isset($_POST['cuenta_bancaria']) ? htmlspecialchars($_POST['cuenta_bancaria']) : ''; ?>">
                                    <div class="error-message">Formato de IBAN inválido</div>
                                </div>
                            </div>
                        </div>

                        <!-- Notas -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <i class="icofont-note text-purple-600"></i>
                                Información Adicional
                            </h3>

                            <div class="form-group">
                                <label for="notas" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Notas
                                </label>
                                <textarea id="notas" name="notas" rows="4"
                                    class="form-textarea w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:text-slate-300 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                    placeholder="Información adicional sobre el cliente, preferencias, comentarios..."><?php echo isset($_POST['notas']) ? htmlspecialchars($_POST['notas']) : ''; ?></textarea>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span id="notasCount">0</span>/500 caracteres
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-between items-center mt-16 mb-8 pt-8 px-4">
                            <a href="clientes.php" 
                                class="flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out"
                                onclick="return confirm('¿Está seguro que desea cancelar? Los cambios no guardados se perderán')">
                                <i class="icofont-arrow-left mr-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" id="submitBtn"
                                class="flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                <i class="icofont-save mr-2"></i>
                                <span>Guardar Cambios</span>
                            </button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- JAVASCRIPTS -->
    <script src="assets/libs/lucide/umd/lucide.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/flatpickr/flatpickr.min.js"></script>
    <script src="assets/libs/@frostui/tailwindcss/frostui.js"></script>
    <script src="assets/js/app.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('clientForm');
            const submitBtn = document.getElementById('submitBtn');
            const notasTextarea = document.getElementById('notas');
            const notasCount = document.getElementById('notasCount');

            // Required fields for validation
            const requiredFields = ['nombre', 'email', 'telefono'];
            const allFields = ['nombre', 'email', 'telefono', 'nif', 'empresa', 'direccion', 'forma_pago', 'plazo_pago', 'cuenta_bancaria', 'notas'];

            // Field validators
            const validators = {
                nombre: (value) => value.trim().length > 0,
                email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
                telefono: (value) => /^[\+]?[0-9\s\-\(\)]{9,}$/.test(value.replace(/\s/g, '')),
                nif: (value) => !value || /^[0-9]{8}[A-Z]$|^[XYZ][0-9]{7}[A-Z]$/.test(value.toUpperCase()),
                cuenta_bancaria: (value) => !value || /^ES[0-9]{22}$/.test(value.replace(/\s/g, ''))
            };

            // Character counter for notes
            function updateNotasCount() {
                const count = notasTextarea.value.length;
                notasCount.textContent = count;
                if (count > 500) {
                    notasTextarea.value = notasTextarea.value.substring(0, 500);
                    notasCount.textContent = 500;
                }
            }

            notasTextarea.addEventListener('input', updateNotasCount);
            updateNotasCount();

            // Validate individual field
            function validateField(fieldName, showError = true) {
                const field = document.getElementById(fieldName);
                const formGroup = field.closest('.form-group');
                const validator = validators[fieldName];

                if (!field) return true;

                const isValid = !validator || validator(field.value);

                if (showError) {
                    if (isValid) {
                        formGroup.classList.remove('error');
                    } else {
                        formGroup.classList.add('error');
                    }
                }

                return isValid;
            }

            // Format IBAN as typed
            document.getElementById('cuenta_bancaria').addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s/g, '').toUpperCase();
                if (value.length > 0) {
                    value = value.match(/.{1,4}/g).join(' ');
                }
                e.target.value = value;
            });

            // Format NIF as typed
            document.getElementById('nif').addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase();
            });

            // Real-time validation events
            allFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field) {
                    field.addEventListener('input', () => {
                        validateField(fieldName, false);
                    });

                    field.addEventListener('blur', () => {
                        validateField(fieldName, true);
                    });
                }
            });

            // Form validation
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                let isValid = true;

                // Validate required fields
                requiredFields.forEach(fieldName => {
                    if (!validateField(fieldName, true)) {
                        isValid = false;
                    }
                });

                // Validate optional fields with specific format
                ['nif', 'cuenta_bancaria'].forEach(fieldName => {
                    if (!validateField(fieldName, true)) {
                        isValid = false;
                    }
                });

                if (isValid) {
                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.classList.add('btn-loading');
                    submitBtn.innerHTML = '<span class="opacity-0">Guardar Cliente</span>';

                    // Submit form
                    setTimeout(() => {
                        form.submit();
                    }, 500);
                } else {
                    // Scroll to first error
                    const firstError = document.querySelector('.form-group.error');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstError.querySelector('input, select, textarea').focus();
                    }
                }
            });
        });
    </script>
</body>

</html>