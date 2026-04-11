<?php
    class Modelo {
        public function __construct() {

        }

        public function select() {
            $sql = "SELECT * FROM modelo WHERE estado = 1";
            return ejecutarConsulta($sql);
        }
    }