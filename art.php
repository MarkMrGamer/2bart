<?php
include("db.php");
if (!isset($_GET["id"]) && !isset($_GET["upvote"]) && !isset($_GET["downvote"])) {
	header("Location: /2bart");
}
if (isset($_GET["id"])) {
	$id = $_GET["id"];
	$thing = $conn->prepare("SELECT * FROM arts WHERE id = ?"); 
    $thing->bind_param("i", $id); 
    $thing->execute();
    $details = $thing->get_result();
	
	if ($details->num_rows > 0) {
		$details2 = $details->fetch_assoc();

		//get views
		$art_id = $details2["id"];
	    $views1 = $conn->prepare("SELECT * FROM views WHERE art_id = ?"); 
        $views1->bind_param("i", $art_id); 
        $views1->execute();
        $views2 = $views1->get_result();
		
		//check if a person view or not
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    $views3 = $conn->prepare("SELECT * FROM views WHERE ip = ? AND art_id = ?"); 
        $views3->bind_param("si", $ip, $art_id); 
        $views3->execute();
        $views4 = $views3->get_result();
		
		if ($views4->num_rows == 0) {
            $addview = $conn->prepare("INSERT INTO views (ip, art_id) VALUES (?,?)"); 
            $addview->bind_param("si", $ip, $art_id); 
            $addview->execute();
		}
		
        //get upvotes
		$upvote = "upvote";
		$art_id = $details2["id"];
	    $votes1 = $conn->prepare("SELECT * FROM votes WHERE vote = ? AND art_id = ?"); 
        $votes1->bind_param("si", $upvote, $art_id); 
        $votes1->execute();
        $votes2 = $votes1->get_result();
		
        //get comments
	    $comments = $conn->prepare("SELECT * FROM comments WHERE art_id = ? ORDER BY id DESC"); 
        $comments->bind_param("i", $art_id);
        $comments->execute();
        $comments2 = $comments->get_result();
		
        //get downvotes
		$downvote = "downvote";
	    $votes3 = $conn->prepare("SELECT * FROM votes WHERE vote = ? AND art_id = ?"); 
        $votes3->bind_param("si", $downvote, $art_id); 
        $votes3->execute();
        $votes4 = $votes3->get_result();
		
		if (isset($_POST["add_comment"])) {
	        if (!empty($_POST["username"])) {
                $author = htmlspecialchars($_POST["username"]); 
            } else {
                $author = "Anonymous";
            }
	        
			$comment = htmlspecialchars($_POST["comment"]);
			
			if (ctype_space($comment) || preg_match("/ㅤ/", $comment) || preg_match("/‎/", $comment)) {
				die("Why are you trying to bypass empty characters");
			}

			if (!empty($comment)) {
			    $time = date("Y-m-d H:i:s", time() + 30);
			
		        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			 
	            $cooldown1 = $conn->prepare("SELECT * FROM cooldown WHERE ip = ?"); 
                $cooldown1->bind_param("s", $ip); 
                $cooldown1->execute();
                $cooldown2 = $cooldown1->get_result();
		
		        if ($cooldown2->num_rows == 0) {
                        $addcooldown = $conn->prepare("INSERT INTO cooldown (ip, cooldown_time) VALUES (?,?)"); 
                        $addcooldown->bind_param("ss", $ip, $time); 
                        $addcooldown->execute();
						
					    $comment1 = $conn->prepare("INSERT INTO comments (name, comment, art_id, ip) VALUES (?,?,?,?)"); 
                        $comment1->bind_param("ssis", $author, $comment, $art_id, $ip); 
                        $comment1->execute();
						header('Location: ' . $_SERVER['HTTP_REFERER']);
                } else {
				        $cooldown3 = $cooldown2->fetch_assoc();
				
     			if ($cooldown3["cooldown_time"] < date("Y-m-d H:i:s")) {
		            $comment2 = $conn->prepare("INSERT INTO comments (name, comment, art_id, ip) VALUES (?,?,?,?)"); 
                    $comment2->bind_param("ssis", $author, $comment, $art_id, $ip); 
                    $comment2->execute();
					
                    $addcooldown2 = $conn->prepare("UPDATE cooldown SET cooldown_time = ? WHERE ip = ?"); 
                    $addcooldown2->bind_param("ss", $time, $ip); 
                    $addcooldown2->execute();
					header('Location: ' . $_SERVER['HTTP_REFERER']);
			    } else {
			        die("YOU HAVE A COOLODNW FOR 30 SeCONDS");
             }
			}
		}
		}
	} else {
		header("Location: /2bart");
	}
}
if (isset($_GET["upvote"])) {
	
    //check if it exist
	$id = $_GET["upvote"];
	$thing = $conn->prepare("SELECT * FROM arts WHERE id = ?"); 
    $thing->bind_param("i", $id); 
    $thing->execute();
    $details = $thing->get_result();
    
	if ($details->num_rows == 0) {
		header("Location: /2bart");
	} else {
		$details2 = $details->fetch_assoc();
		
		//get views
		$art_id = $details2["id"];
	    $views1 = $conn->prepare("SELECT * FROM views WHERE art_id = ?"); 
        $views1->bind_param("i", $art_id); 
        $views1->execute();
        $views2 = $views1->get_result();
		
        //get upvotes
		$upvote = "upvote";
	    $votes1 = $conn->prepare("SELECT * FROM votes WHERE vote = ? AND art_id = ?"); 
        $votes1->bind_param("si", $upvote, $art_id); 
        $votes1->execute();
        $votes2 = $votes1->get_result();
		
        //get comments
	    $comments = $conn->prepare("SELECT * FROM comments WHERE art_id = ? ORDER BY id DESC"); 
        $comments->bind_param("i", $art_id);
        $comments->execute();
        $comments2 = $comments->get_result();
		
        //get downvotes
		$downvote = "downvote";
	    $votes3 = $conn->prepare("SELECT * FROM votes WHERE vote = ? AND art_id = ?"); 
        $votes3->bind_param("si", $downvote, $art_id); 
        $votes3->execute();
        $votes4 = $votes3->get_result();
	}
	
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$art_id = $details2["id"];
	
	//I must check 
	$thing2 = $conn->prepare("SELECT * FROM votes WHERE ip = ? AND art_id = ?"); 
    $thing2->bind_param("si", $ip, $art_id); 
    $thing2->execute();
    $checking = $thing2->get_result();
	$checking2 = $checking->fetch_assoc();
    
	if ($checking->num_rows > 0) {
		if ($checking2["vote"] == "upvote") {
			die("You already upvoted tbh");
		} else {
			$voting = "upvote";
		    $changevote = $conn->prepare("UPDATE votes SET vote = ? WHERE ip = ? AND art_id = ?"); 
            $changevote->bind_param("ssi", $voting, $ip, $art_id); 
            $changevote->execute();
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		}
	} else {
		$voting = "upvote";
		$addvote = $conn->prepare("INSERT INTO votes (ip, vote, art_id) VALUES (?,?,?)"); 
        $addvote->bind_param("ssi", $ip, $voting, $art_id); 
        $addvote->execute();
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}

}
if (isset($_GET["downvote"])) {
	
    //check if it exist
	$id = $_GET["downvote"];
	$thing = $conn->prepare("SELECT * FROM arts WHERE id = ?"); 
    $thing->bind_param("i", $id); 
    $thing->execute();
    $details = $thing->get_result();
    
	if ($details->num_rows == 0) {
		header("Location: /2bart");
	} else {
		$details2 = $details->fetch_assoc();

		//get views
		$art_id = $details2["id"];
	    $views1 = $conn->prepare("SELECT * FROM views WHERE art_id = ?"); 
        $views1->bind_param("i", $art_id); 
        $views1->execute();
        $views2 = $views1->get_result();
		
        //get upvotes
		$upvote = "upvote";
		$art_id = $details2["id"];
	    $votes1 = $conn->prepare("SELECT * FROM votes WHERE vote = ? AND art_id = ?"); 
        $votes1->bind_param("si", $upvote, $art_id); 
        $votes1->execute();
        $votes2 = $votes1->get_result();
		
        //get comments
	    $comments = $conn->prepare("SELECT * FROM comments WHERE art_id = ? ORDER BY id DESC"); 
        $comments->bind_param("i", $art_id);
        $comments->execute();
        $comments2 = $comments->get_result();
		
        //get downvotes
		$downvote = "downvote";
	    $votes3 = $conn->prepare("SELECT * FROM votes WHERE vote = ? AND art_id = ?"); 
        $votes3->bind_param("si", $downvote, $art_id); 
        $votes3->execute();
        $votes4 = $votes3->get_result();
	}
	
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$art_id = $details2["id"];
	
	//I must check 
	$thing2 = $conn->prepare("SELECT * FROM votes WHERE ip = ? AND art_id = ?"); 
    $thing2->bind_param("si", $ip, $art_id); 
    $thing2->execute();
    $checking = $thing2->get_result();
	$checking2 = $checking->fetch_assoc();
    
	if ($checking->num_rows > 0) {
		if ($checking2["vote"] == "downvote") {
			die("You already downvoted tbh");
		} else {
			$voting = "downvote";
		    $changevote = $conn->prepare("UPDATE votes SET vote = ? WHERE ip = ? AND art_id = ?"); 
            $changevote->bind_param("ssi", $voting, $ip, $art_id); 
            $changevote->execute();
			header('Location: ' . $_SERVER['HTTP_REFERER']);
		}
	} else {
		$voting = "downvote";
		$addvote = $conn->prepare("INSERT INTO votes (ip, vote, art_id) VALUES (?,?,?)"); 
        $addvote->bind_param("ssi", $ip, $voting, $art_id); 
        $addvote->execute();
		header('Location: ' . $_SERVER['HTTP_REFERER']);
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
							<font class="art_header_text">
								<?php echo $details2["name"]; ?>
								<font size="-1">by <?php echo $details2["author"]; ?>
								</font>
							</font>
						</div>
						<table width="100%" border="0">
							<tr>
								<td align="center">
								    <!--[if IE]><a class="image_art" href="<?php echo $details2["image"]; ?>"><img width="380" height="350" src="<?php echo $details2["image"]; ?>"></a><![endif]-->
									<!--[if !IE]><!--><a class="image_art" href="<?php echo $details2["image"]; ?>"><img width="100%" height="100%" src="<?php echo $details2["image"]; ?>"></a><!--<![endif]-->
									</td>
								</tr>
								<tr>
									<td>
										<font size="3">
											<table width="100%">
												<tr>
													<td valign="top" align="left">
														<font size="3">
															<b>
																<?php echo $views2->num_rows; ?></b> views</font>
													</td>
													<td align="right">
														<img src="arrowup.png">
															<font color="green" size="-1">
																<?php echo $votes2->num_rows; ?></font>
															<img src="arrowdown.png">
																<font color="red" size="-1">
																	<?php echo $votes4->num_rows; ?></font>
																<br>
																	<a class="vote_button" href="art.php?upvote=<?php echo $details2["id"]; ?>"><img src="upvote.png"></a>
																		<a class="vote_button" href="art.php?downvote=<?php echo $details2["id"]; ?>"><img src="downvote.png"></a>
																		</td>
																	</tr>
																</table>
																<table width="100%">
																	<tr>
																		<td width="200">
																			<b>Description</b>: <br>
																				<?php echo $details2["description"]; ?>
																			</td>
																			<td>
																				<b>Date</b>: <br>
																					<?php echo $details2["date"]; ?>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</div>
														<?php if ($comments2->num_rows > 0) { ?>
														<div class="art_container">
															<div class="art_header">
																<font class="art_header_text">Comments</font>
															</div>
															<table style="line-break: anywhere;" width="100%">
																<?php while ($comment_details = $comments2->fetch_assoc()) { ?>
																<tr>
																	<td align="left">
																		<b>
																			<?php echo $comment_details["name"]; ?>
																		</b> - <?php echo $comment_details["date"]; ?>
																	</b>
																	<br>
																		<?php echo $comment_details["comment"]; ?>
																	</b>
																	<br>
																	</td>

																</tr>
																<?php 
										}
										?>
															</table>
														</div>
														<?php 
																	}
																	?>
														<div class="art_container">
															<div class="art_header">
																<font class="art_header_text">Add Comment</font>
															</div>
															<table width="100%">
																<tr>
																	<td>
																		<form method="POST" action="art.php?id=<?php echo $details2["id"]; ?>">
																			<font size="2">
																				<label>Username (not required):</label>
																			</font>
																			<input type="text" name="username">
																				<br>
																					<font size="2">
																						<label>Comment:</label>
																					</font>
																					<input type="text" name="comment">
																						<br>
																							<input type="submit" id="submit" value="add comment" name="add_comment">
																							</form>
																						</td>
																					</tr>
																				</table>
																			</div>
																			<br>
																			</center>
																		</body>
																	</html>
																	