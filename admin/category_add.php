
<?php
	include 'includes/session.php';
	include 'includes/slugify.php';

	if(isset($_POST['add'])){
		$name = $_POST['name'];
		$slug = slugify($name);

		$conn = $pdo->open();

		$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM category WHERE cat_slug=:slug");
		$stmt->execute(['slug'=>$slug]);
		$row = $stmt->fetch();

		if($row['numrows'] > 0){
			$_SESSION['error'] = 'Category already exist';
		}
		else{
			try{
				$stmt = $conn->prepare("INSERT INTO category (name,cat_slug) VALUES (:name,:slug)");
				$stmt->execute(['name'=>$name,'slug'=>$slug]);
				$_SESSION['success'] = 'Category added successfully';
			}
			catch(PDOException $e){
				$_SESSION['error'] = $e->getMessage();
			}
		}

		$pdo->close();
	}
	else{
		$_SESSION['error'] = 'Fill up category form first';
	}

	header('location: category.php');

?>