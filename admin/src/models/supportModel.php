<?php

class SupportModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

//===============================================================================================================================================    

    public function support() {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

        $itemsPerPage = 10; // Number of items to display per page
        $offset = ($page - 1) * $itemsPerPage;

        $sql = "SELECT *,
                        CASE
                            WHEN student_id IS NOT NULL THEN CONCAT('STU ', LPAD(CAST(student_id AS CHAR), 4, '0'))
                            ELSE CONCAT('EMP ', LPAD(CAST(employee_id AS CHAR), 4, '0'))
                        END AS person_id
                FROM ticket
                LIMIT $offset, $itemsPerPage";
        $result = $this->conn->query($sql);
        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $totalItemsQuery = "SELECT COUNT(*) as total FROM ticket";
        $totalItemsResult = mysqli_query($this->conn, $totalItemsQuery);
        $totalItems = mysqli_fetch_assoc($totalItemsResult)['total'];


        $response = [
            'data' => $data,
            'totalItems' => $totalItems
        ];

        return $response;
        $page=0;
    }
    
//===============================================================================================================================================

    public function ticketCheck($ticketId, $comment, $status, $rating) {
            
        $sql = "UPDATE ticket SET comments = '$comment', status = '$status', rating = '$rating'  WHERE ticket_no = $ticketId";
        if ($this->conn->query($sql) === TRUE) {
            $response['success'] = true;
            $response['message'] = "data updated successfully!";
        } else {
            $response['success'] = false;
            $response['message'] = "data updataion failed. Please try again.";
        }
        return $response;
        
    }
    

}
?>
