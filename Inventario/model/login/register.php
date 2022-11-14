<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$registerFullName = '';
	$registerUsername = '';
	$registerPassword1 = '';
	$registerPassword2 = '';
	$hashedPassword = '';
	
	if(isset($_POST['registerUsername'])){
		$registerFullName = htmlentities($_POST['registerFullName']);
		$registerUsername = htmlentities($_POST['registerUsername']);
		$registerPassword1 = htmlentities($_POST['registerPassword1']);
		$registerPassword2 = htmlentities($_POST['registerPassword2']);
		
		if(!empty($registerFullName) && !empty($registerUsername) && !empty($registerPassword1) && !empty($registerPassword2)){
			
			// Sanitize name
			$registerFullName = filter_var($registerFullName, FILTER_SANITIZE_STRING);
			
			// Check if name is empty
			if($registerFullName == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, insira seu nome.</div>';
				exit();
			}
			
			// Check if username is empty
			if($registerUsername == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor insira seu nome de usuário.</div>';
				exit();
			}
			
			// Check if both passwords are empty
			if($registerPassword1 == '' || $registerPassword2 == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, digite as duas senhas.</div>';
				exit();
			}
			
			// Check if username is available
			$usernameCheckingSql = 'SELECT * FROM user WHERE username = :username';
			$usernameCheckingStatement = $conn->prepare($usernameCheckingSql);
			$usernameCheckingStatement->execute(['username' => $registerUsername]);
			
			if($usernameCheckingStatement->rowCount() > 0){
				// Username already exists. Hence can't create a new user
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Nome de usuário não disponível. Selecione um nome de usuário diferente.</div>';
				exit();
			} else {
				// Check if passwords are equal
				if($registerPassword1 !== $registerPassword2){
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>
					As senhas não coincidem.</div>';
					exit();
				} else {
					// Start inserting user to DB
					// Encrypt the password
					$hashedPassword = md5($registerPassword1);
					$insertUserSql = 'INSERT INTO user(fullName, username, password) VALUES(:fullName, :username, :password)';
					$insertUserStatement = $conn->prepare($insertUserSql);
					$insertUserStatement->execute(['fullName' => $registerFullName, 'username' => $registerUsername, 'password' => $hashedPassword]);
					
					echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Registro concluído.</div>';
					exit();
				}
			}
		} else {
			// One or more mandatory fields are empty. Therefore, display a the error message
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>
			Por favor, insira todos os campos marcados com um (*)</div>';
			exit();
		}
	}
?>