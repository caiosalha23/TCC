<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['purchaseDetailsItemNumber'])){

		$purchaseDetailsItemNumber = htmlentities($_POST['purchaseDetailsItemNumber']);
		$purchaseDetailsPurchaseDate = htmlentities($_POST['purchaseDetailsPurchaseDate']);
		$purchaseDetailsItemName = htmlentities($_POST['purchaseDetailsItemName']);
		$purchaseDetailsQuantity = htmlentities($_POST['purchaseDetailsQuantity']);
		$purchaseDetailsUnitPrice = htmlentities($_POST['purchaseDetailsUnitPrice']);
		$purchaseDetailsVendorName = htmlentities($_POST['purchaseDetailsVendorName']);
		
		$initialStock = 0;
		$newStock = 0;
		
		// Check if mandatory fields are not empty
		if(isset($purchaseDetailsItemNumber) && isset($purchaseDetailsPurchaseDate) && isset($purchaseDetailsItemName) && isset($purchaseDetailsQuantity) && isset($purchaseDetailsUnitPrice)){
			
			// Check if itemNumber is empty
			if($purchaseDetailsItemNumber == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, insira o número do item.</div>';
				exit();
			}
			
			// Check if itemName is empty
			if($purchaseDetailsItemName == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>
				Por favor, insira o nome do item.</div>';
				exit();
			}
			
			// Check if quantity is empty
			if($purchaseDetailsQuantity == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>
				Por favor, insira a Quantidade.</div>';
				exit();
			}
			
			// Check if unit price is empty
			if($purchaseDetailsUnitPrice == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Por favor, insira o preço por unidade.</div>';
				exit();
			}
			
			// Sanitize item number
			$purchaseDetailsItemNumber = filter_var($purchaseDetailsItemNumber, FILTER_SANITIZE_STRING);
			
			// Validate item quantity. It has to be an integer
			if(filter_var($purchaseDetailsQuantity, FILTER_VALIDATE_INT) === 0 || filter_var($purchaseDetailsQuantity, FILTER_VALIDATE_INT)){
				// Valid quantity
			} else {
				// Quantity is not a valid number
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Insira um número válido para a quantidade.</div>';
				exit();
			}
			
			// Validate unit price. It has to be an integer or floating point value
			if(filter_var($purchaseDetailsUnitPrice, FILTER_VALIDATE_FLOAT) === 0.0 || filter_var($purchaseDetailsUnitPrice, FILTER_VALIDATE_FLOAT)){
				// Valid unit price
			} else {
				// Unit price is not a valid number
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Insira um número válido para o preço por unidade.</div>';
				exit();
			}
			
			// Check if the item exists in item table and 
			// calculate the stock values and update to match the new purchase quantity
			$stockSql = 'SELECT stock FROM item WHERE itemNumber=:itemNumber';
			$stockStatement = $conn->prepare($stockSql);
			$stockStatement->execute(['itemNumber' => $purchaseDetailsItemNumber]);
			if($stockStatement->rowCount() > 0){
				
				// Get the vendorId for the given vendorName
				$vendorIDsql = 'SELECT * FROM vendor WHERE fullName = :fullName';
				$vendorIDStatement = $conn->prepare($vendorIDsql);
				$vendorIDStatement->execute(['fullName' => $purchaseDetailsVendorName]);
				$row = $vendorIDStatement->fetch(PDO::FETCH_ASSOC);
				$vendorID = $row['vendorID'];
				
				// Item exits in the item table, therefore, start the inserting data to purchase table
				$insertPurchaseSql = 'INSERT INTO purchase(itemNumber, purchaseDate, itemName, unitPrice, quantity, vendorName, vendorID) VALUES(:itemNumber, :purchaseDate, :itemName, :unitPrice, :quantity, :vendorName, :vendorID)';
				$insertPurchaseStatement = $conn->prepare($insertPurchaseSql);
				$insertPurchaseStatement->execute(['itemNumber' => $purchaseDetailsItemNumber, 'purchaseDate' => $purchaseDetailsPurchaseDate, 'itemName' => $purchaseDetailsItemName, 'unitPrice' => $purchaseDetailsUnitPrice, 'quantity' => $purchaseDetailsQuantity, 'vendorName' => $purchaseDetailsVendorName, 'vendorID' => $vendorID]);
				
				// Calculate the new stock value using the existing stock in item table
				$row = $stockStatement->fetch(PDO::FETCH_ASSOC);
				$initialStock = $row['stock'];
				$newStock = $initialStock + $purchaseDetailsQuantity;
				
				// Update the new stock value in item table
				$updateStockSql = 'UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber';
				$updateStockStatement = $conn->prepare($updateStockSql);
				$updateStockStatement->execute(['stock' => $newStock, 'itemNumber' => $purchaseDetailsItemNumber]);
				
				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Detalhes de compra adicionados ao banco de dados e valores de estoque atualizados.</div>';
				exit();
				
			} else {
				// Item does not exist in item table, therefore, you can't make a purchase from it 
				// to add it to DB as a new purchase
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>O item não existe no banco de dados. Portanto, primeiro insira este item no banco de dados usando a guia <strong>Item</strong></div>';
				exit();
			}

		} else {
			// One or more mandatory fields are empty. Therefore, display a the error message
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>
			Por favor, insira todos os campos marcados com um (*)</div>';
			exit();
		}
	}
?>