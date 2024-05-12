<?php

class TransactionModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

//===============================================================================================================================================    

    public function transaction() {

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

        $itemsPerPage = 10; // Number of items to display per page
        $offset = ($page - 1) * $itemsPerPage;

        $sql = "SELECT * FROM transaction LIMIT $offset, $itemsPerPage";
        $result = $this->conn->query($sql);
        $data = array();
        $i=1;
        while ($row = $result->fetch_assoc()) {
            $row['serial_number'] = ($page - 1) * $itemsPerPage + $i;
            $data[] = $row;
            $i++;
        }

        $totalItemsQuery = "SELECT COUNT(*) as total FROM transaction";
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

    public function balance() {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $itemsPerPage = 10;
        $offset = ($page - 1) * $itemsPerPage;
        $sql = "SELECT
                    total_credits - total_other_transactions AS balance
                FROM
                    (SELECT
                        SUM(CASE WHEN transactiontype = 'credit' THEN amount ELSE 0 END) AS total_credits,
                        SUM(CASE WHEN transactiontype <> 'credit' THEN amount ELSE 0 END) AS total_other_transactions
                    FROM
                        transaction) AS subquery
                LIMIT $offset, $itemsPerPage";        ;

        $result = $this->conn->query($sql);
        $data = array();
        $i=1;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['serial_number'] = ($page - 1) * $itemsPerPage + $i;
                $data[] = $row;
                $i++;
            }
        }

        return $data;
    }
    

}
?>
