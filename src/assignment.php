<?php
require_once 'pass.php';
$connection =
new mysqli($db_hostname, $db_username, $db_password, $db_database);
if ($connection->connect_error) die($connection->connect_error);




//Check if delete was pressed
if (isset($_POST['delete']) && isset($_POST['id']))
{
	$id = get_post($connection, 'id');
	$query = "DELETE FROM video WHERE id ='$id'";
	$result = $connection->query($query);
	if (!$result) echo "DELETE failed: $query<br>" .
		$connection->error . "<br><br>";
}



//check if checkout was pressed
if (isset($_POST['check']) && isset($_POST['id']) && isset($_POST['rented']))
{
	$id = get_post($connection, 'id');
	if($_POST['rented'] == 1)
		$query = "UPDATE video SET rented = '0' WHERE id ='$id'";
	else $query = "UPDATE video SET rented = '1' WHERE id ='$id'";
	$result = $connection->query($query);
	if (!$result) echo "UPDATE failed: $query<br>" .
		$connection->error . "<br><br>";
}




//Delete All
if (isset($_POST['D'])){
	$query = "DELETE FROM video";
	$result = $connection->query($query);
	if (!$result) echo "DELETE failed: $query<br>" .
		$connection->error . "<br><br>";
}



//check form
if (isset($_POST['name']) &&
	isset($_POST['category']) &&
	isset($_POST['length']) &&
	isset($_POST['rented']))
{
$name = get_post($connection, 'name');
$category = get_post($connection, 'category');
$length = get_post($connection, 'length');
$rented = get_post($connection, 'rented');
$query = "INSERT INTO video (name, category, length, rented) VALUES" .
"('$name', '$category', '$length', '$rented')";

$result = $connection->query($query);
if (!$result) echo "INSERT failed: $query<br>" .
	$connection->error . "<br><br>";
}

echo <<<_END
<form action="assignment.php" method="post"><pre>
Name     <input type="text" name="name">
Category <input type="text" name="category">
Length   <input type="number" name="length" min="1">
Rented   <input type="number" name="rented" min="0" max="1">
         <input type="submit" value="ADD RECORD">
</pre></form>
_END;


//Show Table
//Filter 
$cate = array();
if (isset($_POST['drop'])){
	$id = $_POST['drop'];
	if ($id != 'All')
		$query = "SELECT * FROM video WHERE category = '$id'";
	else 	$query = "SELECT * FROM video";
}	
else{
	$query = "SELECT * FROM video";
}
$result = $connection->query($query);
if (!$result) die ("Database access failed: " . $connection->error);
	$rows = $result->num_rows;
echo "<table border = '1px solid black'>
<tr>  <th>ID</th>  
<th>Name</th>  
<th>Category</th>
<th>Length</th>
<th>Rented</th>  
</tr>";
for ($j = 0 ; $j < $rows ; ++$j)
{
	$result->data_seek($j);
	$row = $result->fetch_array(MYSQLI_NUM);

echo <<<_END
<pre>
<tr>
<td>$row[0]</td>
<td>$row[1]</td>
<td>$row[2]</td>
<td>$row[3]</td>
_END;
$cate[$j] = $row[2];
$ava = decide($row[4]);
echo<<<_END
<td>$ava</td>
</pre>
<td>
<form action="assignment.php" method="post">
<input type="hidden" name="delete" value="yes">
<input type="hidden" name="id" value="$row[0]">
<input type="submit" value="DELETE"></form>
</td>
<td>
<form action="assignment.php" method="post">
<input type="hidden" name="check" value="yes">
<input type="hidden" name="id" value="$row[0]">
<input type="hidden" name="rented" value="$row[4]">
<input type="submit" value="Check IN/OUT"></form>
</td>
</tr>
_END;
}
echo "</table>";






//Delete All
echo <<<_END
<br><br>
<form action="assignment.php" method="post">
<input type="hidden" name="D" value="yes">
<input type="submit" value="DELETE ALL"></form>
_END;





//Drop Down Menu
echo "<form action='assignment.php' method='post'>";
echo "<select name = 'drop'> 
	<option value = 'All'>All Movies</option>";
$cate = array_unique($cate);
$cat = array();
for($i = 0; $i<count($cate);$i++){
echo "<option value = '$cate[$i]'>$cate[$i]</option>";
}

echo <<<_END
</select>;
<input type="submit" value="filter"></form>
_END;





//Fix input
function get_post($connection, $var)
{
	return $connection->real_escape_string($_POST[$var]);
}




//Change between bool and availiablity 
function decide($num){
	if($num == 1)
		return "Avilable";
	else return "Checked Out";
}

$result->close();
$connection->close();
?>