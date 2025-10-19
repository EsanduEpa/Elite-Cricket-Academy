<?php
    class M_Pages {
        private $db;

        public function __construct() {
            $this->db = new Database();
        }

        public function getUsers(){
            $this->db->query("SELECT * FROM User");
            return $this->db->resultSet();
        }
    }
?>