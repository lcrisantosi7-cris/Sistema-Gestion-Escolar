<?php

class Database{
    public $servidor;
    public $usuario;
    public $clave;
    public $basedatos;
    public $conexion;

    public function __construct()
    {
        $this->servidor = 'localhost';
        $this->usuario = 'root';
        $this->clave = '';
        $this->basedatos = 'proyectoweb';
    }

    public function conectar(){
        try{
            $dsn = "mysql:host=".$this->servidor.";dbname=".$this->basedatos.";port=3306";
            $this->conexion = new PDO($dsn,$this->usuario,$this->clave);
            $this->conexion->query('set names utf8');
            return true;
        } catch(PDOException $e){
            return false;
        }
    }
}

?>