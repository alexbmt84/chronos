<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ChronoTâches !</title>

        <!-- Bootstrap CSS and icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
        <!-- CSS  -->
        <link rel="stylesheet" href="css/main.css">
    </head>
    <body>
        <header class="container-fluid bg-dark text-white text-end py-2">
            <div class="row justify-content-between">
                <div class="col-2">
                    <h6 class="text-start">ChronoTâches !</h6>
                </div>
                <div class="col-2">
                    <?php if (isset($_SESSION["user"])) : ?>
                        Bonjour <?= unserialize($_SESSION["user"])-> pseudo; ?> !
                        <i class="bi bi-grip-vertical"></i>
                        <a href="profile.php" class="text-white"><i class="bi bi-person-circle"></i></a>
                        <i class="bi bi-grip-vertical"></i>
                        <a href="logout.php" class="text-white"><i class="bi bi-box-arrow-right"></i></a>
                    <?php else: ?>
                        <a href="login.php" class="text-white"><i class="bi bi-box-arrow-in-right"></i></a>
                        <i class="bi bi-grip-vertical"></i>
                        <a href="register.php" class="text-white"><i class="bi bi-person-plus"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </header>