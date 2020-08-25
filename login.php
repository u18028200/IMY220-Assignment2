<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;	
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Ivan Oosthuizen">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			//---------------------TASK 2---------------------
			define('KB', 1024);
			define('MB', 1048576);
			define('GB', 1073741824);
			define('TB', 1099511627776);

			if (isset($_POST["submit"])){

				$email = $_POST["hEmail"];
				$pass = $_POST["hPass"];

				$file = $_FILES["picToUpload"];
				$fName = $file["name"];
				$fExtension = strtolower(pathinfo($fName)['extension']);
				$fSize = $file["size"];
				$fType = $file["type"];
				$fPath = $file["tmp_name"];

				$allow = ["jpg", "jpeg"];
				$allowedSize = 1*MB;
				$newPath = "gallery/".$fName;

				if (in_array($fExtension, $allow) && $fSize <= $allowedSize && $file["error"]===0){

					move_uploaded_file($fPath, $newPath);

					$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
					$res = $mysqli->query($query);
					if($row = mysqli_fetch_array($res)){
						$userID = $row['user_id'];
						$query = "INSERT INTO tbgallery (user_id, filename) VALUES ('$userID', '$fName');";
						$res = mysqli_query($mysqli, $query) == TRUE;
					}
				
				}
				else {
					if (!in_array($fExtension, $allow)){
						echo 	'<div class="alert alert-danger mt-3" role="alert">
	  								Incorrect file type!
								</div>';
					}
					else if ($fSize > $allowedSize){
						echo 	'<div class="alert alert-danger mt-3" role="alert">
	  								Your image is too large!
								</div>';
					}
					else {
						echo 	'<div class="alert alert-danger mt-3" role="alert">
	  								Unkown error. Please try again.
								</div>';
					}
					
				}
			}
			//------------------------------------------------


			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					$userID = $row['user_id'];
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form action='' method='POST' enctype='multipart/form-data'>
								<div class='form-group'>";
									//---------------------TASK 2---------------------
					echo			"<input type='hidden' name='hEmail' value='".$email."'/>
									<input type='hidden' name='hPass' value='".$pass."'/>";
									//------------------------------------------------
					echo			"<input type='file' class='form-control' name='picToUpload' id='picToUpload' multiple/><br/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
								</div>
							  </form>";

					//---------------------TASK 3---------------------
					echo	"<div class='row imageGallery'>";
								
								$query = "SELECT filename FROM tbgallery WHERE user_id = '$userID'";
								$res = $mysqli->query($query);
								while($row = mysqli_fetch_array($res)) {
					echo 			"<div class='col-3' style='background-image: url(gallery/".$row['filename']."'></div>";
									//echo $row['filename'];
								}
								
					echo	"</div>";
					//------------------------------------------------
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>