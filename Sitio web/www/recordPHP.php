<?php

require './phpdata.php';
require './mysqlData.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//$var1 = "meh"
$var1 = $_POST['var1'];
$var2 = $_POST['var2'];

//if(date("Y-m-d",time())
$var3 = date("Y-m-d",time());
$var4 = date("Y-m-d",strtotime($var2));

$change = false;
if($var3 == $var4){
    $var2 = date("Y-m-d H:i:s",time());
    $change = true;
}

//$var4 = time();

//echo $var1 . " " . $var2 . " " . $var3 . " " . $var4;

$sql = "SELECT id, plane, name, node, startdate, enddate, userinfo, state FROM reserve_table WHERE (startdate BETWEEN UNIX_TIMESTAMP('$var1') AND UNIX_TIMESTAMP('$var2')) AND (enddate BETWEEN UNIX_TIMESTAMP('$var1') AND UNIX_TIMESTAMP('$var2')) ORDER BY node ASC, startdate ASC;";

//$sql = "SELECT from_unixtime(MIN(startdate),\"%H:%i\") startdate FROM reserve_table WHERE plane = '$var2' AND node = $var3 AND (startdate BETWEEN UNIX_TIMESTAMP('$var1') AND $var4) AND (enddate BETWEEN UNIX_TIMESTAMP('$var1') AND $var4);";
$result = $conn->query($sql);
$return;

$header = "";


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $return = $row;
        if ($row['node'] !== $header) {
            if ($header !== "") {
                echo "</table></div>";
            }
            $header = $row['node'];
            echo "<h2>" . $row['name'] . "</h2>";
            echo "<div class=\"reserveTable\">";
            echo "<table>";
            echo "<tr>";
            echo "<td>Espacios</td>";
            //echo "<td>Sala</td>";
            echo "<td>Fecha inicio</td>";
            echo "<td>Fecha fin</td>";
            echo "<td>Aceptada</td>";
            echo "<td>Solicitante</td></tr>";
        }
        $userinfo = json_decode($row['userinfo'], true);
        echo "<tr class=\"reserveTR\" onclick=\"window.location.href = '/record.php?id=" . $row['id'] . "';\">";
        echo "<td>";
        echo $row['plane'];
        echo "</td>";
        echo "<td>";
        echo date('Y/m/d H:i', $row['startdate']);
        echo "</td>";
        echo "<td>";
        echo date('Y/m/d H:i', $row['enddate']);
        echo "</td>";
        echo "<td>";
        if($row['state'] === "Reserved")
            echo "Sí";
        else
            echo "No"; 
        echo "</td>";
        echo "<td style=\" width : 30%;\">";
        echo $userinfo['Nombre y apellidos'];
        echo "</td>";
        echo "</tr>";
    }
    echo "</tr></table></div>";
} else {
    if($change){
        echo "<h2>No hay reservas que hayan vencido hoy</h2>";
    }else{
        echo "<h2>No hubo reservas para este día</h2>";
    }
}
?>