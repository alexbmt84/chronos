<?php
    /**
     * Représente une tâche, qui regroupe plusieurs "Timers"...
     */
    class Tache {
        private int $id;
        private string $nom;
        private int $etat;
        private DateTime $dateCreation;
        private array $timers;
        private int $categorie;
        private int $utilisateur;

        public function __construct($id=0, $nom="", $dateCreation="") {
            $this-> id = $id;
            $this-> nom = $nom;
            $this-> etat = Etat::ATTENTE;
            
            try {
                $this-> dateCreation = new DateTime($dateCreation);
            } catch (Exception|Error $e) {
                $this-> dateCreation = new DateTime();
            }

            $this-> timers = array();
        }

        public function __set($propriete, $valeur) {
            if ($propriete == "date_creation") 
                $this-> dateCreation = new DateTime($valeur);
            else
                $this-> $propriete = $valeur;
        }

        public function __get($propriete) {
            if ($propriete == "date_creation")
                return $this-> dateCreation-> format("Y-m-d");
            else
                return $this-> $propriete;
        }

        /**
         * start()
         * Lancement d'une tâche : création en base de données et état mis en ACTIF
         * Avec lancement d'un chrono (Timer)
         * @return bool booléen marquant le succès ou l'échec de l'enregistrement de la tâche dans la base de données.
         */
        public function start(): bool {
            $this-> etat = Etat::ACTIF;
            if ($this-> update()) {
                $timer = new Timer();
                $timer-> tache = $this-> id;

                if ($timer-> start()) {
                    $this-> timers[] = $timer;

                    return true;
                }
            }

            return false;
        }

        /**
         * pause()
         * Arrêt du dernier chorno actif (Timer) : mise à jour en base de données en base de données et état mis en PAUSE
         * @return bool booléen marquant le succès ou l'échec de l'enregistrement de la tâche dans la base de données.
         */
        public function pause(): bool {
            $this-> etat = Etat::PAUSE;

            if ($this-> update()) {
                $this-> timers[count($this-> timers)-1]-> stop();
                
                return true;
            } else {
                return false;
            }
        }

        /**
         * stop()
         * Arrêt du dernier chorno actif (Timer) : mise à jour en base de données en base de données et état mis en STOP
         * @return bool booléen marquant le succès ou l'échec de l'enregistrement de la tâche dans la base de données.
         */
        public function stop(): bool {
            $this-> etat = Etat::STOP;

            if ($this-> update()) {
                if (count($this-> timers) > 0) {
                    if ($this-> etat == Etat::ACTIF) {
                        $this-> timers[count($this-> timers)-1]-> stop();
                    }
                }

                return true;
            } else  return false;
        }

        /**
         * save()
         * Sauvegarde en base de données une nouvelle tâche avec etat mis à ATTENTE
         * @return bool True en cas de succès - False en cas d'échec
         */
        public function save(): bool {
            $sql = "INSERT INTO taches (nom, categorie, utilisateur) VALUES (:nom, :categorie, :utilisateur);";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                
                $query-> bindParam(":nom", $this-> nom, PDO::PARAM_STR);
                $query-> bindParam(":categorie", $this-> categorie, PDO::PARAM_INT);
                $query-> bindParam(":utilisateur", $this-> utilisateur, PDO::PARAM_INT);
    
                if ($query-> execute()) {
                    return true;
                } else {
                    return false;
                }
            } catch (Exception|Error $e) {
                die($e-> getMessage());
                return false;
            }
        }

        /**
         * save()
         * Met à jour une tâche en base de données.
         * @return bool True en cas de succès - False en cas d'échec
         */
        public function update(): bool {
            $sql = "UPDATE taches SET nom=:nom, etat=:etat, categorie=:categorie WHERE id=:id;";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                
                $query-> bindParam(":nom", $this-> nom, PDO::PARAM_STR);
                $query-> bindParam(":etat", $this-> etat, PDO::PARAM_INT);
                $query-> bindParam(":categorie", $this-> categorie, PDO::PARAM_INT);
                $query-> bindParam(":id", $this-> id, PDO::PARAM_INT);
    
                if ($query-> execute()) {
                    return true;
                } else {
                    return false;
                }
            } catch (Exception|Error $e) {
                //die($e-> getMessage());
                return false;
            }
        }

        /**
         * static findAll()
         * Trouve toutes les tâches stockées en base de données.
         * @return [Tache] tableau contenant toutes les tâches ou tableau vide.
         */
        public static function findAll(): ?array {
            $sql = "SELECT * FROM taches ORDER BY id ASC";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                
                if ($query-> execute()) {
                    $query-> setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Tache");
                    $results = $query-> fetchAll();

                    return $results;
                } else {
                    return null;
                }
            } catch (Exception|Error $e) {
                //die($e-> getMessage());
                return null;
            }
        }

        /**
         * static findById()
         * @param int $id l'identifiant de la tâche à récupérer depuis la base de données.
         * @return Tache La tâche dont l'identifiant est fourni en argument, tâche vide en cas d'échec (id vaut 0).
         */
        public static function findById(int $id): Tache {
            $sql = "SELECT * FROM taches WHERE id = :id;";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                $query-> bindParam(":id", $id, PDO::PARAM_INT);
                
                if ($query-> execute()) {
                    $query-> setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Tache");
                    $result = $query-> fetch();
                    
                    return $result;
                } else {
                    return null;
                }
            } catch (Exception|Error $e) {
                //die($e-> getMessage());
                return null;
            }
        }

        /**
         * static findByCategory()
         * @param int $categorie l'identifiant de la catégorie des tâches à récupérer depuis la base de données.
         * @return [Tache] La liste des tâches dont l'identifiant de la catégorie est fourni en argument, tableau vide en cas d'échec (id vaut 0).
         */
        public static function findByCategory(int $categorie, int $utilisateur): ?array {
            $sql = "SELECT * FROM taches WHERE categorie=:categorie AND utilisateur=:utilisateur ORDER BY nom ASC";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                $query-> bindParam(":categorie", $categorie, PDO::PARAM_INT);
                $query-> bindParam(":utilisateur", $utilisateur, PDO::PARAM_INT);
                
                if ($query-> execute()) {
                    $query-> setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Tache");
                    $results = $query-> fetchAll();

                    return $results;
                } else {
                    return null;
                }
            } catch (Exception|Error $e) {
                //die($e-> getMessage());
                return null;
            }
        }

        /**
         * loadAllTimers()
         * Charge tous les timers associés à une tâche dans sa attribut (tableau) timers.
         */
        public function loadAllTimers(): void {
            // Charger toutes les tâches d'une catégorie
            $this-> timers = Timer::findByTache($this-> id);  
        }

        /**
         * delete()
         * Supprime la tâche courante de la base de données après avoir supprimé tous ses timers associés.
         * @return bool True en cas de succès, False en cas d'échec.
         */
        public function delete(): bool {
            $continuer = true;

            // supprimer tous les Timers de la tâche
            foreach ($this-> timers as $item) {
                if (!$item-> delete())
                    $continuer = false;
            }

            // Si succès de suppression des Timers associés:
            // supprimer la tâche
            if ($continuer) {
                $sql = "DELETE FROM taches WHERE id=:id";

                $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
                
                try {
                    $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                    $query = $db-> prepare($sql);
                    
                    $query-> bindParam(":id", $this-> id, PDO::PARAM_INT);
        
                    if ($query-> execute()) {
                        return true;
                    } else {
                        return false;
                    }
                } catch (Exception|Error $e) {
                    //die($e-> getMessage());
                    return false;
                }
            }

            return false;
        }
    }