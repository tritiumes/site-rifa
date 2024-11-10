<?php
require_once('config.php');

if (isset($_GET['notify']) == 'mercadopago') {	
	$json_event = file_get_contents('php://input', true);
	$event = json_decode($json_event);
	$mercadopago_access_token = $_settings->info('mercadopago_access_token');
	$enable_pixel = $_settings->info('enable_pixel');
	$facebook_access_token = $_settings->info('facebook_access_token');
	$facebook_pixel_id = $_settings->info('facebook_pixel_id');

	if (isset($event->type) == 'payment'){
		$url = 'https://api.mercadopago.com/v1/payments/'.$event->data->id.'';
		$headers = array(
			'Accept: application/json',
			'Authorization: Bearer '.$mercadopago_access_token
		);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$resposta = curl_exec($ch);
		curl_close($ch);
		$payment_info = json_decode($resposta, true);		
		$payment_id = $payment_info['id'];
		$status = $payment_info['status'];
		$payment_type = $payment_info['payment_type_id'];
		$pedido_id = $payment_info['external_reference']; 
		$qry = $conn->query("SELECT o.status, o.product_id, o.total_amount, o.quantity, c.firstname, c.lastname, c.phone
			FROM order_list o
			INNER JOIN customer_list c ON o.customer_id = c.id
			WHERE o.id = '{$pedido_id}'");

		if ($qry->num_rows > 0) {
			$row = $qry->fetch_assoc();
			$status_order = $row['status'];
			$product_id = $row['product_id'];
			$quantity = $row['quantity'];
			$total_amount = $row['total_amount'];
			$firstname = $row['firstname'];
			$lastname = $row['lastname'];
			$phone = '55'.$row['phone'].'';
		}

		$product_list = $conn->query("
			SELECT pending_numbers, paid_numbers
			FROM product_list
			WHERE id = '{$product_id}'
			");

		if ($product_list->num_rows > 0) {
			$row = $product_list->fetch_assoc();
			$pendingNumbers = $row['pending_numbers'];
			$updatePending = $pendingNumbers - $quantity;
			$paidNumbers = $row['paid_numbers'];
			$updatePaid = $paidNumbers + $quantity;			
		}
		
		if($status == 'approved'){	
			if($status_order == '1'){
				#Define o pedido como pago
				date_default_timezone_set('America/Sao_Paulo');
				$payment_date = date('Y-m-d H:i:s');
				$sql_ol = "UPDATE order_list SET status = '2', date_updated = '{$payment_date}', whatsapp_status = '' WHERE id = '{$pedido_id}'";
				$conn->query($sql_ol);

				#Atualiza quantidade de números pendentes e números pagos do sorteio
				$sql_pl = "UPDATE product_list SET pending_numbers = '{$updatePending}', paid_numbers = '{$updatePaid}' WHERE id = '{$product_id}'";
				$conn->query($sql_pl);

			#PIXEL AUTOMÁTICO
				if($enable_pixel == 1 && !empty($facebook_pixel_id) && !empty($facebook_access_token)){
					$url = "https://graph.facebook.com/v14.0/{$facebook_pixel_id}/events?access_token={$facebook_access_token}";
					$fn = hash('sha256', $firstname);
					$ln = hash('sha256', $lastname);
					$ph = hash('sha256', $phone);
					$data = [
						[
							'event_name' => 'Purchase',                     
							'event_time' => time(),
							'user_data' => [
								'fn' => $fn,
								'ln' => $ln,
								'ph' => $ph,
								'external_id' => hash('sha256', $pedido_id),
							],
							'custom_data' => [
								'currency' => 'BRL',
								'value' => (float) number_format($total_amount, 2, '.', ''),							
							],
						]
					];
					$options = [
						CURLOPT_URL => $url,
						CURLOPT_POST => true,
						CURLOPT_POSTFIELDS => json_encode([
							'data' => $data,
						//'test_event_code' => 'TEST86406'	
						]),
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_HTTPHEADER => [
							'Content-Type: application/json',
						],
					];

					$curl = curl_init();
					curl_setopt_array($curl, $options);
					$response = curl_exec($curl);
					curl_close($curl);

					if ($response) {
						$result = json_decode($response, true);
						echo "Evento enviado com sucesso. ID do evento: {$result['fbtrace_id']}";
					} else {
						echo "Ocorreu um erro ao enviar o evento: " . curl_error($curl);
					} 	


				}
				#PIXEL AUTOMÁTICO

			}
		}

	}
}

if (isset($_GET['notify']) == 'gerencianet') {
	$postData = json_decode(file_get_contents('php://input'));
	$enable_pixel = $_settings->info('enable_pixel');
	$facebook_access_token = $_settings->info('facebook_access_token');
	$facebook_pixel_id = $_settings->info('facebook_pixel_id');

	if($postData){
		if (isset($postData->evento) && isset($postData->data_criacao)) {
			header('HTTP/1.0 200 OK');
			exit();
		}

		$pixPaymentData = $postData->pix;
		if (empty($pixPaymentData)) {
			#Pagamento Pix não recebido pelo Webhook
			exit();
		} else { 
			$txID  = $pixPaymentData[0]->txid;
			$e2eID = $pixPaymentData[0]->endToEndId;
			$qry = $conn->query("SELECT o.status, o.product_id, o.total_amount, o.quantity, c.firstname, c.lastname, c.phone
				FROM order_list o
				INNER JOIN customer_list c ON o.customer_id = c.id
				WHERE o.txid = '{$txID}'");

			if ($qry->num_rows > 0) {
				$row = $qry->fetch_assoc();
				$status_order = $row['status'];
				$product_id = $row['product_id'];
				$quantity = $row['quantity'];
				$total_amount = $row['total_amount'];
				$firstname = $row['firstname'];
				$lastname = $row['lastname'];
				$phone = '55'.$row['phone'].'';
			}

			$product_list = $conn->query("
				SELECT pending_numbers, paid_numbers
				FROM product_list
				WHERE id = '{$product_id}'
				");

			if ($product_list->num_rows > 0) {
				$row = $product_list->fetch_assoc();
				$pendingNumbers = $row['pending_numbers'];
				$updatePending = $pendingNumbers - $quantity;
				$paidNumbers = $row['paid_numbers'];
				$updatePaid = $paidNumbers + $quantity;			
			}

			if($status_order == '1'){
				#Define o pedido como pago
				date_default_timezone_set('America/Sao_Paulo');
				$payment_date = date('Y-m-d H:i:s');
				$sql_ol = "UPDATE order_list SET status = '2', date_updated = '{$payment_date}', whatsapp_status = '' WHERE txid = '{$txID}'";
				$conn->query($sql_ol);

				#Atualiza quantidade de números pendentes e números pagos do sorteio
				$sql_pl = "UPDATE product_list SET pending_numbers = '{$updatePending}', paid_numbers = '{$updatePaid}' WHERE id = '{$product_id}'";
				$conn->query($sql_pl);
			#PIXEL AUTOMÁTICO
				if($enable_pixel == 1 && !empty($facebook_pixel_id) && !empty($facebook_access_token)){
					$url = "https://graph.facebook.com/v14.0/{$facebook_pixel_id}/events?access_token={$facebook_access_token}";
					$fn = hash('sha256', $firstname);
					$ln = hash('sha256', $lastname);
					$ph = hash('sha256', $phone);
					$data = [
						[
							'event_name' => 'Purchase',                     
							'event_time' => time(),
							'user_data' => [
								'fn' => $fn,
								'ln' => $ln,
								'ph' => $ph,
								'external_id' => hash('sha256', $txID),
							],
							'custom_data' => [
								'currency' => 'BRL',
								'value' => (float) number_format($total_amount, 2, '.', ''),							
							],
						]
					];
					$options = [
						CURLOPT_URL => $url,
						CURLOPT_POST => true,
						CURLOPT_POSTFIELDS => json_encode([
							'data' => $data,
						//'test_event_code' => 'TEST86406'	
						]),
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_HTTPHEADER => [
							'Content-Type: application/json',
						],
					];

					$curl = curl_init();
					curl_setopt_array($curl, $options);
					$response = curl_exec($curl);
					curl_close($curl);

					if ($response) {
						$result = json_decode($response, true);
						echo "Evento enviado com sucesso. ID do evento: {$result['fbtrace_id']}";
					} else {
						echo "Ocorreu um erro ao enviar o evento: " . curl_error($curl);
					} 	


				}
		    #PIXEL AUTOMÁTICO

			}

		}
	}
}
if (isset($_GET['notify']) == 'paggue') {
	$paggue_notify = file_get_contents('php://input', true);
	$paggue_get = json_decode($paggue_notify, true);
	$enable_pixel = $_settings->info('enable_pixel');
	$facebook_access_token = $_settings->info('facebook_access_token');
	$facebook_pixel_id = $_settings->info('facebook_pixel_id');
	$payment_id = $payment_info['id'];
	$status = $paggue_get['status'];		
	$pedido_id = $paggue_get['external_id'];
	$qry = $conn->query("SELECT o.status, o.product_id, o.total_amount, o.quantity, c.firstname, c.lastname, c.phone
		FROM order_list o
		INNER JOIN customer_list c ON o.customer_id = c.id
		WHERE o.id = '{$pedido_id}'");

	if ($qry->num_rows > 0) {
		$row = $qry->fetch_assoc();
		$status_order = $row['status'];
		$product_id = $row['product_id'];
		$quantity = $row['quantity'];
		$total_amount = $row['total_amount'];
		$firstname = $row['firstname'];
		$lastname = $row['lastname'];
		$phone = '55'.$row['phone'].'';
	}

	$product_list = $conn->query("
		SELECT pending_numbers, paid_numbers
		FROM product_list
		WHERE id = '{$product_id}'
		");

	if ($product_list->num_rows > 0) {
		$row = $product_list->fetch_assoc();
		$pendingNumbers = $row['pending_numbers'];
		$updatePending = $pendingNumbers - $quantity;
		$paidNumbers = $row['paid_numbers'];
		$updatePaid = $paidNumbers + $quantity;			
	}

	if($status == '1'){	
		if($status_order == '1'){
				#Define o pedido como pago
			date_default_timezone_set('America/Sao_Paulo');
			$payment_date = date('Y-m-d H:i:s');
			$sql_ol = "UPDATE order_list SET status = '2', date_updated = '{$payment_date}', whatsapp_status = '' WHERE id = '{$pedido_id}'";
			$conn->query($sql_ol);

				#Atualiza quantidade de números pendentes e números pagos do sorteio
			$sql_pl = "UPDATE product_list SET pending_numbers = '{$updatePending}', paid_numbers = '{$updatePaid}' WHERE id = '{$product_id}'";
			$conn->query($sql_pl);
			#PIXEL AUTOMÁTICO
			if($enable_pixel == 1 && !empty($facebook_pixel_id) && !empty($facebook_access_token)){
				$url = "https://graph.facebook.com/v14.0/{$facebook_pixel_id}/events?access_token={$facebook_access_token}";
				$fn = hash('sha256', $firstname);
				$ln = hash('sha256', $lastname);
				$ph = hash('sha256', $phone);
				$data = [
					[
						'event_name' => 'Purchase',                     
						'event_time' => time(),
						'user_data' => [
							'fn' => $fn,
							'ln' => $ln,
							'ph' => $ph,
							'external_id' => hash('sha256', $pedido_id),
						],
						'custom_data' => [
							'currency' => 'BRL',
							'value' => (float) number_format($total_amount, 2, '.', ''),							
						],
					]
				];
				$options = [
					CURLOPT_URL => $url,
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => json_encode([
						'data' => $data,
						//'test_event_code' => 'TEST86406'	
					]),
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_HTTPHEADER => [
						'Content-Type: application/json',
					],
				];

				$curl = curl_init();
				curl_setopt_array($curl, $options);
				$response = curl_exec($curl);
				curl_close($curl);

				if ($response) {
					$result = json_decode($response, true);
					echo "Evento enviado com sucesso. ID do evento: {$result['fbtrace_id']}";
				} else {
					echo "Ocorreu um erro ao enviar o evento: " . curl_error($curl);
				} 	


			}
		    #PIXEL AUTOMÁTICO


		}
	}

}


if (isset($_GET['notify']) == 'pagstar') {
	$pagstar_notify = file_get_contents('php://input', true);
	$pagstar_get = json_decode($pagstar_notify, true);
	$enable_pixel = $_settings->info('enable_pixel');
	$facebook_access_token = $_settings->info('facebook_access_token');
	$facebook_pixel_id = $_settings->info('facebook_pixel_id');
	$payment_id = $payment_info['id'];
	$status = $pagstar_get['status'];		
	$pedido_id = $pagstar_get['transaction_id'];
	$qry = $conn->query("SELECT o.status, o.product_id, o.total_amount, o.quantity, c.firstname, c.lastname, c.phone
		FROM order_list o
		INNER JOIN customer_list c ON o.customer_id = c.id
		WHERE o.id = '{$pedido_id}'");

	if ($qry->num_rows > 0) {
		$row = $qry->fetch_assoc();
		$status_order = $row['status'];
		$product_id = $row['product_id'];
		$quantity = $row['quantity'];
		$total_amount = $row['total_amount'];
		$firstname = $row['firstname'];
		$lastname = $row['lastname'];
		$phone = '55'.$row['phone'].'';
	}

	$product_list = $conn->query("
		SELECT pending_numbers, paid_numbers
		FROM product_list
		WHERE id = '{$product_id}'
		");

	if ($product_list->num_rows > 0) {
		$row = $product_list->fetch_assoc();
		$pendingNumbers = $row['pending_numbers'];
		$updatePending = $pendingNumbers - $quantity;
		$paidNumbers = $row['paid_numbers'];
		$updatePaid = $paidNumbers + $quantity;			
	}

	if($status == 'approved'){	
		if($status_order == '1'){
				#Define o pedido como pago
			date_default_timezone_set('America/Sao_Paulo');
			$payment_date = date('Y-m-d H:i:s');
			$sql_ol = "UPDATE order_list SET status = '2', date_updated = '{$payment_date}', whatsapp_status = '' WHERE id = '{$pedido_id}'";
			$conn->query($sql_ol);

				#Atualiza quantidade de números pendentes e números pagos do sorteio
			$sql_pl = "UPDATE product_list SET pending_numbers = '{$updatePending}', paid_numbers = '{$updatePaid}' WHERE id = '{$product_id}'";
			$conn->query($sql_pl);
			#PIXEL AUTOMÁTICO
			if($enable_pixel == 1 && !empty($facebook_pixel_id) && !empty($facebook_access_token)){
				$url = "https://graph.facebook.com/v14.0/{$facebook_pixel_id}/events?access_token={$facebook_access_token}";
				$fn = hash('sha256', $firstname);
				$ln = hash('sha256', $lastname);
				$ph = hash('sha256', $phone);
				$data = [
					[
						'event_name' => 'Purchase',                     
						'event_time' => time(),
						'user_data' => [
							'fn' => $fn,
							'ln' => $ln,
							'ph' => $ph,
							'external_id' => hash('sha256', $pedido_id),
						],
						'custom_data' => [
							'currency' => 'BRL',
							'value' => (float) number_format($total_amount, 2, '.', ''),							
						],
					]
				];
				$options = [
					CURLOPT_URL => $url,
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => json_encode([
						'data' => $data,
						//'test_event_code' => 'TEST86406'	
					]),
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_HTTPHEADER => [
						'Content-Type: application/json',
					],
				];

				$curl = curl_init();
				curl_setopt_array($curl, $options);
				$response = curl_exec($curl);
				curl_close($curl);

				if ($response) {
					$result = json_decode($response, true);
					echo "Evento enviado com sucesso. ID do evento: {$result['fbtrace_id']}";
				} else {
					echo "Ocorreu um erro ao enviar o evento: " . curl_error($curl);
				} 	


			}
		    #PIXEL AUTOMÁTICO


		}
	}

}
?>