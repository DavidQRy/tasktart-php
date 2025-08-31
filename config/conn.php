<?php 
    class DataBase {
        public string $host = "localhost";
        public string $dbname = "tasktart";
        public string $username = "root";
        public string $password = "";

        private ?mysqli $conn = null;

        public function connect(){
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
            if ($this->conn->connect_error) {
                echo "Conection failed ". $this->conn->connect_error;
            }
            return $this->conn;
            
        }
        
    }
?>