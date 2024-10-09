<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Municipalidad</title>
    <!-- Enlace a la hoja de estilos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: linear-gradient(45deg, #66d5bb, #44add1);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        header {
            background-color: #f3f3f3;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo img {
            height: 50px;
        }

        nav {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .login-btn {
            background-color: #0451b8;
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .login-btn:hover {
            background-color: #4db6ac;
        }

        .calendar {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 250px;
            margin: 20px auto;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .calendar h2 {
            margin: 0;
            font-size: 1.8em;
            color: #000; /* Color negro para el título del calendario */
            font-weight: bold;
        }

        .calendar p {
            margin: 10px 0;
            color: #000;
            font-size: 1.2em;
            font-weight: 500;
        }

        .calendar #date {
            font-weight: bold;
            color: #000;
        }

        .calendar #time {
            font-size: 1.1em;
            color: #000; /* Negro para la hora */
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
        }

        .calendar .icon {
            font-size: 3em;
            color: #000; /* Negro para el icono */
            margin-bottom: 10px;
        }

        .calendar .time-icon {
            font-size: 1.2em;
            color: #000; /* Negro para el icono de la hora */
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        footer {
            background-color: #d0fff4;
            padding: 20px;
            text-align: center;
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.1);
     
        }

        footer p {
            margin: 0;
            color:  #0451b8 ;
        }

        .footer-links {
            margin: 10px 0;
        }

        .footer-links a {
            color:  #0451b8  ;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }

        .footer-links a i {
            margin-right: 5px;
        }

        .footer-contacts {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 10px;
            gap: 20px;
            background-color:  #44add1 ;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .footer-contacts div {
            background-color: #4fc3f7;
            padding: 10px 15px;
            border-radius: 8px;
            color: white;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .footer-contacts div:hover {
            background-color:  #44add1 ;
        }

        .footer-contacts div span {
            display: block;
            font-size: 1.2em;
        }
    </style>
    <script>
        function updateCalendar() {
            const date = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = date.toLocaleDateString('es-ES', options);
            const time = date.toLocaleTimeString('es-ES');

            document.getElementById('date').innerText = formattedDate;
            document.getElementById('time-text').innerText = time;
        }

        setInterval(updateCalendar, 1000); // Actualiza la hora cada segundo
    </script>
</head>

<body onload="updateCalendar()">
    <header>
        <div class="logo">
            <img src="https://www.montecristo.gov.ar/imagenes/estructura/img_logo_mc.png" alt="logo de la muni">
        </div>
        <nav>
            <a href="login.php" class="login-btn">Iniciar Sesión</a>
        </nav>
    </header>
    <main>
        <div class="calendar">
            <i class="fas fa-calendar-alt icon"></i> <!-- Icono de calendario en negro -->
            <h2>HOY</h2>
            <p id="date"></p>
            <p id="time"><i class="fas fa-clock time-icon"></i> <span id="time-text"></span></p>
        </div>
    </main>
    <footer>
        <div class="footer-links">
            <a href="https://www.facebook.com/MunicipalidaddeMonteCristo?mibextid=ZbWKwL"><i class="fab fa-facebook-f"></i>Facebook</a>
            <a href="https://www.instagram.com/municipalidad.montecristo?igsh=MTJucGV2bW43NWY4"><i class="fab fa-instagram"></i>Instagram</a>
        </div>
        <p>&copy; 2024 Municipalidad de Monte Cristo. Todos los derechos reservados.</p>
    </footer>
</body>

</html>
