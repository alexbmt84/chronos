<?php
/**
 * Timer : chronomètre représentant une fraction temporelle d'une tâche
 */
    class Timer {
        private int $id;
        private DateTime $_start;
        private ?DateTime $_end;
        private int $tache;

        /**
         * Constructeur avec paramètres facultatifs
         */
        public function __construct($id=0, $start="", $end="", $tache=0) {
            $this-> id = $id;

            if ($start != "") {
                try {
                    $this-> start = new DateTime($start);
                } catch (Exception | Error $e) {
                    $this-> _start = new DateTime();
                }
            } else {
                $this-> _start = new DateTime();
            }

            if (isset($end) && $end != "") {
                try {
                    $this-> _end = new DateTime($end);
                } catch (Exception | Error $e) {
                    $this-> _end = null;
                }
            } else {
                $this-> _end = null;
            }

            $this-> tache = $tache;
        }

        /**
         * Accesseurs
         * Setter magique
         */
        public function __set($propriete, $valeur) {
            if ($propriete == "start") {
                $this-> _start = new DateTime($valeur);
            } else if ($propriete == "end") {
                if ($valeur != "") {
                    $this-> _end = new DateTime($valeur);
                }
            } else {
                $this-> $propriete = $valeur;
            }
        }

        /**
         * Accesseur
         * Getter magique
         */
        public function __get($propriete) {
            if ($propriete == "start") {
                return $this-> _start;
            } else if ($propriete == "end") {
                return $this-> _end;
            } else {
                return $this-> $propriete;
            }
        }

        /**
         * start()
         * Lancement d'une chrono (Timer) : création en base de données et état mis en ATTENTE
         * @return bool booléen marquant le succès ou l'échec de l'enregistrement de la tâche dans la base de données.
         */
        public function start(): bool {
            $sql = "INSERT INTO timers (tache) VALUES (:tache);";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                
                $query-> bindParam(":tache", $this-> tache, PDO::PARAM_INT);
    
                if ($query-> execute()) {
                    $this-> id = $db-> lastInsertId();
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
         * stop()
         * Arrêt du dernier chorno actif (Timer) : mise à jour en base de données en base de données et état mis en STOP
         * @return bool booléen marquant le succès ou l'échec de l'enregistrement de la tâche dans la base de données.
         */
        public function stop(): bool {
            $sql = "UPDATE timers SET end = CURRENT_TIMESTAMP WHERE id = :id;";

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

        /**
         * findById()
         * Récupération d'un timer depuis la base de données par son identifiant.
         * @param int $id Identifiant de la tâche à récupérer depuis la base de données.
         * @return Timer Chrono (Timer) dont l'identifiant est recherché.
         */
        public static function findById(int $id): ?Timer {
            $sql = "SELECT * FROM timers WHERE id = :id;";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                $query-> bindParam(":id", $id, PDO::PARAM_INT);
                
                if ($query-> execute()) {
                    $query-> setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Timer");
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
         * findById()
         * Récupérer les chronos (Timer) associés à une tâche, par l'identifiant de la tâche.
         * @param int $tache Identifiant de la tâche auquel sont rattachés les Timers.
         * @return array Tableau regroupant les timers associés à la tâche.
         */
        public static function findByTache(int $tache): array {
            $sql = "SELECT * FROM timers WHERE tache=:tache ORDER BY id ASC";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                $query-> bindParam(":tache", $tache, PDO::PARAM_INT);
                
                if ($query-> execute()) {
                    $query-> setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Timer");
                    $results = $query-> fetchAll();

                    return $results;
                } else {
                    return array();
                }
            } catch (Exception|Error $e) {
                //die($e-> getMessage());
                return array();
            }
        }

        /**
         * delete()
         * Supprimer la tâche "courante" de la base de données.
         * @return bool True en cas de succés, False en cas d'échec.
         */
        public function delete(): bool {
            $sql = "DELETE FROM timers WHERE id=:id";

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
    }