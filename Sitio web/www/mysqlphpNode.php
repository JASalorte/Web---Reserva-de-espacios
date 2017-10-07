<?php

				$var1 = $_POST['var1'];
				$var2 = $_POST['var2'];
				$var3 = $_POST['var3'];

                
                require './mysqlData.php';

// Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
				
				$var4 = strtotime('+1 day', strtotime($var1));

                $sql = "SELECT state, startdate, enddate FROM reserve_table WHERE plane = '$var2' AND node = $var3 AND (startdate BETWEEN UNIX_TIMESTAMP('$var1') AND $var4) AND (enddate BETWEEN UNIX_TIMESTAMP('$var1') AND $var4);";
                $result = $conn->query($sql);
                $return = array();
//array_push($return, $sql);
                if ($result->num_rows > 0) {
                    // output data of each row
                    while ($row = $result->fetch_assoc()) {
                        array_push($return, $row);
                    }
                }
				
               echo json_encode($return);
			   //echo $return;

                $conn->close();
?>