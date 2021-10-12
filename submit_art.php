<?php
//artist submit
include("db.php");
if (isset($_POST["add_art"])) {
	$name = htmlspecialchars($_POST["name"]);
	$description = htmlspecialchars($_POST["description"]);
	if (!empty($_POST["username"])) {
          $author = htmlspecialchars($_POST["username"]); 
        } else {
          $author = "Anonymous";
        }
	
	$picture = rand(1,999999);
	$target_directory = "uploads/";
	$target2 = $target_directory . basename($_FILES["file"]["name"]);
    $imageFileType = strtolower(pathinfo($target2,PATHINFO_EXTENSION));
	$target = $target_directory . $picture . "." . $imageFileType;
    
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        die("only jpg or png or gif");
    }

    $check = getimagesize($_FILES["file"]["tmp_name"]);
     if($check !== false) {
		 if (empty($name)) {
			die("Please put name br");
		 }
		 if (empty($description)) {
			die("Please put description br");
		 }
		 
		 if (ctype_space($name) || preg_match("/ㅤ/", $name) || preg_match("/‎/", $name)) {
			die("Don't put empty characters in name br");
		 }

		 if (ctype_space($description) || preg_match("/ㅤ/", $description) || preg_match("/‎/", $description)) {
			die("Don't put empty characters in description br");
		 }
		 
		if (!empty($name) && !empty($description)) {
			$time = date("Y-m-d H:i:s", time() + 30);
			
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			
	        $cooldown1 = $conn->prepare("SELECT * FROM cooldown WHERE ip = ?"); 
            $cooldown1->bind_param("s", $ip); 
            $cooldown1->execute();
            $cooldown2 = $cooldown1->get_result();
		
		    if ($cooldown2->num_rows == 0) {
				if (move_uploaded_file($_FILES["file"]["tmp_name"], $target)) {
                    $addcooldown = $conn->prepare("INSERT INTO cooldown (ip, cooldown_time) VALUES (?,?)"); 
                    $addcooldown->bind_param("ss", $ip, $time); 
                    $addcooldown->execute();
		            $upload = $conn->prepare("INSERT INTO arts (name, description, image, author) VALUES (?,?,?,?)");
			        $upload->bind_param("ssss", $name, $description, $target, $author); 
		     	    $upload->execute();
				    die("Uploaded");
                }
		    } else {
			$cooldown3 = $cooldown2->fetch_assoc();	
     			if ($cooldown3["cooldown_time"] < date("Y-m-d H:i:s")) {
				    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target)) {
                        $upload = $conn->prepare("INSERT INTO arts (name, description, image, author) VALUES (?,?,?,?)");
			            $upload->bind_param("ssss", $name, $description, $target, $author); 
		     	        $upload->execute();

                                $addcooldown2 = $conn->prepare("UPDATE cooldown SET cooldown_time = ? WHERE ip = ?"); 
                                $addcooldown2->bind_param("ss", $time, $ip); 
                                $addcooldown2->execute();
					    die("Uploaded <a href=\"index.php\">Go back home</a>");
					}
			    } else {
			        die("YOU HAVE A COOLODNW FOR 30 SeCONDS");
                }
			}
		}
     } else {
        die("WTF this is not a image");
     }
}
?>
<html>
	<head>
		<title>2bart: Semi anarchy art thing</title>
		<link type="text/css" rel="stylesheet" href="2bart.css">
		</head>
		<body>
			<center>
				<?php require("header.php"); ?>
				<div class="art_background_container">
					<div class="art_container">
						<div class="art_header">
							<font class="art_header_text">Submit Art</font>
						</div>
						<br>
							<form method="POST" action="submit_art.php" enctype="multipart/form-data">
								<font size="2">
									<label>Art name:</label>
								</font>
								<input type="text" name="name">
									<br>
										<font size="2">
											<label>Art description:</label>
										</font>
										<input type="text" name="description">
											<br>
												<font size="2">
													<label>Art author (not required):</label>
												</font>
												<input type="text" name="username">
													<br>
														<font size="2">
															<label>Art picture:</label>
														</font>
														<input type="file" id="file" name="file" accept="image/png, image/jpeg">
															<br>
																<br>
																	<input type="submit" id="submit" value="add art" name="add_art">
																	</form>
																</div>
															</div>
														</center>
													</body>
												</html>
												