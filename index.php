<?php
session_start();
if (!empty($_SESSION['active'])) {
    header('location: src/');
} else {
    if (!empty($_POST)) {
        $alert = '';
        if (empty($_POST['usuario']) || empty($_POST['clave'])) {
            $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Ingrese usuario y contraseña
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } else {
            require_once "conexion.php";
            $user = mysqli_real_escape_string($conexion, $_POST['usuario']);
            $clave = md5(mysqli_real_escape_string($conexion, $_POST['clave']));
            $query = mysqli_query($conexion, "SELECT * FROM usuario WHERE usuario = '$user' AND clave = '$clave'");
            mysqli_close($conexion);
            $resultado = mysqli_num_rows($query);
            if ($resultado > 0) {
                $dato = mysqli_fetch_array($query);
                $_SESSION['active'] = true;
                $_SESSION['idUser'] = $dato['idusuario'];
                $_SESSION['nombre'] = $dato['nombre'];
                $_SESSION['user'] = $dato['usuario'];
                header('Location: src/');
            } else {
                $alert = '<div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                <strong>Error:</strong> Contraseña incorrecta.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
                session_destroy();
            }
        }
    }
}
?>
<body>
<!DOCTYPE html>
<html lang="es-AR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
        }

        .brand-section {
            flex: 1;
            background-color: rgba(0, 123, 255, 0.8);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .brand-section h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            text-align: center;
        }

        .brand-section img {
            max-width: 100px;
            margin-bottom: 20px;
        }

        .login-form-section {
            flex: 1;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
        }

        .login-form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/img/images.jpg') no-repeat center center;
            background-size: cover;
            z-index: -1;
            opacity: 0.6;
        }

        .login-form {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-form h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #007bff;
            font-weight: bold;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-form .form-control {
            border-radius: 20px;
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            transition: all 0.3s ease-in-out;
        }

        .login-form .form-control:focus {
            border-color: #007bff;
            box-shadow: 0px 0px 8px rgba(0, 123, 255, 0.6);
            outline: none;
        }

        .login-form button {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px;
            font-size: 18px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-form button:hover {
            background-color: #0056b3;
        }

        .login-form .alert {
            margin-top: flex;
        }
    </style>
</head>
<body>
    <div class="brand-section">
        <img src="assets/img/logo1.jpg" alt="Logo Empresa">
        <h1>Pintureria</h1>
        <p>Mundo Color</p>
    </div>
    <div class="login-form-section">
        <form action="" method="POST" class="login-form">
        <?php echo (isset($alert)) ? $alert : ''; ?>
            <h2>Iniciar Sesión</h2>
            <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Usuario" required>
            <input type="password" name="clave" id="clave" class="form-control" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>   
        </form>
       
    </div>
    
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
            