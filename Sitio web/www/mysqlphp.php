<?php

				$var1 = $_POST['var1'];
				$var2 = $_POST['var2'];



                
                require './mysqlData.php';

// Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                //$sql = "SELECT node, state FROM reserve_table WHERE plane = '$var2' AND (UNIX_TIMESTAMP('$var1') BETWEEN startdate AND enddate)";
				
				$sql = "SELECT node, state FROM reserve_table WHERE plane = '$var2' AND (UNIX_TIMESTAMP('$var1') >= startdate AND UNIX_TIMESTAMP('$var1') < enddate)";
                $result = $conn->query($sql);
                $return = array();

                if ($result->num_rows > 0) {
                    // output data of each row
                    while ($row = $result->fetch_assoc()) {
                        array_push($return, $row);
                    }
                } else {
                    //array_push($return, $sql);
                }
                echo json_encode($return);

                $conn->close();
?>