<?php

class Checkbox
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

//===============================================================================================================================================

    public function checkboc($table, $featureEnabled, $idName, $id){
        $sql = "UPDATE $table SET activation = $featureEnabled WHERE $idName = '$id'";
        $this->conn->query($sql);
    }

//===============================================================================================================================================
    
}