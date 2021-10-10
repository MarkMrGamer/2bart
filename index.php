<?php
include("db.php");

//since im terrible at pagination, here is some code from another repo
$total_arts = $conn->query('SELECT COUNT(*) FROM arts')->fetch_row()[0]; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$art_numbers = 9;

$thing = $conn->prepare("SELECT * FROM arts ORDER BY id DESC LIMIT ?,?"); 
$calc_arts = ($page - 1) * $art_numbers;
$thing->bind_param("ii", $calc_arts, $art_numbers);
$thing->execute();
$things = $thing->get_result();
$counter = 0;
?>
<html>
	<head>
		<title>2bart: Anarchy art thing</title>
	</head>
	<body>
		<center>
			<a href="/2bart"><img src="2bart.png"></a>
				<p>Welcome to 2bart</p>
				<a href="submit_art.php">Submit an art</a>
				<br>
					<br>
						<table border="1">
							<tr>
					<?php
					while($art = $things->fetch_assoc()) {
					$counter++;
						?>
								<td align="center" width="106" height="136">
									<a href="art.php?id=<?php echo $art["id"]; ?>"><img src="<?php echo $art["image"]; ?>" width="100" height="100"></a>
										<br>
											<font size="2"><a href="art.php?id=<?php echo $art["id"]; ?>"><?php echo $art["name"]; ?></a>
												<br> by <?php echo $art["author"]; ?>
												</font>
											</td>
				<?php
				if ($counter == 3) {
					$counter = 0;
					echo "</tr>";
				}
					}
				?>
				                        <tr>
										    <td colspan="3" align="center">
											<?php if (ceil($total_arts / $art_numbers) > 0): ?>
											<?php if ($page > 1): ?>
											<a href="index.php?page=<?php echo $page-1; ?>">Back</a> 
											<?php endif; ?>
											|
											<?php if ($page < ceil($total_arts / $art_numbers)): ?>
											<a href="index.php?page=<?php echo $page+1; ?>">Next</a>
											<?php endif; ?>
											<?php endif; ?>
										    </td>
										</tr>
									</table>
										</center>
									</body>
								</html>
