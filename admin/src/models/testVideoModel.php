<?php

class TestVideoModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

//===============================================================================================================================================    

    public function testVideoPresenting() {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $id = $_GET['id'];
        $table1 = $_GET['table1'];
        $table2 = $_GET['table2'];
        $itemId = $_GET['itemId'];

        $itemsPerPage = 9; // Number of items to display per page
        $offset = ($page - 1) * $itemsPerPage;

        $sql = "SELECT t.*, CASE 
                        WHEN ta.$itemId IS NOT NULL THEN 1
                            ELSE 0
                        END AS isIn
                FROM $table1 t
                LEFT JOIN $table2 ta ON t.$itemId = ta.$itemId AND ta.batch_id = $id
                ORDER BY t.$itemId ASC
                LIMIT $offset, $itemsPerPage";
        $result = $this->conn->query($sql);
        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $totalItemsQuery = "SELECT COUNT(*) as total FROM $table1";
        $totalItemsResult = mysqli_query($this->conn, $totalItemsQuery);
        $totalItems = mysqli_fetch_assoc($totalItemsResult)['total'];


        $response = [
            'data' => $data,
            'totalItems' => $totalItems
        ];

        return $response;
    }

//===============================================================================================================================================

    public function removeTheAssigning($batchId, $testId, $student_id) {
        // Insert the employee data into the database (assuming you have an "employees" table)
        try{
            $sql = "DELETE FROM testass WHERE batch_id = $batchId AND test_id = $testId";
            $this->conn->query($sql);
        
            $sql1 = "DELETE FROM evaluation
                     WHERE student_id IN (
                        SELECT s.student_id
                        FROM assignstudent AS s
                        JOIN evaluation AS e ON s.student_id = e.student_id
                        WHERE s.batch_id = $batchId AND e.test_id = $testId)";
            $this->conn->query($sql1); 
            
            $sql3 = "DELETE FROM paidtest
                     WHERE student_id IN (
                        SELECT s.student_id
                        FROM assignstudent AS s
                        JOIN paidtest AS e ON s.student_id = e.student_id
                        WHERE s.batch_id = $batchId AND e.test_id = $testId)";

            $this->conn->query($sql3);

            $sql4 = "DELETE FROM answering
                     WHERE student_id IN (
                        SELECT s.student_id
                        FROM assignstudent AS s
                        JOIN answering AS e ON s.student_id = e.student_id
                        WHERE s.batch_id = $batchId AND e.test_id = $testId)";
            $this->conn->query($sql4);

            
            $response['message'] = "Deleted";
        }catch(Exception $e){
            $response['message'] = "Something went wrong";
        }
        
        
        return $response;
        
    }


//===============================================================================================================================================

public function testVideoAssigning($batchId, $test1Id, $isPresent, $test, $itemId, $item) {
    try{
        // Update the attendance for the student
        $sql = "INSERT INTO $test (batch_id, $itemId, assigned_on, ispresent) VALUES (?, ?, CURDATE(), ?) ON DUPLICATE KEY UPDATE ispresent = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('issi', $batchId, $test1Id, $isPresent, $isPresent);
        $stmt->execute();

        if ($stmt->error) {
            throw new Exception("Error adding test or video data");
        }else{
            $response['success'] = true;
            $response['message'] = "The inserted of $item of batches has been completed";
        }
        return $response;
    }catch( Exception $e){
        $response['success'] = false;
        $response['message'] = "Something went wrong";
    }
    
}


    

}
?>
