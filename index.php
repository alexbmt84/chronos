<?php
/**
 * Page principale 
 */
    session_name("chronotaches");
    session_start();

    // Si le visiteur n'est pas connecté, le renvoyer à la page de connexion...
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        die();
    }

    // Autoloader pour ne plus avoir à "gérer manuellement" les classes ...
    require_once "core/autoloader.php";
    Autoloader::register();

    // Charger les catégories de tâches depuis la BDD (perso, pro, ...)
    $lstCategories = CategorieTache::findAll();

    if (isset($_GET["categorie"])) {
        $categorie = $_GET["categorie"];
        $selectedCategory = CategorieTache::findByLabel($_GET["categorie"]);
    } 

    if (!isset($selectedCategory)) {
        // Au pire des cas, si pas de catégorie par défaut, se préparer à prendre la
        // 1ère catégorie comme catégorie par défaut...
        if (count($lstCategories) > 0) {
            $categorie = $lstCategories[0];
        }

        // Rechercher la catégorie par défaut, si elle est définie...
        foreach($lstCategories as $item) {
            if ($item-> defaut) {
                $selectedCategory = $item;
                break;
            }
        }

        $categorie = $selectedCategory-> label;
    }

    // traitement des formulaires
    if (!empty($_POST)) {
        // Création d'une nouvelle tâche
        if (isset($_POST["tache"]) && isset($_POST["categorie"]) && $_POST["tache"] != "") {
            $tache = new Tache();
            $tache-> nom = trim($_POST["tache"]);
            $tache-> categorie = filter_var($_POST["categorie"], FILTER_SANITIZE_NUMBER_INT);
            $tache-> utilisateur = unserialize($_SESSION["user"])-> id;

            $tache-> save();
        }

        // Action sur une des tâches
        if (isset($_POST["action"])) {
            switch ($_POST["action"]) {
                case "start":
                    $tache = Tache::findById($_POST["tache"]);
                    $tache-> loadAllTimers();
                    $tache-> start();
                    break;
                case "pause":
                    $tache = Tache::findById($_POST["tache"]);
                    $tache-> loadAllTimers();
                    $tache-> pause();
                    break;
                case "stop":
                    $tache = Tache::findById($_POST["tache"]);
                    $tache-> loadAllTimers();
                    $tache-> stop();
                    break;
                case "remove-timer":
                    $timer = Timer::findById($_POST["timer"]);
                    $timer-> delete();
                    break;
                case "delete":
                    $tache = Tache::findById($_POST["tache"]);
                    $tache-> loadAllTimers();
                    $tache-> delete();
                    break;
                default:
                    break;
            }
        }
    }

    include "core/header.php";
?>

<main class="container">
    <div class="row justify-content-center py-5">
        <div class="col-6">
            <h1 class="text-center">ChronoTâches PHP/MySQL</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12 ps-0">
            <!-- Menu à onglets des catégories de tâches -->
            <ul class="nav nav-tabs">
                <?php
                foreach ($lstCategories as $item) : ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $categorie==$item-> label ? "active":"" ?>" <?= $categorie==$item-> label ? "aria-current='categorie'":"" ?> href="./?categorie=<?= $item-> label; ?>">
                            <?= $item-> label ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="row justify-content-center py-5 border-start border-bottom">
        <div class="col-6">
            <section id="add-task-form">
                <form action="" method="post" class="row g-1">
                    <div class="col-12 col-lg-8">
                        <input type="hidden" name="categorie" value="<?= $selectedCategory-> id; ?>">
                        <input type="text" name="tache" placeholder="Nom de la nouvelle tâche" class="form-control" value="">
                    </div>
                    <div class="col-12 col-lg-4">
                        <button class="btn btn-dark w-100">Ajouter une tâche !</button>
                    </div>
                </form>
            </section>

            <!-- Conteneur de liste de tâches ...  -->
            <section id="tasks-wrapper">
                <?php
                    $selectedCategory-> loadAllTasks(unserialize($_SESSION["user"])-> id);

                    foreach ($selectedCategory-> taches as $item) :
                        $item-> loadAllTimers();
                ?>
                    <article class="tache py-3" id="tache-<?= $item-> id; ?>">
                        <div class="card w-100">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-11">
                                        <h5 class="card-title"><?= $item-> nom; ?></h5>
                                    </div>
                                    <div class="col-1">
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#sb-modal-<?= $item-> id; ?>"><i class="bi bi-trash3"></i></button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="sb-modal-<?= $item-> id; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="staticBackdropLabel">Suppression d'une tâche</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Êtes-vous sûr de vouloir supprimer la tâche <span class="text-nowrap">"<?= $item-> nom; ?>"<span> ?
                                                        <br>
                                                        Cette opération est définitive et entraînera<br>
                                                        la suppression de tous les Timers associés...
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                    <form action="" method="post">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="tache" value="<?= $item-> id; ?>">
                                                        <button type="submit" class="btn btn-primary">Confirmer</button>
                                                    </form>
                                                </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="card-subtitle mb-2">
                                    <h6 class="text-muted">Créée le <?= $item-> dateCreation-> format("d/m/Y à H:i:s"); ?> - 
                                    <?php 
                                        if ($item-> etat == Etat::ATTENTE) echo "<span class='text-info'>En attente</span>";
                                        if ($item-> etat == Etat::ACTIF) echo "<span class='text-success'>Active</span>";
                                        if ($item-> etat == Etat::PAUSE) echo "<span class='text-warning'>En pause</span>";
                                        if ($item-> etat == Etat::STOP) echo "<span class='text-danger'>Terminée</span>";
                                    ?>
                                    </h6>
                                    <div class="btns pb-3">
                                        <div class="row">
                                            <div class="col-1">
                                                <form action="./?categorie=<?= $categorie; ?>#tache-<?= $item-> id; ?>" method="post">
                                                    <input type="hidden" name="tache" value="<?= $item-> id ?>">
                                                    <input type="hidden" name="action" value="start">
                                                    <button class="btn btn-sm btn-dark <?= $item-> etat==Etat::ATTENTE||$item-> etat==Etat::PAUSE ? "":"disabled"; ?>"><i class="bi bi-play-btn"></i></button>
                                                </form>
                                            </div> 
                                            <div class="col-1">
                                                <form action="./?categorie=<?= $categorie; ?>#tache-<?= $item-> id; ?>" method="post">
                                                    <input type="hidden" name="tache" value="<?= $item-> id ?>">
                                                    <input type="hidden" name="action" value="pause">
                                                    <button class="btn btn-sm btn-dark <?= $item-> etat==Etat::ACTIF ? "":"disabled"; ?>"><i class="bi bi-pause-btn"></i></button>
                                                </form>
                                            </div>
                                            <div class="col-1">
                                                <form action="./?categorie=<?= $categorie; ?>#tache-<?= $item-> id; ?>" method="post">
                                                    <input type="hidden" name="tache" value="<?= $item-> id ?>">
                                                    <input type="hidden" name="action" value="stop">
                                                    <button class="btn btn-sm btn-dark <?= $item-> etat==Etat::STOP ? "disabled":""; ?>"><i class="bi bi-stop-btn"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                        // Calcul du temps total écoulé pour la tâche en état actif
                                        $ans = 0;
                                        $mois = 0;
                                        $jours = 0;
                                        $heures = 0;
                                        $minutes = 0;
                                        $secondes = 0;
                                        
                                        foreach ($item-> timers as $timer) {
                                            if ($timer-> end != null) {
                                                $temps = $timer-> start-> diff($timer-> end);

                                                $secondes += $temps-> s;
                                                if ($secondes > 59) {
                                                    $reste = $secondes % 60;
                                                    $minutes += (int) ($secondes / 60);
                                                    $secondes = $reste;
                                                }

                                                $minutes += $temps-> i;
                                                if ($minutes > 59) {
                                                    $reste = $minutes % 60;
                                                    $heures += (int) ($minutes / 60);
                                                    $minutes = $reste;
                                                }

                                                $heures += $temps-> h;
                                                if ($heures > 23) {
                                                    $reste = $heures % 24;
                                                    $jours += (int) ($heures / 24);
                                                    $heures = $reste;
                                                }

                                                $jours += $temps-> d;
                                                if ($jours > 30) {
                                                    $reste = $jours % 30;
                                                    $mois += (int) ($jours / 30);
                                                    $jours = $reste;
                                                }

                                                $mois += $temps-> m;
                                                if ($mois > 12) {
                                                    $reste = $mois % 12;
                                                    $ans += (int) ($mois / 12);
                                                    $mois = $reste;
                                                }

                                                $ans += $temps-> y;
                                            }
                                        }
                                    ?>
                                    <h6>Temps total écoulé: <?= $jours; ?> jours <?= $heures; ?> heures <?= $minutes; ?> minutes <?= $secondes; ?> secondes</h6>
                                    
                                </div>

                                <div class="card-text">
                                    <?php foreach ($item-> timers as $timer) : ?>
                                        <div class="row pb-3">
                                            <div class="col-11">
                                                <i class="bi bi-stopwatch"></i></span> Du <?= $timer-> start-> format("d/m/Y à H:m:s"); ?>
                                                <?php if ($timer-> end != null) : ?>
                                                    <i class="bi bi-arrow-right"></i> Au <?= $timer-> end-> format("d/m/Y à H:m:s"); ?>
                                                    <?php $temps = $timer-> start-> diff($timer-> end); ?>
                                                    <br>
                                                    <?php // Afficher la durée écoulée sur un timer ?>
                                                    <span class="h6 ps-5"><i class="bi bi-clock-history"></i>
                                                        <?php if ($temps-> y > 0): ?>
                                                        <?= $temps-> y; ?> ans
                                                        <?php endif; ?>
                                                        <?php if ($temps-> m > 0): ?>
                                                        <?= $temps-> m; ?> mois
                                                        <?php endif; ?>
                                                        <?= $temps-> d; ?> jours
                                                        <?= $temps-> h; ?> heures
                                                        <?= $temps-> i; ?> minutes
                                                        <?= $temps-> s; ?> secondes
                                                    </span>
                                                <?php else: ?>
                                                    <i class="bi bi-arrow-right"></i>  En cours...
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-1">
                                                <?php // Si la tâche n'est pas active, activer le bouton de suppression d'un timer ?>
                                                <button type="button" class="btn btn-sm btn-danger <?= $item-> etat == Etat::ACTIF ? "disabled":"" ?>" data-bs-toggle="modal" data-bs-target="#sb-modal-timer-<?= $timer-> id; ?>"><i class="bi bi-file-x"></i></button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="sb-modal-timer-<?= $timer-> id; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="staticBackdropLabel">Suppression d'un Timer</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Êtes-vous sûr de vouloir supprimer définitivement le Timer  ?</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                            <form action="" method="post">
                                                                <input type="hidden" name="action" value="remove-timer">
                                                                <input type="hidden" name="timer" value="<?= $timer-> id; ?>">
                                                                <button type="submit" class="btn btn-primary">Supprimer</button>
                                                            </form>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
            </section>
        </div>
    </div>
</main>

<?php
    include "core/footer.php";
?>