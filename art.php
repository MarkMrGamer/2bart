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
			
			if (!empty($comment)) {
			    $time = date("Y-m-d H:i:s", time() + 30);
			
		        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			 
	            $cooldown1 = $conn->prepare("SELECT * FROM cooldown WHERE ip = ?"); 
                $cooldown1->bind_param("s", $ip); 
                $cooldown1->execute();
                $cooldown2 = $cooldown1->get_result();
		
		        if ($cooldown2->num_rows == 0) {
                        $addcooldown = $conn->prepare("INSERT INTO views (ip, cooldown) VALUES (?,?)"); 
                        $addcooldown->bind_param("si", $ip, $time); 
                        $addcooldown->execute();
						
					    $comment1 = $conn->prepare("INSERT INTO comments (name, comment, art_id, ip) VALUES (?,?,?,?)"); 
                        $comment1->bind_param("ssis", $author, $comment, $art_id, $ip); 
                        $comment1->execute();
						header('Location: ' . $_SERVER['HTTP_REFERER']);
                } else {
		                $cooldown3 = $conn->prepare("SELECT * FROM cooldown WHERE ip = ?"); 
                        $cooldown3->bind_param("i", $ip); 
                        $cooldown3->execute();
                        $cooldown4 = $cooldown3->get_result();
				        $cooldown5 = $cooldown4->fetch_assoc();
				
     			if ($cooldown5["cooldown"] < date("Y-m-d H:i:s")) {
		            $comment2 = $conn->prepare("INSERT INTO comments (name, comment, art_id, ip) VALUES (?,?,?,?)"); 
                    $comment2->bind_param("ssis", $author, $comment, $art_id, $ip); 
                    $comment2->execute();
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
			header("art.php?id=" . $art_id);
		}
	} else {
		$voting = "upvote";
		$addvote = $conn->prepare("INSERT INTO votes (ip, vote, art_id) VALUES (?,?,?)"); 
        $addvote->bind_param("ssi", $ip, $voting, $art_id); 
        $addvote->execute();
		header("art.php?id=" . $art_id);
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
			header("art.php?id=" . $art_id);
		}
	} else {
		$voting = "downvote";
		$addvote = $conn->prepare("INSERT INTO votes (ip, vote, art_id) VALUES (?,?,?)"); 
        $addvote->bind_param("ssi", $ip, $voting, $art_id); 
        $addvote->execute();
		header("art.php?id=" . $art_id);
	}

}
?>
<html>
	<head>
		<title>2bart: Anarchy art thing</title>
	</head>
	<body>
		<center>
			<a href="/2bart"><img src="2bart.png"></a><br>
				<a href="submit_art.php">Submit an art</a>
				<br>
					<br>
						<table border="1">
							<tr>
								<td align="center">
									<img src="<?php echo $details2["image"]; ?>">
		                </td>
									<td valign="top">
										<font size="5">
											<b><?php echo $details2["name"]; ?></b>
										</font>
										<br>
											<font size="3">by <?php echo $details2["author"]; ?></font>
											<br>
												<font size="3">
												    <?php echo $views2->num_rows; ?> views <br>
													<b>↑</b><?php echo $votes2->num_rows; ?><b>↓</b><?php echo $votes4->num_rows; ?></font><br>
													<a href="art.php?upvote=<?php echo $details2["id"]; ?>">Upvote this art</a><br>
													<a href="art.php?downvote=<?php echo $details2["id"]; ?>">Downvote this art</a>
												<br><br><b>Description</b>: <br> <?php echo $details2["description"]; ?><br><br><b>Date</b>: <br> <?php echo $details2["date"]; ?>
						</td>
											</tr>
											<tr>
												<td>
						            Comments
						        </td>
												<td>
						            
						        </td>
											</tr>
											<tr>
										<?php while ($comment_details = $comments2->fetch_assoc()) { ?>
											<td colspan="2">
											<b><?php echo $comment_details["name"]; ?></b> - <?php echo $comment_details["date"]; ?></b><br>
											<?php echo $comment_details["comment"]; ?></b><br>
											</td>
										<?php 
										}
										?>
											</tr>
											<tr>
											<td colspan="2">
											    <form method="POST" action="art.php?id=<?php echo $details2["id"]; ?>">
				                                    <label>Username (not required):</label> <input type="text" name="username"><br>
				                                    <label>Comment:</label> <input type="text" name="comment"><br>
				                                    <input type="submit" id="submit" value="add comment" name="add_comment">
				                                </form>
											</td>
											</tr>
										</table>
									</center>
								</body>
							</html>
