<?php
	include 'includes/session.php';
	include 'includes/scripts_sendEmail.php';

	if(isset($_POST['signup'])){
		$company = $_POST['company'];
		$type = 2;
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$repassword = $_POST['repassword'];

		$_SESSION['firstname'] = $firstname;
		$_SESSION['lastname'] = $lastname;
		$_SESSION['email'] = $email;

		// if(!isset($_SESSION['captcha'])){
		// 	require('recaptcha/src/autoload.php');		
		// 	$recaptcha = new \ReCaptcha\ReCaptcha('6LevO1IUAAAAAFCCiOHERRXjh3VrHa5oywciMKcw', new \ReCaptcha\RequestMethod\SocketPost());
		// 	$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

		// 	if (!$resp->isSuccess()){
		//   		$_SESSION['error'] = 'Please answer recaptcha correctly';
		//   		header('location: signup.php');	
		//   		exit();	
		//   	}	
		//   	else{
		//   		$_SESSION['captcha'] = time() + (10*60);
		//   	}

		// }

		if($password != $repassword){
			$_SESSION['error'] = 'Passwords did not match';
			header('location: signup.php');
		}
		else{
			$conn = $pdo->open();

			$stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM users WHERE email=:email");
			$stmt->execute(['email'=>$email]);
			$row = $stmt->fetch();
			if($row['numrows'] > 0){
				$_SESSION['error'] = 'Email already taken';
				header('location: signup.php');
			}
			else{
				$now = date('Y-m-d');
				$password = password_hash($password, PASSWORD_DEFAULT);

				//generate code
				$set='123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$code=substr(str_shuffle($set), 0, 12);

				try{
					$stmt = $conn->prepare("INSERT INTO users (type, company ,email, password, firstname, lastname, activate_code, created_on) VALUES (:type, :company, :email, :password, :firstname, :lastname, :code, :now)");
					$stmt->execute(['type'=>$type,'company'=>$company,'email'=>$email, 'password'=>$password, 'firstname'=>$firstname, 'lastname'=>$lastname, 'code'=>$code, 'now'=>$now]);
					$userid = $conn->lastInsertId();

					$message = "
						<h2>Thank you for Registering.</h2>
						<p>Your Account:</p>
						<p>Email: ".$email."</p>
						<p>Password: ".$_POST['password']."</p>
						<p>Please click the link below to activate your account.</p>
						<a href='https://www.easyserve.ph/activate.php?code=".$code."&user=".$userid."'>Activate Account</a>
					";

					// Initialize and use EmailSender to send the email
       			$emailSender = new EmailSender();
		       	 try{
			        $result = $emailSender->sendEmail($email, $message);

			        unset($_SESSION['firstname']);
			        unset($_SESSION['lastname']);
			        unset($_SESSION['email']);

			        $_SESSION['success'] = 'Account created. Check your email to activate.';
		       		header('location: signup.php');
			

				  } catch (Exception $e) {
				        $_SESSION['error'] = 'Message could not be sent. Mailer Error: '. $e->getMessage();
				        header('location: signup.php');
				   }

				}
				catch(PDOException $e){
					$_SESSION['error'] = $e->getMessage();
					header('location: register.php');
				}

				$pdo->close();

			}

		}

	}
	else{
		$_SESSION['error'] = 'Fill up signup form first';
		header('location: signup.php');
	}

?>