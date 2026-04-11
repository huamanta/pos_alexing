<?php
    class Marca {
        public function __construct() {

        }

        public function select() {
            $sql = "SELECT * FROM marca WHERE estado = 1";
            return ejecutarConsulta($sql);
        }
    }