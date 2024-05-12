<?php

class TestModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    
//===============================================================================================================================================    

    public function completeTest($category, $student_id){

        $sql = "SELECT e.test_id, t.name, t.test_id, e.attempted_on, t.image_file, e.evaluated FROM paidtest AS e, test AS t WHERE t.test_id = e.test_id AND e.student_id = '$student_id' AND e.attempted = 1 AND t.category = '" . $category . "'";
        
        $result = $this->conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;

    }

//===============================================================================================================================================

    /**public function pendingTest($category, $student_id){

        try{
            $sql = "SELECT 
                        t.test_id, 
                        t.name, 
                        t.image_file,
                        CASE 
                            WHEN(t.test_id IN (SELECT test_id From paidtest)) THEN 1
                            ELSE 0
                        END AS paid    
                    FROM test AS t, testass AS tass 
                    WHERE 
                        tass.batch_id = (SELECT batch_id FROM assignstudent AS ass WHERE ass.student_id = $student_id) AND 
                        t.test_id = tass.test_id AND 
                        tass.test_id NOT IN (SELECT test_id FROM paidtest WHERE attempted = 1) AND 
                        t.category = '" . $category . "'";
            

            $result = $this->conn->query($sql);
            if (!$result) {
                throw new Exception("Query execution failed: " . $this->conn->error);
            }

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }    
            
            return $data;


        } catch(Exception $e){
            error_log("Exception occurred: " . $e->getMessage());
            return $sql;
        }

    }**/
    

    public function pendingTest($category, $student_id){

        try{
            $sql = "SELECT 
                        t.test_id, 
                        t.name, 
                        t.image_file,
                        CASE 
                            WHEN(t.test_id IN (SELECT test_id From paidtest WHERE attempted = 2 AND student_id = '$student_id')) THEN 1
                            ELSE 0
                        END AS paid    
                    FROM test AS t, testass AS tass 
                    WHERE 
                        tass.batch_id = (SELECT batch_id FROM assignstudent AS ass WHERE ass.student_id = '$student_id') AND 
                        t.test_id = tass.test_id AND 
                        tass.test_id NOT IN (SELECT test_id FROM paidtest WHERE attempted = 1 AND student_id = '$student_id') AND 
                        t.category = '" . $category . "'";
            

            $result = $this->conn->query($sql);
            if (!$result) {
                throw new Exception("Query execution failed: " . $this->conn->error);
            }

            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }    
            
            return $data;


        } catch(Exception $e){
            error_log("Exception occurred: " . $e->getMessage());
            return $sql;
        }

    }

    public function checked_completed($test_id, $student_id){
        $sql = 'SELECT 
        CASE 
            WHEN (SELECT COUNT(*) FROM paidtest AS pt WHERE pt.test_id = '.$test_id.' AND pt.student_id = "'.$student_id.'")
            THEN 
                CASE 
                    WHEN (SELECT COUNT(*) FROM paidtest WHERE attempted = 2 AND test_id = '.$test_id.' AND student_id = "'.$student_id.'")
                    THEN 2
                    ELSE 1
                    END
            ELSE 0
            END AS complete';

        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['complete'];
    }
    

}
?>
