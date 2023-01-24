<?php
/**
 * Catégories de tâches:
 * Exemple:
 *    - Perso
 *    - Pro
 *    - ...
 */
    class CategorieTache {
        private int $id;
        private string $label;
        private array $taches;
        private bool $defaut;

        /**
         * Constructeur avec des paramètres facultatifs
         */
        public function __construct($id=0, $label="", $taches=null, $defaut=false) {
            $this-> id = $id;
            $this-> label = $label;
            if (isset($taches))
                $this-> taches = $taches;
            else
                $this-> taches = array();
            $this-> defaut = $defaut;
        }

        /** Accesseurs
         * Setter magique
         */
        public function __set($propriete, $valeur) {
            $this-> $propriete = $valeur;
        }

        /** Accesseur
         * Getter magique
         */
        public function __get($propriete) {
            return $this-> $propriete;
        }

        /**
         * Récupérer toutes les catégories de tâches de la base de données.
         * Par exemple, pour permettre à l'utilisateur de sélectionner une des catégories
         * afin de gérer les tâches associées
         */
        public static function findAll(): ?array {
            $sql = "SELECT * FROM categories_taches ORDER BY label ASC;";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                
                if ($query-> execute()) {
                    $query-> setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "CategorieTache");
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
         * Récupérer une catégorie de tâches par son identifiant unique
         */
        public static function findById(int $id): ?CategorieTache {
            $sql = "SELECT * FROM categories_taches WHERE id=:id;";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                $query-> bindParam(":id", $id, PDO::PARAM_INT);
                
                if ($query-> execute()) {
                    $query-> setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "CategorieTache");
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
         * Récupérer une catégorie de tâche par son nom (label)
         */
        public static function findByLabel(string $label): ?CategorieTache {
            $sql = "SELECT * FROM categories_taches WHERE label=:label;";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                $query-> bindParam(":label", $label, PDO::PARAM_STR);
                
                if ($query-> execute()) {
                    $query-> setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "CategorieTache");
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
         * Récupérer la catégorie par défaut
         * (A afficher par défaut dans la page d'accueil)
         */
        public static function findDefault(): ?CategorieTache {
            $sql = "SELECT * FROM categories_taches WHERE defaut=1;";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                
                if ($query-> execute()) {
                    $query-> setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "CategorieTache");
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
         * Charger toutes les tâches d'une catégorie
         * Repose sur la classe Tache et sa méthode static findByCategorie()
         */
        public function loadAllTasks(int $utilisateur): void {
            // Charger toutes les tâches d'une catégorie
            $this-> taches = Tache::findByCategory($this-> id, $utilisateur);
        }
    }