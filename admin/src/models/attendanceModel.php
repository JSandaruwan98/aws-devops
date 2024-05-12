<?php

class Attendance
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

//===============================================================================================================================================    

    public function viewAttendance(){
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $personId = $_GET['personId'];
        $table =$_GET['table'];
        $itemsPerPage = 10; // Number of items to display per page
        $offset = ($page - 1) * $itemsPerPage;

        $sql = "SELECT 
                    s.$personId,
                    s.name,
                    CASE 
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() AND a.leave_id IS NOT NULL THEN 1 ELSE NULL END) > 'absent' THEN 'leave'
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() AND a.$personId IS NOT NULL THEN 1 ELSE NULL END) > 'absent' THEN 'present'
                        ELSE 'absent'
                    END AS day1,
                    CASE 
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() - INTERVAL 1 DAY AND a.leave_id IS NOT NULL THEN 1 ELSE NULL END) > 0 THEN 'leave'
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() - INTERVAL 1 DAY AND a.$personId IS NOT NULL THEN 1 ELSE NULL END) > 0 THEN 'present'
                        ELSE 'absent'
                    END AS day2,
                    CASE 
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() - INTERVAL 2 DAY AND a.leave_id IS NOT NULL THEN 1 ELSE NULL END) > 0 THEN 'leave'
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() - INTERVAL 2 DAY AND a.$personId IS NOT NULL THEN 1 ELSE NULL END) > 0 THEN 'present'
                        ELSE 'absent'
                    END AS day3,
                    CASE 
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() - INTERVAL 3 DAY AND a.leave_id IS NOT NULL THEN 1 ELSE NULL END) > 0 THEN 'leave'
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() - INTERVAL 3 DAY AND a.$personId IS NOT NULL THEN 1 ELSE NULL END) > 0 THEN 'present'
                        ELSE 'absent'
                    END AS day4,
                    CASE 
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() - INTERVAL 4 DAY AND a.leave_id IS NOT NULL THEN 1 ELSE NULL END) > 0 THEN 'leave'
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() - INTERVAL 4 DAY AND a.$personId IS NOT NULL THEN 1 ELSE NULL END) > 0 THEN 'present'
                        ELSE 'absent'
                    END AS day5,
                    CASE 
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() - INTERVAL 5 DAY AND a.leave_id IS NOT NULL THEN 1 ELSE NULL END) > 0 THEN 'leave'
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() - INTERVAL 5 DAY AND a.$personId IS NOT NULL THEN 1 ELSE NULL END) > 0 THEN 'present'
                        ELSE 'absent'
                    END AS day6,
                    CASE 
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() - INTERVAL 6 DAY AND a.leave_id IS NOT NULL THEN 1 ELSE NULL END) > 0 THEN 'leave'
                        WHEN COUNT(CASE WHEN a.attendance_date = CURDATE() - INTERVAL 6 DAY AND a.$personId IS NOT NULL THEN 1 ELSE NULL END) > 0 THEN 'present'
                        ELSE 'absent'
                    END AS day7
                FROM
                    $table AS s
                LEFT JOIN
                    attendance AS a
                ON
                    s.$personId = a.$personId
                WHERE
	                s.activation = 1    
                GROUP BY 
                    s.$personId, s.name
                LIMIT 
                    $offset, $itemsPerPage";

        $result = $this->conn->query($sql);
        $data = array();
        $i=1;
        while ($row = $result->fetch_assoc()) {
            $row['serial_number'] = ($page - 1) * $itemsPerPage + $i;
            $data[] = $row;
            $i++;
        }

        $totalItemsQuery = "SELECT COUNT(*) as total 
                            FROM $table
                            WHERE activation = 1";
        $totalItemsResult = mysqli_query($this->conn, $totalItemsQuery);
        $totalItems = mysqli_fetch_assoc($totalItemsResult)['total'];


        $response = [
            'data' => $data,
            'totalItems' => $totalItems
        ];

        return $response;
    }

//===============================================================================================================================================    

    public function viewMarkAttendance() {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $table = $_GET['table'];
        $personId = $_GET['personId'];
        $date = $_GET['date'];
        $itemsPerPage = 10; // Number of items to display per page
        $offset = ($page - 1) * $itemsPerPage;

        $sql = "SELECT s.$personId, s.name, a.attendance_id,
                    CASE WHEN a.$personId IS NOT NULL THEN 1 ELSE 0 END AS present
                FROM $table AS s
                LEFT JOIN attendance AS a ON s.$personId = a.$personId AND a.attendance_date = '$date'
                WHERE s.activation = 1 
                LIMIT $offset, $itemsPerPage";
        $result = $this->conn->query($sql);
        $data = array();
        $i=1;
        while ($row = $result->fetch_assoc()) {
            $row['serial_number'] = ($page - 1) * $itemsPerPage + $i;
            $data[] = $row;
            $i++;
        }

        $totalItemsQuery = "SELECT COUNT(*) AS total
                            FROM $table AS s
                            LEFT JOIN attendance AS a ON s.$personId = a.$personId AND a.attendance_date = '$date'";
        $totalItemsResult = mysqli_query($this->conn, $totalItemsQuery);
        $totalItems = mysqli_fetch_assoc($totalItemsResult)['total'];


        $response = [
            'data' => $data,
            'totalItems' => $totalItems
        ];

        return $response;
    }

//===============================================================================================================================================

    public function markAttendance($attendanceDate, $personId, $personIdName) {

        $sql = "INSERT IGNORE INTO attendance ($personIdName, attendance_date) 
                    VALUES ('$personId', '$attendanceDate')";

            if ($this->conn->query($sql) === TRUE) {
                $response['success'] = true;
                $response['message'] = "Batch created successfully!";
            } else {
                $response['success'] = false;
                $response['message'] = "Batch creation failed. Please try again.";
            }
        
        
        return $response;
    }

//===============================================================================================================================================

    public function removeAttendance($attendanceId) {
        // Insert the employee data into the database (assuming you have an "employees" table)
        $sql = "DELETE FROM attendance WHERE attendance_id = $attendanceId";

        if ($this->conn->query($sql) === TRUE) {
            $response['success'] = true;
            $response['message'] = "Employee '$attendanceId' created successfully!";
        } else {
            $response['success'] = false;
            $response['message'] = "Employee creation failed. Please try again.";
        }
        
    }

//===============================================================================================================================================
    
}