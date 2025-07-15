<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Base de Datos</title>
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

        .logo i {
            font-size: 50px;
            color: #3498db;
        }

        .error-message {
            background-color: #fce4e4;
            color: #c0392b;
            border: 1px solid #e74c3c;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php
    

    mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ALL);
    $error = ""; // Inicializar la variable de error

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $host = $_POST["host"];
        $user = $_POST["user"];
        $pass = $_POST["pass"];
        $db   = $_POST["db"];

        try {
            $conn = new mysqli($host, $user, $pass);

            if ($conn->connect_error) {
                throw new Exception("No se pudo conectar con el servidor de base de datos: " . $conn->connect_error);
            }

            // Crear la base de datos si no existe y seleccionarla
            $conn->query("CREATE DATABASE IF NOT EXISTS `$db`");
            $conn->select_db($db);

            // Guardar config
            $config = [
                "host" => $host,
                "user" => $user,
                "pass" => $pass,
                "db"   => $db
            ];
            file_put_contents("config-db.json", json_encode($config));

            $es_nueva_bdd = true;

            // Cambiamos el nivel de reporte temporalmente para esta verificación
            $old_report_mode = mysqli_report(0);

            // Verificamos si existe la tabla usuarios usando INFORMATION_SCHEMA
            $result = $conn->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES 
                          WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = 'usuarios'");

            // Restauramos el modo de reporte original
            mysqli_report($old_report_mode);

            if ($result && $result->num_rows > 0) {
                // La tabla usuarios existe, verificamos si tiene registros
                $check_users = $conn->query("SELECT COUNT(*) as total FROM usuarios");
                $row = $check_users->fetch_assoc();
                if ($row['total'] > 0) {
                    $es_nueva_bdd = false;
                }
            }

            if ($es_nueva_bdd) {
                include_once('crear_tablas.php');
                crearTablas($conn);
                $conn->close();
                header("Location: admin.php");
                exit();
            } else {
                $conn->close();
                header("Location: auth-login.php");
                $flagFile = __DIR__ . '/config/install-completed.txt';

                // Intentamos crear el archivo con contenido (puede estar vacío también)
                if (!file_exists($flagFile)) {
                    file_put_contents($flagFile, "Configuración completada el " . date('Y-m-d H:i:s'));
                }
                exit();
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    ?>

    <div class="container">
        <div class="logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#3498db" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
        </div>
        <h1>Configuración de la Base de Datos</h1>
        <p style="text-align: center; margin-bottom: 25px; color: #7f8c8d;">Ingresa los datos de conexión para iniciar la aplicación</p>

        <form method="POST">
            <div class="form-group">
                <?php if ($error): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <label for="host">Host:</label>
                <input type="text" id="host" name="host" value="localhost" required>
                <div class="help-text">Dirección del servidor de base de datos</div>
            </div>

            <div class="form-group">
                <label for="user">Usuario:</label>
                <input type="text" id="user" name="user" value="root" required>
                <div class="help-text">Nombre de usuario para acceder a la base de datos</div>
            </div>

            <div class="form-group">
                <label for="pass">Contraseña:</label>
                <input type="password" id="pass" name="pass">
                <div class="help-text">Contraseña del usuario de la base de datos</div>
            </div>

            <div class="form-group">
                <label for="db">Base de datos:</label>
                <input type="text" id="db" name="db" required>
                <div class="help-text">Nombre de la base de datos a utilizar</div>
            </div>

            <button type="submit">Guardar y continuar</button>
        </form>
    </div>
</body>

</html>