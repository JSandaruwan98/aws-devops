<?php

class BatchModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

//===============================================================================================================================================    

    public function getBatch()
    {
        $sql = "SELECT *,CONCAT(DATE_FORMAT(time_from, '%h:%i %p'), ' - ' , DATE_FORMAT(time_to, '%h:%i %p')) AS duration FROM batch WHERE activation = 1";
        $result = $this->conn->query($sql);
        $data = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

//===============================================================================================================================================

    public function viewBatch() {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

        $itemsPerPage = 10; // Number of items to display per page
        $offset = ($page - 1) * $itemsPerPage;

        $sql = "SELECT *,CONCAT(DATE_FORMAT(time_from, '%h:%i %p'), ' - ' , DATE_FORMAT(time_to, '%h:%i %p')) AS duration FROM batch LIMIT $offset, $itemsPerPage";
        $result = $this->conn->query($sql);
        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $totalItemsQuery = "SELECT COUNT(*) as total FROM batch";
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

    public function insertBatch($program, $class, $batchname, $timefrom, $timeto) {
        $response = array();

        //checked the batch name exist or not
        function batchnameExists($batchname_to_check, $conn) {
            $name_to_check = mysqli_real_escape_string($conn, $batchname_to_check);
            $sql = "SELECT * FROM batch WHERE name='$name_to_check'";
            $result = mysqli_query($conn, $sql);
            return mysqli_num_rows($result) > 0;
        }

        $batchname_to_check = $batchname;

        // Perform data validation
        if (empty($program) || empty($class) || empty($batchname) || empty($timefrom) || empty($timeto)) {
            $response['success'] = false;
            $response['message'] = "All fields are required.";
        } elseif(batchnameExists($batchname_to_check, $this->conn)){
            $response['success'] = false;
            $response['message'] = "Batch name already exists";
        } else {
            // Data is valid, proceed with database insertion

            // Insert the batch data into the database (assuming you have a "batch" table)
            $sql = "INSERT INTO batch (name, program, class_id, time_from, time_to) 
                    VALUES ('$batchname', '$program', '$class', '$timefrom', '$timeto')";

            $sql1 = "INSERT INTO notification (type, message) 
                    VALUES ('Added a new Batch', 'Admin Added a $batchname')";        

            $this->conn->query($sql1);

            if ($this->conn->query($sql) === TRUE) {
                $response['success'] = true;
                $response['message'] = "Batch '$batchname' created successfully!";
            } else {
                $response['success'] = false;
                $response['message'] = "Batch creation failed. Please try again.";
            }
        }

        return $response;
    }

}
?>
