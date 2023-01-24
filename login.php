<?php
/**
 * Page de connexion...
 */
    session_name("chronotaches");
    session_start();

    if (isset($_SESSION["user"])) {
        header("Location: ./");
        die();
    }

    // Autoloader pour ne plus avoir à "gérer manuellement" les classes ...
    require_once "core/autoloader.php";
    Autoloader::register();

    if (!empty($_POST)) {
        if (isset($_POST["email"]) && isset($_POST["password"]) && $_POST["email"] != "" && $_POST["password"] != "") {
            $utilisateur = Utilisateur::findByEmailAndPassword($_POST["email"], trim($_POST["password"]));

            if (isset($utilisateur) && $utilisateur-> id > 0) {
                $_SESSION["user"] = serialize($utilisateur);
                header("Location: ./");
            } else {
                $alert = new Alert("alert-warning");
                $alert-> title = "Désolé !";
                $alert-> body = "Veillez vérifier vos saisies !";
                $alert-> footer = "<a href='register.php' class='text-dark'>Aller à la page de création de compte</a>";
            }
        } else {
            $alert = new Alert("alert-warning");
            $alert-> title = "Désolé !";
            $alert-> body = "Veillez vérifier vos saisies !";
            $alert-> footer = "<a href='register.php' class='text-dark'>Aller à la page de création de compte</a>";
        }
    }

    include "core/header.php";
?>

<main class="container">
    <div class="row justify-content-center py-5">
        <div class="col-6">
            <h1 class="text-center">Timers PHP/MySQL</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-6 pt-2 pb-5">
            <h4>Connexion au service</h4>
        </div>
    </div>

    <?php if (isset($alert)): ?>
    <div class="row justify-content-center">
        <div class="col-6 pt-2 pb-1">
            <?= $alert; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-6">
            <form action="" method="post" class="row">
                <div class="col-12 form-group py-1">
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email *" value="<?= isset($_POST["email"]) ? $_POST["email"] : "";?>" required>
                </div>
                <div class="col-12 form-group py-1">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Mot de passe *" required>
                </div>
                <div class="col-12 form-group py-1">
                    <button class="btn btn-dark w-100"><i class="bi bi-box-arrow-in-right"></i> Je me lance !</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php
    include "core/footer.php";
?>