<?php
    session_start();

    include 'conexion.php';
    // Inicializar variables
    $error = '';
    $success = false;
    
    // Verificar si se ha enviado el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validar los datos del formulario
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmar_password = $_POST['confirmar_password'] ?? '';
        $telefono = $_POST['telefono'] ?? ''; // Cambiado de Mobile_Number a telefono

        // Validaciones básicas
        if (empty($nombre) || empty($email) || empty($password)) {
            $error = "Todos los campos son obligatorios";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "El formato del correo electrónico no es válido";
        } elseif (strlen($password) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres";
        } elseif ($password !== $confirmar_password) {
            $error = "Las contraseñas no coinciden";
        } else {
            // Verificar si el correo ya existe en la base de datos
            $check_email = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
            $check_email->bind_param("s", $email);
            $check_email->execute();
            $result = $check_email->get_result();
            
            if ($result->num_rows > 0) {
                $error = "Este correo electrónico ya está registrado";
            } else {
                // Encriptar la contraseña
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                
                // Insertar el usuario administrador
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, telefono) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $nombre, $email, $hashed_password, $telefono);
                $stmt->execute();
                $usuario_id = $stmt->insert_id;
                
                // Guardar en sesión y redirigir
                $_SESSION['usuario'] = $nombre;
                $_SESSION['usuario_id'] = $usuario_id;
                
                // Redirigir al login
                header("Location: auth-login.php?registro=ok");
                exit();
            }
        }
    }
    ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth group" data-sidebar="brand" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creación de Usuario Administrador</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 500px;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #34495e;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }
        .help-text {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        button {
            width: 100%;
            padding: 14px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .progress-bar {
            display: flex;
            margin-bottom: 30px;
            justify-content: center;
        }
        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 120px;
        }
        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #ecf0f1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7f8c8d;
            font-weight: bold;
            margin-bottom: 8px;
            position: relative;
        }
        .step-circle.active {
            background-color: #3498db;
            color: white;
        }
        .step-circle.completed {
            background-color: #2ecc71;
            color: white;
        }
        .step-circle.completed::after {
            content: '✓';
        }
        .step-title {
            font-size: 12px;
            color: #7f8c8d;
            text-align: center;
        }
        .step-line {
            height: 3px;
            width: 60px;
            background-color: #ecf0f1;
            margin: 15px 0;
        }
        .step-line.completed {
            background-color: #2ecc71;
        }
        .error-message {
            background-color: #fee;
            border-left: 4px solid #e74c3c;
            color: #c0392b;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            font-size: 14px;
        }
        .error-message svg {
            margin-right: 10px;
            min-width: 20px;
            color: #e74c3c;
        }
        .password-strength {
            margin-top: 8px;
        }
        .strength-meter {
            height: 4px;
            border-radius: 2px;
            margin-top: 5px;
            background-color: #ecf0f1;
            overflow: hidden;
        }
        .strength-meter div {
            height: 100%;
            width: 0%;
            transition: width 0.3s;
        }
        .strength-text {
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#3498db" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
        </div>

        <div class="progress-bar">
            <div class="progress-step">
                <div class="step-circle completed">1</div>
                <span class="step-title">Configuración BD</span>
            </div>
            <div class="step-line completed"></div>
            <div class="progress-step">
                <div class="step-circle active">2</div>
                <span class="step-title">Crear Admin</span>
            </div>
            <div class="step-line"></div>
            <div class="progress-step">
                <div class="step-circle">3</div>
                <span class="step-title">Finalizar</span>
            </div>
        </div>

        <h1>Creación de Usuario Administrador</h1>

        <form method="POST">
            <div class="form-group">
                <?php if ($error): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    <div class="help-text">Se utilizará para iniciar sesión y recuperación de contraseña</div>
                </div>

                <div class="form-group">
                    <label for="telefono">Telefono</label>
                    <input type="telefono" id="telefono" name="telefono" value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>" required>

                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                    <div class="password-strength">
                    <div class="strength-meter">
                        <div id="strength-bar"></div>
                    </div>
                    <div class="strength-text" id="strength-text">Ingrese una contraseña</div>
                </div>
                <div class="help-text">Mínimo 8 caracteres. Combine letras, números y símbolos</div>

                </div>
                
                <div class="form-group">
                    <label for="confirmar_password">Confirmar Contraseña</label>
                    <input type="password" id="confirmar_password" name="confirmar_password" required>
                    <div class="help-text" id="password-match"></div>
                </div>
            </div>

            <button type="submit">Crear Cuenta de Administrador</button>
        </form>
    </div>
    <script>
        // Función para evaluar la fortaleza de la contraseña
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');
        
        // Criterios de fortaleza
        const length = password.length;
        const hasLowerCase = /[a-z]/.test(password);
        const hasUpperCase = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecialChar = /[^A-Za-z0-9]/.test(password);
        
        // Calcular puntuación (0-100)
        let strength = 0;
        let criteria = 0;
        
        if (length > 0) criteria++;
        if (length >= 8) criteria++;
        if (hasLowerCase) criteria++;
        if (hasUpperCase) criteria++;
        if (hasNumber) criteria++;
        if (hasSpecialChar) criteria++;
        
        strength = Math.min(100, Math.floor((criteria / 6) * 100));
        
        // Actualizar la barra de fortaleza
        strengthBar.style.width = strength + '%';
        
        // Asignar color según fortaleza
        if (strength < 30) {
            strengthBar.style.backgroundColor = '#e74c3c'; // Rojo
            strengthText.textContent = 'Muy débil';
        } else if (strength < 60) {
            strengthBar.style.backgroundColor = '#f39c12'; // Naranja
            strengthText.textContent = 'Débil';
        } else if (strength < 80) {
            strengthBar.style.backgroundColor = '#f1c40f'; // Amarillo
            strengthText.textContent = 'Media';
        } else {
            strengthBar.style.backgroundColor = '#2ecc71'; // Verde
            strengthText.textContent = 'Fuerte';
        }
    });
    
    // Verificar que las contraseñas coincidan
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        const matchText = document.getElementById('password-match');
        
        if (confirmPassword === '') {
            matchText.textContent = '';
            matchText.style.color = '#7f8c8d';
        } else if (password === confirmPassword) {
            matchText.textContent = 'Las contraseñas coinciden';
            matchText.style.color = '#2ecc71';
        } else {
            matchText.textContent = 'Las contraseñas no coinciden';
            matchText.style.color = '#e74c3c';
        }
    });
    </script>
</body>

</html>