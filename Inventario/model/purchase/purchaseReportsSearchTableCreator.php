<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$uPrice = 0;
	$qty = 0;
	$totalPrice = 0;
	
	$purchaseDetailsSearchSql = 'SELECT * FROM purchase';
	$purchaseDetailsSearchStatement = $conn->prepare($purchaseDetailsSearchSql);
	$purchaseDetailsSearchStatement->execute();

	$output = '<table id="purchaseReportsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
				<thead>
					<tr>
						<th>ID da Compra</th>
						<th>Número do Item</th>
						<th>Data da Compra</th>
						<th>Nome do Item</th>
						<th>Nome do Fornecedor</th>
						<th>ID do Fornecedor</th>
						<th>Quantidade</th>
						<th>Preço por Unidade</th>
						<th>Preço Total</th>
					</tr>
				</thead>
				<tbody>';
	
	// Create table rows from the selected data
	while($row = $purchaseDetailsSearchStatement->fetch(PDO::FETCH_ASSOC)){
		$uPrice = $row['unitPrice'];
		$qty = $row['quantity'];
		$totalPrice = $uPrice * $qty;
		
		$output .= '<tr>' .
						'<td>' . $row['purchaseID'] . '</td>' .
						'<td>' . $row['itemNumber'] . '</td>' .
						'<td>' . $row['purchaseDate'] . '</td>' .
						'<td>' . $row['itemName'] . '</td>' .
						'<td>' . $row['vendorName'] . '</td>' .
						'<td>' . $row['vendorID'] . '</td>' .
						'<td>' . $row['quantity'] . '</td>' .
						'<td>' . $row['unitPrice'] . '</td>' .
						'<td>' . $totalPrice . '</td>' .
					'</tr>';
	}
	
	$purchaseDetailsSearchStatement->closeCursor();
	
	$output .= '</tbody>
					<tfoot>
						<tr>
							<th>Total</th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</tfoot>
				</table>';
	echo $output;
?>


