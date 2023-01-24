<?php
/**
 * Page de création de compte
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
        if (isset($_POST["pseudo"]) && $_POST["pseudo"] != "") {
            if (isset($_POST["email"]) && $_POST["email"] != "") {
                if (isset($_POST["password"]) && $_POST["password"] != "" && strlen($_POST["password"])>7) {
                    if (isset($_POST["password2"]) && $_POST["password"] == $_POST["password2"]) {
                        if (Utilisateur::countByEmail($_POST["email"]) == 0) {
                            $utilisateur = new Utilisateur();
                            
                            $utilisateur-> pseudo = htmlspecialchars($_POST["pseudo"], ENT_QUOTES, 'UTF-8');
                            $utilisateur-> email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
                            $utilisateur-> hash = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT, ["cost"=> 12]);
    
                            if ($utilisateur-> save()) {
                                $alert = new Alert("alert-success");
                                $alert-> title = "Bravo !";
                                $alert-> body = "Votre compte est créé et vous pouvez maintenant vous connecter !";
                                $alert-> footer = "<a href='login.php' class='text-dark'>Aller à la page de connexion</a>";
                            } else {
                                $alert = new Alert("alert-danger");
                                $alert-> title = "Désolé !";
                                $alert-> body = "Le compte n'a pu être créé !";
                            }
                        } else {
                            $alert = new Alert("alert-warning");
                            $alert-> title = "Attention !";
                            $alert-> body = "Un compte avec cette adresse email existe déjà !<br>Ou une erreur s'est produite ?";
                            $alert-> footer = "<a href='login.php' class='text-dark'>Aller à la page de connexion</a>";
                        }
                    } else {
                        $alert = new Alert("alert-warning");
                        $alert-> title = "Attention !";
                        $alert-> body = "Les mots de passes saisis ne se correspondent pas !";
                    }
                } else {
                    $alert = new Alert("alert-warning");
                    $alert-> title = "Attention !";
                    $alert-> body = "Veillez à saisir un mot de passe valide !";
                    $alert-> footer = "Avec plus de 7 caractères, par exemple ?";
                }
            } else {
                $alert = new Alert("alert-warning");
                $alert-> title = "Attention !";
                $alert-> body = "Veillez à saisir une adresse email !";
            }
        } else {
            $alert = new Alert("alert-warning");
            $alert-> title = "Attention !";
            $alert-> body = "Veillez à saisir un pseudo !";
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
            <h4>Inscription au service</h4>
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
                    <input type="text" name="pseudo" id="pseudo" class="form-control" placeholder="Pseudo *" value="<?= isset($_POST["pseudo"]) ? $_POST["pseudo"] : "";?>" required>
                </div>
                <div class="col-12 form-group py-1">
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email *" value="<?= isset($_POST["email"]) ? $_POST["email"] : "";?>" required>
                </div>
                <div class="col-12 form-group py-1">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Mot de passe *" required>
                </div>
                <div class="col-12 form-group py-1">
                    <input type="password" name="password2" id="password2" class="form-control" placeholder="Confirmation du mot de passe *" required>
                </div>
                <div class="col-12 form-group py-1">
                    <button class="btn btn-dark w-100"><i class="bi bi-person-plus"></i> Je me lance !</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php
    include "core/footer.php";
?>