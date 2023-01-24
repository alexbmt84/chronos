<?php
/**
 * Représente les utilisateurs enregistrés du site
 */
    class Utilisateur {
        private int $id;
        private string $email;
        private string $hash;
        private string $pseudo;
        private DateTime $dateCreation;

        public function __construct(int $id=0, string $email="", string $hash="", string $pseudo="", string $dateCreation = "") {
            $this-> id = $id;
            $this-> email = $email;
            $this-> hash = $hash;
            $this-> pseudo = $pseudo;

            // Tenter de définir la date de création a partir du paramètre $dateCreation
            // En cas d'Exception levée, ou toute erreur, définir au DateTime courant (now).
            try {
                $this-> dateCreation = new DateTime($dateCreation);
            } catch (Exception|Error $e) {
                $this-> dateCreation = new DateTime();
            }
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

        public function save(): bool {
            if ($this-> id == 0) {
                // ID de l'utilisateur est à 0 donc nouvelle inscription...
                $sql = "INSERT INTO utilisateurs (email, hash, pseudo) VALUES (:email, :hash, :pseudo);";
            } else {
                // ID de l'utilisateur supérieur à 0 donc déjà en base de données: mise à jour...
                $sql = "UPDATE utilisateurs SET email=:email, hash=:hash, pseudo=:pseudo WHERE id=:id;";
            }

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                
                $query-> bindParam(":email", $this-> email, PDO::PARAM_STR);
                $query-> bindParam(":hash", $this-> hash, PDO::PARAM_STR);
                $query-> bindParam(":pseudo", $this-> pseudo, PDO::PARAM_STR);
    
                if ($this-> id != 0)
                    $query-> bindParam(":id", $this-> id, PDO::PARAM_INT);
                
                if ($query-> execute()) {
                    return true;
                } else {
                    //die("erreur");
                    return false;
                }
            } catch (Exception|Error $e) {
                //die($e-> getMessage());
                return false;
            }
        }

        /**
         * countByEmail()
         * Permet de compter les utilisateurs utilisant l'email passé en argument.
         * Utilisation principale: bloquer la création d'un nouveau compte si l'email existe déjà en base de données
         * @param string $email Email dont la présence est à vérifier en base de données.
         * @return int Nombre d'utilisateurs utilisant cet adresse email.
         */
        public static function countByEmail(string $email): int {
            $sql = "SELECT COUNT(id) as count FROM utilisateurs WHERE email LIKE :email;";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                
                $query-> bindParam(":email", $email, PDO::PARAM_STR);
                
                if ($query-> execute()) {
                    $results = $query-> fetch();

                    return $results[0];
                } else {
                    return -1;
                }
            } catch (Exception|Error $e) {
                //die($e-> getMessage());
                return -1;
            }
        }

        /**
         * findByEmailAndPassword()
         * Aussi nommé suivant les projets: login()
         * Permet de retrouver un utilisateur d'après son adresse email et son mot de passe en clair.
         * Utilisation principale: connexion à l'application.
         * @param string $email Adresse email de l'utilisateur à récupérer
         * @param string $password Mot de passe en clair de l'utilisateur à récupérer
         * @return ?Utilisateur retourne l'utilisateur retrouvé en cas de réussite, un utilisateur vide (dont l'id est à 0) ou null en cas d'échec (retourne ou Utilisateur ou null).
         */
        public static function findByEmailAndPassword(string $email, string $password): ?Utilisateur {
            $sql = "SELECT * FROM utilisateurs WHERE email LIKE :email;";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                
                $query-> bindParam(":email", $email, PDO::PARAM_STR);
                
                if ($query-> execute()) {
                    $query-> setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Utilisateur");
                    $result = $query-> fetch();

                    if ($result) {
                        if (password_verify($password, $result-> hash)) {
                            return $result;
                        } else {
                            //var_dump("bad password");
                            return new Utilisateur();
                        }
                    } else {
                        return new Utilisateur();
                    }
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
         * Récupére un utilisateur depuis la base de données par son identifiant
         * @param int $id Identifiant de l'utilisateur à récupérer
         * @return ?Utilisateur Utilisateur dont l'id correspond, ou utilisateur avec id à 0 ou retour null en cas d'échec
         */
        public static function findById(int $id): ?Utilisateur {
            $sql = "SELECT * FROM utilisateurs id = :id;";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                
                $query-> bindParam(":id", $id, PDO::PARAM_STR);
                
                if ($query-> execute()) {
                    $query-> setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Utilisateur");
                    $result = $query-> fetch();

                    if ($result) {
                        return $result;
                    } else {
                        return new Utilisateur();
                    }
                } else {
                    return null;
                }
            } catch (Exception|Error $e) {
                //die($e-> getMessage());
                return null;
            }
        }

        /**
         * search()
         * Récupère la liste des utilisateurs qui correspondent à la recherche.
         * La recherche par mot clé est faite sur l'email ou pseudo.
         * @param string $q mot clé de recherche.
         * @return array Tableau regroupant la liste des utilisateurs répondant au critère de recherche, si ils existent.
         */
        public static function search(string $q): array {
            $q = "%" . $q . "%";
            $sql = "SELECT * FROM utilisateurs WHERE email LIKE :q AND pseudo LIKE :q ORDER BY pseudo, email;";

            $dsn = "mysql:host=" . DB::HOST . "; port=" . DB::PORT . "; dbname=" . DB::DBNAME . "; charset=" . DB::CHARSET;
            
            try {
                $db = new PDO($dsn, DB::DBUSER, DB::DBPASS);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $query = $db-> prepare($sql);
                
                $query-> bindParam(":q", $q, PDO::PARAM_STR);
                
                if ($query-> execute()) {
                    $query-> setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Utilisateur");
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
    }