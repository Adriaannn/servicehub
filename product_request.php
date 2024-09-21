<?php
	include 'includes/session.php'; 

		if($_POST['request_description'] == ""){
			$_SESSION['error'] = 'Fill up product form first';
		}

		$request_description = $_POST['request_description'];  
		$product_id = (int)$_POST['prodid'];  
		$user_id = $user['id'];    
		$request_date = date('Y-m-d',strtotime($_POST['request_date']));
		$status = 'P'; // Default value is P- Pending
		$conn = $pdo->open();
		try{ 
			$stmt = $conn->prepare("INSERT INTO job_order (user_id, product_id, request_description, request_date,status) VALUES (:user_id, :product_id, :request_description,:request_date,:status)");
			$stmt->execute(['user_id'=>$user_id, 'product_id'=>$product_id, 'request_description'=>$request_description, 'request_date'=>$request_date,'status'=>$status]);
			$_SESSION['success'] = 'Service added successfully'; 
		}
		catch(PDOException $e){
			$_SESSION['error'] = $e->getMessage();
		} 
		$pdo->close(); 
	 
		
	header('location: my_request.php');

?>