<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <!--barra de navegación-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <!-- marca -->
            <a class="navbar-brand" href="/">SWPRA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- vínculos -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- vínculos por defecto -->
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/">Inicio</a>
                    </li>
                    <!-- vínculos específicos -->
                    <?php if ( isset($_SESSION) && isset($_SESSION['usuarioComun']) ): ?>
                    <?php elseif ( isset($_SESSION) && isset($_SESSION['usuarioAdministrador']) ): ?>
                    <?php else: ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!--barra de notificaciones-->
    <div class="container container-fluid">
        <?php if ( isset($_SESSION) && isset($_SESSION['mensaje']) && isset($_SESSION['class']) ): ?>
            <div class="alert <?=$_SESSION['class'];?> alert-dismissible fade show" role="alert">
                <?=$_SESSION['mensaje'];?>
                <?php unset($_SESSION['mensaje']); ?>
                <?php unset($_SESSION['class']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>
    <!--contenedor principal-->
    <div class="container container-fluid">
        <!--sección del título-->
        <div class="container container-fluid mt-3 mb-4">
            <p class="h1 text-center">Inicio del sistema web</p>
        </div>
        <!--sección del subtitulo-->
        <div class="container container-fluid mb-5">
            <p class="h4 text-center"></p>
        </div>
        <!--cuerpo-->
        <div class="container container-fluid">
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>