<?php
/**
 * Objets permettant de gérer et afficher les messages (erreur, succès, ...)
 * à destination de l'utilisateur
 */
    class Alert {  
        /**
         * 
         */
        private string $type;
        private string $title;
        private string $body;
        private string $footer;

        public function __construct(string $type="", string $title="", string $body="", string $footer="") {
            $this-> isset = true;
            $this-> type = $type;
            $this-> title = $title;
            $this-> body = $body;
            $this-> footer = $footer;
        }

        /**
         * Surcharge de la méthode magique __toString()
         * Permet d'afficher le message en utilisant la librairie Bootstrap (4 et 5)
         * @return string Bloc html formatant l'affichage du message.
         */
        public function __toString() {
            $str = "<div class='alert {$this-> type} alert-dismissible fade show' role='alert'>
                        <h4 class='alert-heading'>{$this-> title}</h4>
                        <p>{$this-> body}</p>";
            if ($this-> footer != "") {
                $str .= "<hr><p class='mb-0'>{$this-> footer}</p>";
            }

            $str .= "   <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";

            return $str;
        }

        public function __set($propriete, $valeur) {
            if ($propriete != "isset") {
                $this-> $propriete = $valeur;
            }
        }

        public function __get($propriete) {
            if ($propriete == "isset")
                return $this-> isset;
        }
    }