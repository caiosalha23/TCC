<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['customerDetailsCustomerFullName'])){
		
		$fullName = htmlentities($_POST['customerDetailsCustomerFullName']);
		$email = htmlentities($_POST['customerDetailsCustomerEmail']);
		$mobile = htmlentities($_POST['customerDetailsCustomerMobile']);
		$phone2 = htmlentities($_POST['customerDetailsCustomerPhone2']);
		$address = htmlentities($_POST['customerDetailsCustomerAddress']);
		$address2 = htmlentities($_POST['customerDetailsCustomerAddress2']);
		$city = htmlentities($_POST['customerDetailsCustomerCity']);
		$district = htmlentities($_POST['customerDetailsCustomerDistrict']);
		$status = htmlentities($_POST['customerDetailsStatus']);
		
		if(isset($fullName) && isset($mobile) && isset($address)) {
			// Validate mobile number
			if(filter_var($mobile, FILTER_VALIDATE_INT) === 0 || filter_var($mobile, FILTER_VALIDATE_INT)) {
				// Valid mobile number
			} else {
				// Mobile is wrong
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Insira um número de telefone válido.</div>';
				exit();
			}
			
			// Validate second phone number only if it's provided by user
			if(!empty($phone2)){
				if(filter_var($phone2, FILTER_VALIDATE_INT) === false) {
					// Phone number 2 is not valid
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Insira um número de Celular válido.</div>';
					exit();
				}
			}
			
			// Validate email only if it's provided by user
			if(!empty($email)) {
				if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
					// Email is not valid
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Insira um email válido</div>';
					exit();
				}
			}
			
			// Validate address
			if($address == ''){
				// Address 1 is empty
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Insira um endereço válido</div>';
				exit();
			}
			
			// Check if Full name is empty or not
			if($fullName == ''){
				// Full Name is empty
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Insira um nome completo</div>';
				exit();
			}
			
			// Start the insert process
			$sql = 'INSERT INTO customer(fullName, email, mobile, phone2, address, address2, city, district, status) VALUES(:fullName, :email, :mobile, :phone2, :address, :address2, :city, :district, :status)';
			$stmt = $conn->prepare($sql);
			$stmt->execute(['fullName' => $fullName, 'email' => $email, 'mobile' => $mobile, 'phone2' => $phone2, 'address' => $address, 'address2' => $address2, 'city' => $city, 'district' => $district, 'status' => $status]);
			echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Cliente adicionado no banco de dados</div>';
		} else {
			// One or more fields are empty
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, insira todos os campos marcados com um (*)</div>';
			exit();
		}
	}
?>