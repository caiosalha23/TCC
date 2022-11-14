<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$itemNumber = htmlentities($_POST['itemDetailsItemNumber']);
	
	if(isset($_POST['itemDetailsItemNumber'])){
		
		// Check if mandatory fields are not empty
		if(!empty($itemNumber)){
			
			// Sanitize item number
			$itemNumber = filter_var($itemNumber, FILTER_SANITIZE_STRING);

			// Check if the item is in the database
			$itemSql = 'SELECT itemNumber FROM item WHERE itemNumber=:itemNumber';
			$itemStatement = $conn->prepare($itemSql);
			$itemStatement->execute(['itemNumber' => $itemNumber]);
			
			if($itemStatement->rowCount() > 0){
				
				// Item exists in DB. Hence start the DELETE process
				$deleteItemSql = 'DELETE FROM item WHERE itemNumber=:itemNumber';
				$deleteItemStatement = $conn->prepare($deleteItemSql);
				$deleteItemStatement->execute(['itemNumber' => $itemNumber]);

				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Item excluído.</div>';
				exit();
				
			} else {
				// Item does not exist, therefore, tell the user that he can't delete that item 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>O item não existe no banco de dados. Portanto, não é possível excluir.</div>';
				exit();
			}
			
		} else {
			// Item number is empty. Therefore, display the error message
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, insira o número do item</div>';
			exit();
		}
	}
?>