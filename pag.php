<?php
function check_order_pagstar()
{




	$curl = curl_init();
	curl_setopt_array($curl, [CURLOPT_URL => 'https://api.pagstar.com/api/v2/wallet/partner/transactions/54de4d6b-d1bf-460f-b3ac-5f6fbd65a55b', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'GET', CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json',
		'User-Agent: Futurama (thomasagfranca@gmail.com)',
		'Authorization: Bearer ' . '7378603|J9h1GXhOS05eDtLHp5GDAkOXek18Bl0NYs5cCqvg'
	)]);
	$response = curl_exec($curl);
	curl_close($curl);


	$payment_info = json_decode($response, true);
	$status = $payment_info['data']['status'];
   
   echo $status;

}


check_order_pagstar();