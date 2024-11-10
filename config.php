<?php

ini_set('display_errors', 0);
error_reporting(0);

ob_start();
ini_set('date.timezone', 'America/Sao_Paulo');
date_default_timezone_set('America/Sao_Paulo');
session_start();
require_once 'initialize.php';
require_once 'classes/DBConnection.php';
require_once 'classes/SystemSettings.php';
$db = new DBConnection();
$conn = $db->conn;
function exibir_cabecalho($conn)
{
	global $_settings;
	$titulo_site = $_settings->info('name');
	$description = '';
	$image_path = '';

	if (isset($_GET['id'])) {
		$id_produto = $_GET['id'];
		$qry = $conn->query("SELECT * FROM `product_list` WHERE slug = '" . $id_produto . "'");

		if ($qry && 0 < $qry->num_rows) {
			$row = $qry->fetch_assoc();
			$nome_produto = $row['name'];
			$image_path = validate_image($row['image_path']);
			$description = $row['description'];
			$titulo_pagina = $nome_produto . ' - ' . $titulo_site;
		} else {
			$url = $_SERVER['REQUEST_URI'];

			if (strpos($url, '/compra/') !== false) {
				$titulo_pagina = 'Checkout - ' . $titulo_site;
			} else {
				$titulo_pagina = $titulo_site;
			}
		}
	} else {
		$url = $_SERVER['REQUEST_URI'];
		$titulo_pagina = $titulo_site;

		if (strpos($url, '/user/compras') !== false) {
			$titulo_pagina = 'Compras - ' . $titulo_site;
		}

		if (strpos($url, '/cadastrar') !== false) {
			$titulo_pagina = 'Faça seu cadastro - ' . $titulo_site;
		}

		if (strpos($url, '/user/atualizar-cadastro') !== false) {
			$titulo_pagina = 'Atualizar cadastro - ' . $titulo_site;
		}

		if (strpos($url, '/meus-numeros') !== false) {
			$titulo_pagina = 'Meus números - ' . $titulo_site;
		}

		if (strpos($url, '/campanhas') !== false) {
			$titulo_pagina = 'Campanhas - ' . $titulo_site;
		}

		if (strpos($url, '/concluidas') !== false) {
			$titulo_pagina = 'Concluídas - ' . $titulo_site;
		}

		if (strpos($url, '/em-breve') !== false) {
			$titulo_pagina = 'Em breve - ' . $titulo_site;
		}

		if (strpos($url, '/ganhadores') !== false) {
			$titulo_pagina = 'Ganhadores - ' . $titulo_site;
		}

		if (strpos($url, '/termos-de-uso') !== false) {
			$titulo_pagina = 'Termos de utilização - ' . $titulo_site;
		}

		if (strpos($url, '/contato') !== false) {
			$titulo_pagina = 'Contato - ' . $titulo_site;
		}

		if (strpos($url, '/alterar-senha') !== false) {
			$titulo_pagina = 'Alterar senha - ' . $titulo_site;
		}

		if (strpos($url, '/recuperar-senha') !== false) {
			$titulo_pagina = 'Recuperação de senha - ' . $titulo_site;
		}
	}

	$metatags = ['og:title' => $titulo_pagina, 'og:description' => $description, 'og:image' => $image_path, 'og:image:type' => 'image/jpeg', 'og:image:width' => '', 'og:image:height' => ''];
	echo '<title>' . $titulo_pagina . '</title>' . "\n";
	echo '<meta name=\'description\' content=\'' . $description . '\'>' . "\n";

	foreach ($metatags as $key => $value) {
		echo '<meta property=\'' . $key . '\' content=\'' . $value . '\'>' . "\n";
	}
}

function formatPhoneNumber($phoneNumber)
{
	$phoneNumber = ($phoneNumber ? preg_replace('/[^0-9]/', '', $phoneNumber) : '');
	$formattedPhoneNumber = '(' . substr($phoneNumber, 0, 2) . ') ' . substr($phoneNumber, 2, 5) . '-' . substr($phoneNumber, 7);
	return $formattedPhoneNumber;
}

function redirect($url = '')
{
	if (!empty($url)) {
		echo '<script>location.href="' . BASE_URL . $url . '"</script>';
	}
}

function drope_format_luck_numbers($client_lucky_numbers, $raffle_total_numbers, $class, $opt, $type_of_draw)
{
	$bichos = [];

	if ($type_of_draw == 3) {
		$bichos = ['00' => 'Avestruz', '01' => 'Águia', '02' => 'Burro', '03' => 'Borboleta', '04' => 'Cachorro', '05' => 'Cabra', '06' => 'Carneiro', '07' => 'Camelo', '08' => 'Cobra', '09' => 'Coelho', 10 => 'Cavalo', 11 => 'Elefante', 12 => 'Galo', 13 => 'Gato', 14 => 'Jacar', 15 => 'Leão', 16 => 'Macaco', 17 => 'Porco', 18 => 'Pavão', 19 => 'Peru', 20 => 'Touro', 21 => 'Tigre', 22 => 'Urso', 23 => 'Veado', 24 => 'Vaca'];
	}

	if ($type_of_draw == 4) {
		$bichos = ['00' => 'Avestruz M1', '01' => 'Avestruz M2', '02' => 'guia M1', '03' => 'guia M2', '04' => 'Burro M1', '05' => 'Burro M2', '06' => 'Borboleta M1', '07' => 'Borboleta M2', '08' => 'Cachorro M1', '09' => 'Cachorro M2', 10 => 'Cabra M1', 11 => 'Cabra M2', 12 => 'Carneiro M1', 13 => 'Carneiro M2', 14 => 'Camelo M1', 15 => 'Camelo M2', 16 => 'Cobra M1', 17 => 'Cobra M2', 18 => 'Coelho M1', 19 => 'Coelho M2', 20 => 'Cavalo M1', 21 => 'Cavalo M2', 22 => 'Elefante M1', 23 => 'Elefante M2', 24 => 'Galo M1', 25 => 'Galo M2', 26 => 'Gato M1', 27 => 'Gato M2', 28 => 'Jacaré M1', 29 => 'Jacaré M2', 30 => 'Leão M1', 31 => 'Leão M2', 32 => 'Macaco M1', 33 => 'Macaco M2', 34 => 'Porco M1', 35 => 'Porco M2', 36 => 'Pavão M1', 37 => 'Pavão M2', 38 => 'Peru M1', 39 => 'Peru M2', 40 => 'Touro M1', 41 => 'Touro M2', 42 => 'Tigre M1', 43 => 'Tigre M2', 44 => 'Urso M1', 45 => 'Urso M2', 46 => 'Veado M1', 47 => 'Veado M2', 48 => 'Vaca M1', 49 => 'Vaca M2'];
	}

	if ($client_lucky_numbers) {
		$client_lucky_numbers = explode(',', $client_lucky_numbers);
		$result = '';
		sort($client_lucky_numbers);

		foreach ($client_lucky_numbers as $client_lucky_number) {
			if (!empty($client_lucky_number)) {
				if (($type_of_draw == 3) || $type_of_draw == 4) {
					$bicho_name = $bichos[$client_lucky_number];

					if ($class === false) {
						$result .= '' . $bicho_name . ', ';
					} else {
						$result .= '' . $bicho_name . '<span class="comma-hide">, </span>';
					}
				} else {
					$output = (($type_of_draw == 3) || $type_of_draw == 4 ? $bichos[$client_lucky_number] : $client_lucky_number);

					if ((bool) $opt) {
						$result .= '' . $output . ', ';
					} else {
						$result .= '' . $output . ', ';
					}
				}
			}
		}
	} else {
		$result = '...';
	}

	return $result;
}

function drope_format_luck_numbers_dashboard($client_lucky_numbers, $raffle_total_numbers, $class, $opt, $type_of_draw)
{
	$bichos = [];

	if ($type_of_draw == 3) {
		$bichos = ['00' => 'Avestruz', '01' => 'guia', '02' => 'Burro', '03' => 'Borboleta', '04' => 'Cachorro', '05' => 'Cabra', '06' => 'Carneiro', '07' => 'Camelo', '08' => 'Cobra', '09' => 'Coelho', 10 => 'Cavalo', 11 => 'Elefante', 12 => 'Galo', 13 => 'Gato', 14 => 'Jacar', 15 => 'Leão', 16 => 'Macaco', 17 => 'Porco', 18 => 'Pavão', 19 => 'Peru', 20 => 'Touro', 21 => 'Tigre', 22 => 'Urso', 23 => 'Veado', 24 => 'Vaca'];
	}

	if ($type_of_draw == 4) {
		$bichos = ['00' => 'Avestruz M1', '01' => 'Avestruz M2', '02' => 'Águia M1', '03' => 'Águia M2', '04' => 'Burro M1', '05' => 'Burro M2', '06' => 'Borboleta M1', '07' => 'Borboleta M2', '08' => 'Cachorro M1', '09' => 'Cachorro M2', 10 => 'Cabra M1', 11 => 'Cabra M2', 12 => 'Carneiro M1', 13 => 'Carneiro M2', 14 => 'Camelo M1', 15 => 'Camelo M2', 16 => 'Cobra M1', 17 => 'Cobra M2', 18 => 'Coelho M1', 19 => 'Coelho M2', 20 => 'Cavalo M1', 21 => 'Cavalo M2', 22 => 'Elefante M1', 23 => 'Elefante M2', 24 => 'Galo M1', 25 => 'Galo M2', 26 => 'Gato M1', 27 => 'Gato M2', 28 => 'Jacaré M1', 29 => 'Jacar M2', 30 => 'Leão M1', 31 => 'Leão M2', 32 => 'Macaco M1', 33 => 'Macaco M2', 34 => 'Porco M1', 35 => 'Porco M2', 36 => 'Pavo M1', 37 => 'Pavão M2', 38 => 'Peru M1', 39 => 'Peru M2', 40 => 'Touro M1', 41 => 'Touro M2', 42 => 'Tigre M1', 43 => 'Tigre M2', 44 => 'Urso M1', 45 => 'Urso M2', 46 => 'Veado M1', 47 => 'Veado M2', 48 => 'Vaca M1', 49 => 'Vaca M2'];
	}

	if ($client_lucky_numbers) {
		$client_lucky_numbers = explode(',', $client_lucky_numbers);
		$result = '';
		sort($client_lucky_numbers);

		foreach ($client_lucky_numbers as $client_lucky_number) {
			if (!empty($client_lucky_number)) {
				if (($type_of_draw == 3) || $type_of_draw == 4) {
					$bicho_name = $bichos[$client_lucky_number];

					if ($class === false) {
						$result .= '' . $bicho_name . ', ';
					} else {
						$result .= '' . $bicho_name . '<span class="comma-hide">, </span>';
					}
				} else {
					$output = (($type_of_draw == 3) || $type_of_draw == 4 ? $bichos[$client_lucky_number] : $client_lucky_number);

					if ((bool) $opt) {
						$result .= '<a class="alert-success" style="border-radius: 5px !important; display: inline-block; padding: 5px; border-radius:2px; margin: 4px;">' . $output . '</a>';
					} else {
						$result .= '' . $output . ',';
					}
				}
			}
		}
	} else {
		$result = '...';
	}

	return $result;
}

function drope_send_whatsapp($order, $code, $status, $customer, $phone, $raffle, $numbers, $quantity, $total, $whatsapp_status, $type_of_draw)
{
	global $_settings;
	$siteName = $_settings->info('name');
	$siteUrl = BASE_URL;
	$numbers = drope_format_luck_numbers_dashboard($numbers, $quantity, $class = false, $opt = false, $type_of_draw);
	$btn = '';

	if ($status == 1) {
		$message = '⚠️ Olá *' . $customer . '*, evite o cancelamento da sua reserva, o prximo ganhador pode ser você. ' . "\r\n\r\n" . '    ------------------------------------' . "\r\n" . '    *CAMPANHA:* ' . $raffle . "\r\n" . '    *NÚMERO(S):* ' . $numbers . "\r\n" . '    *VALOR TOTAL:* R$ ' . $total . "\r\n" . '    *STATUS*:  PENDENTE' . "\r\n" . '    ------------------------------------' . "\r\n\r\n" . '    *Para pagar agora, clique no link abaixo* ⤵' . "\r\n" . '    ' . $siteUrl . 'compra/' . md5($code) . "\r\n\r\n" . '   _Só ganha quem joga!_' . "\r\n\r\n" . '   *' . $siteName . '*' . "\r\n" . ' ';
		$text = urlencode($message);
	}

	if ($status == 2) {
		$message = 'Oii *' . $customer . '*, seu pagamento foi confirmado com sucesso! ✅' . "\r\n\r\n" . '    ------------------------------------' . "\r\n" . '    *CAMPANHA:* ' . $raffle . "\r\n" . '    *NMERO(S):* ' . $numbers . "\r\n" . '    *VALOR TOTAL:* R$ ' . $total . "\r\n" . '    *STATUS:*  PAGO' . "\r\n" . '    ------------------------------------' . "\r\n\r\n" . '    _Boa Sorte!_ 🏽' . "\r\n\r\n" . '    *' . $siteName . '*' . "\r\n" . '    ';
		$text = urlencode($message);
	}

	if ($status == 3) {
		$message = ' RESERVA CANCELADA' . "\r\n" . '            ' . "\r\n" . '    Olá *' . $customer . '*, a reserva *#' . $order . '*, da campanha ' . $raffle . ', *foi cancelada por no ser paga no prazo*.' . "\r\n\r\n" . '    🚨 O número/bicho desta reserva foi novamente disponibilizado automaticamente na plataforma. ' . "\r\n\r\n" . '    _Atenciosamente,_' . "\r\n" . ' ' . "\r\n" . '    *' . $siteName . '*' . "\r\n" . '    ';
		$text = urlencode($message);
	}

	$btn = '<a class="send-whatsapp" data-post-id="' . $order . '" href="https://api.whatsapp.com/send?phone=55' . $phone . '&text=' . $text . '" target="_blank"><img src="' . $siteUrl . 'admin/assets/img/whatsapp.png" style="height: 30px"></a>';

	if ($whatsapp_status) {
		$btn = '<a class="send-whatsapp" data-post-id="' . $order . '" href="https://api.whatsapp.com/send?phone=55' . $phone . '&text=' . $text . '" target="_blank"><img src="' . $siteUrl . 'admin/assets/img/whatsapp-sent.png" style="height: 30px"></a>';
	}

	return $btn;
}

function slugify($text, $length = NULL)
{
	$replacements = ['<' => '', '>' => '', '-' => ' ', '&' => '', '"' => '', 'À' => 'A', 'Á' => 'A', '' => 'A', 'Ã' => 'A', 'Ä' => 'A', '' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae', 'Ç' => 'C', '\'' => '', 'Ć' => 'C', '' => 'C', 'Ĉ' => 'C', '' => 'C', 'Ď' => 'D', 'Đ' => 'D', 'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', '' => 'E', '' => 'E', '' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', '' => 'G', 'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', '' => 'I', 'Į' => 'I', 'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', '' => 'L', '' => 'L', '' => 'L', '' => 'L', 'Ŀ' => 'L', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N', 'Ņ' => 'N', 'Ŋ' => 'N', '' => 'O', 'Ó' => 'O', '' => 'O', '' => 'O', 'Ö' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', '' => 'O', 'Ŏ' => 'O', '' => 'OE', 'Ŕ' => 'R', '' => 'R', '' => 'R', 'Ś' => 'S', '' => 'S', '' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', '' => 'T', '' => 'T', '' => 'U', 'Ú' => 'U', '' => 'U', 'Ü' => 'Ue', 'Ū' => 'U', '' => 'U', 'Ű' => 'U', '' => 'U', 'Ũ' => 'U', '' => 'U', 'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', '' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z', 'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', '' => 'a', '' => 'a', 'ã' => 'a', 'ä' => 'ae', 'å' => 'a', '' => 'a', 'ą' => 'a', 'ă' => 'a', 'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', '' => 'c', '' => 'd', 'đ' => 'd', 'ð' => 'd', '' => 'e', 'é' => 'e', 'ê' => 'e', '' => 'e', 'ē' => 'e', 'ę' => 'e', '' => 'e', '' => 'e', 'ė' => 'e', '' => 'f', 'ĝ' => 'g', '' => 'g', 'ġ' => 'g', '' => 'g', 'ĥ' => 'h', '' => 'h', 'ì' => 'i', 'í' => 'i', '' => 'i', '' => 'i', 'ī' => 'i', 'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', '' => 'ij', 'ĵ' => 'j', '' => 'k', 'ĸ' => 'k', 'ł' => 'l', '' => 'l', 'ĺ' => 'l', 'ļ' => 'l', '' => 'l', '' => 'n', 'ń' => 'n', 'ň' => 'n', '' => 'n', '' => 'n', 'ŋ' => 'n', 'ò' => 'o', '' => 'o', '' => 'o', 'õ' => 'o', 'ö' => 'oe', '' => 'o', '' => 'o', 'ő' => 'o', 'ŏ' => 'o', '' => 'oe', '' => 'r', 'ř' => 'r', '' => 'r', 'š' => 's', 'ś' => 's', '' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'ue', 'ū' => 'u', 'ů' => 'u', '' => 'u', 'ŭ' => 'u', 'ũ' => 'u', '' => 'u', '' => 'w', 'ý' => 'y', 'ÿ' => 'y', 'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'α' => 'a', 'ß' => 'ss', 'ẞ' => 'b', '' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', '' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', '' => 'I', '' => 'Y', 'К' => 'K', 'Л' => 'L', '' => 'M', 'Н' => 'N', '' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', '' => 'U', 'Ф' => 'F', '' => 'H', '' => 'C', 'Ч' => 'CH', '' => 'SH', 'Щ' => 'SCH', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', '' => 'YU', '' => 'YA', 'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', '' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', '' => 'i', '' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', '' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', '' => 's', 'т' => 't', 'у' => 'u', '' => 'f', 'х' => 'h', 'ц' => 'c', '' => 'ch', '' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', '' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', '.' => '-', '' => '-eur-', '$' => '-usd-'];
	$keys = array_keys($replacements);
	$values = array_values($replacements);
	$text = str_replace($keys, $values, $text);
	$text = preg_replace('~[^\\pL\\d.]+~u', '-', $text);
	$text = preg_replace('~[^-\\w.]+~', '-', $text);
	$text = trim($text, '-');
	$text = preg_replace('~-+~', '-', $text);
	$text = strtolower($text);
	if (isset($length) && $length < strlen($text)) {
		$text = rtrim(substr($text, 0, $length), '-');
	}

	return $text;
}

function validate_image($file)
{
	global $_settings;

	if (!empty($file)) {
		$ex = explode('?', $file);
		$file = $ex[0];
		$ts = (isset($ex[1]) ? '?' . $ex[1] : '');

		if (is_file(BASE_APP . $file)) {
			return BASE_URL . $file . $ts;
		} else {
			return BASE_URL . 'assets/img/no_image.jpg';
		}
	} else {
		return BASE_URL . 'assets/img/no_image.jpg';
	}
}

function format_num($number = '', $decimal = '', $decimalSeparator = ',', $thousandsSeparator = '.')
{
	if (is_numeric($number)) {
		$ex = explode('.', $number);
		$decLen = (isset($ex[1]) ? strlen($ex[1]) : 0);

		if (is_numeric($decimal)) {
			return number_format($number, $decimal, $decimalSeparator, $thousandsSeparator);
		} else {
			return number_format($number, $decLen, $decimalSeparator, $thousandsSeparator);
		}
	} else {
		return 'Invalid Input';
	}
}

function show_winner_name($draw_number, $winner_data)
{
	global $conn;
	$draw_number = $conn->real_escape_string($draw_number);
	$sql = "
            SELECT c.firstname, c.lastname, c.email, c.phone
            FROM order_list o
            INNER JOIN customer_list c ON o.customer_id = c.id
            WHERE o.order_numbers LIKE CONCAT('%,', '$draw_number', ',%')
            OR o.order_numbers LIKE CONCAT('$draw_number', ',%')
            OR o.order_numbers LIKE CONCAT('%,', '$draw_number')
            OR o.order_numbers = '$draw_number'
            LIMIT 1
        ";

	$result = $conn->query($sql);
	if ($result && 0 < $result->num_rows) {
		$row = $result->fetch_assoc();
		$firstname = $row['firstname'];
		$lastname = $row['lastname'];
		$email = $row['email'];
		$phone = $row['phone'];
		echo '' . $firstname . ' ' . $lastname . '';

		if ((bool) $winner_data) {
			echo '<br>';
			echo '<strong>Cota:</strong> ' . $draw_number . '<br>';

			if ($email) {
				echo '<strong>Email:</strong> <span class="view-email">' . $email . '</span><svg id="view-email" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">' . "\r\n" . '                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>' . "\r\n" . '                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>' . "\r\n" . '                </svg><br>';
			}

?>
			<strong>Telefone:</strong> <span class="view-phone"><?= formatphonenumber($phone) ?></span>
			<svg id="view-phone" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
				<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
				<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
			</svg><br>
<?php

		}
	} else {
		echo 'Nenhum ganhador encontrado.';
	}
}

function date_brazil($format, $timestamp = NULL)
{
	$timestamp = ($timestamp ? $timestamp : 'now');
	$timestamp = (is_numeric($timestamp) ? date('Y-m-d H:i:s', $timestamp) : $timestamp);
	$date = new DateTime($timestamp);
	$timezone = new DateTimeZone('America/Sao_Paulo');
	$date->setTimezone($timezone);
	return $date->format($format);
}

function mercadopago_generate_pix($order_id, $amount, $client_name, $client_email, $order_expiration)
{
	global $_settings;
	global $conn;
	require_once 'gateway/mercadopago/vendor/autoload.php';
	$access_token = $_settings->info('mercadopago_access_token');
	$minutes_pix_expiration = $order_expiration;
	$amount = number_format((float) $amount, 2, '.', '');

	if (!$client_email) {
		$client_email = 'no-reply@dropestore.com';
	}

	MercadoPago\SDK::setAccessToken($access_token);
	$payment = new MercadoPago\Payment();
	$payment->transaction_amount = $amount;
	$payment->description = 'Pedido #' . $order_id;
	$payment->payment_method_id = 'pix';
	$payment->payer = ['email' => $client_email, 'first_name' => $client_name];
	$payment->notification_url = BASE_URL . 'webhook.php?notify=mercadopago';
	$payment->external_reference = $order_id;

	if ($minutes_pix_expiration) {
		$payment->date_of_expiration = date_brazil('Y-m-d\\TH:i:s.vP', time() + ($minutes_pix_expiration * 60));
	}

	$payment->setCustomHeader('X-Idempotency-Key', uniqid());
	$payment->save();
	$pix_qrcode = $payment->point_of_interaction->transaction_data->qr_code_base64;
	$pix_code = $payment->point_of_interaction->transaction_data->qr_code;
	$id_mp = $payment->id;
	$payment_method = 'MercadoPago';
	$sql = "UPDATE order_list
        SET payment_method = '$payment_method',
            pix_code = '$pix_code',
            pix_qrcode = '$pix_qrcode',
            id_mp = '$id_mp',
            order_expiration = '$order_expiration'
        WHERE id = $order_id";


	if ($conn->query($sql)) {
	}
}

function drope_gn_access_token($api_pix_certificate, $client_id, $client_secret)
{
	$curl = curl_init();
	$authorization = base64_encode($client_id . ':' . $client_secret);
	curl_setopt_array($curl, [
		CURLOPT_URL => 'https://api-pix.gerencianet.com.br/oauth/token',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => '{"grant_type": "client_credentials"}',
		CURLOPT_SSLCERT => $api_pix_certificate,
		CURLOPT_SSLCERTPASSWD => '',
		CURLOPT_HTTPHEADER => ['Authorization: Basic ' . $authorization, 'Content-Type: application/json']
	]);
	$response = curl_exec($curl);
	curl_close($curl);
	return json_decode($response, true);
}

function drope_txid($quantity = 35)
{
	$txid = 'drope' . strval(time());
	$quantity = ((26 <= $quantity) && $quantity <= 35 ? $quantity : 35);
	$chars_str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$chars_len = strlen($chars_str);

	for ($j = 0; $j < $quantity; ++$j) {
		if ($quantity <= strlen($txid)) {
			break;
		}

		$current_char = rand(0, $chars_len - 1);
		$txid .= $chars_str[$current_char];
	}

	return $txid;
}

function drope_gn_emite_pix($pix_url_cob, $api_pix_certificate, $body, $tokenType, $accessToken)
{
	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_URL => $pix_url_cob,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'PUT',
		CURLOPT_SSLCERT => $api_pix_certificate,
		CURLOPT_SSLCERTPASSWD => '',
		CURLOPT_POSTFIELDS => json_encode($body),
		CURLOPT_HTTPHEADER => ['authorization: ' . $tokenType . ' ' . $accessToken, 'Content-Type: application/json']
	]);
	$dadosPix = json_decode(curl_exec($curl), true);
	curl_close($curl);
	return $dadosPix;
}

function drope_gn_setwebhook($tokenType, $client_chave_pix, $accessToken, $api_pix_certificate)
{
	$webhook_url = BASE_URL . 'webhook.php?notify=gerencianet';
	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_URL => 'https://api-pix.gerencianet.com.br/v2/webhook/' . $client_chave_pix,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'PUT',
		CURLOPT_SSLCERT => $api_pix_certificate,
		CURLOPT_SSLCERTPASSWD => '',
		CURLOPT_POSTFIELDS => '{' . "\r\n" . '            "webhookUrl": "' . $webhook_url . '"' . "\r\n" . '        }',
		CURLOPT_HTTPHEADER => ['authorization: ' . $tokenType . ' ' . $accessToken, 'x-skip-mtls-checking: true', 'Content-Type: application/json']
	]);
	$response = json_decode(curl_exec($curl), true);
	curl_close($curl);
	return $response;
}

function drope_gn_get_qrcode($loc_id, $tokenType, $accessToken, $api_pix_certificate)
{
	$curl = curl_init();
	curl_setopt_array($curl, [
		CURLOPT_URL => 'https://api-pix.gerencianet.com.br/v2/loc/' . $loc_id . '/qrcode',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_SSLCERT => $api_pix_certificate,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => ['authorization: ' . $tokenType . ' ' . $accessToken]
	]);
	$response = json_decode(curl_exec($curl), true);
	curl_close($curl);
	return $response;
}

function gerencianet_generate_pix($order_id, $amount, $client_name, $client_email, $order_expiration)
{
	global $_settings;
	global $conn;
	$api_pix_certificate = $_SERVER['DOCUMENT_ROOT'] . '/pagamentos.pem';
	$client_id = $_settings->info('gerencianet_client_id');
	$client_secret = $_settings->info('gerencianet_client_secret');
	$client_chave_pix = $_settings->info('gerencianet_pix_key');
	$dadosToken = drope_gn_access_token($api_pix_certificate, $client_id, $client_secret);
	$tokenType = $dadosToken['token_type'];
	$accessToken = $dadosToken['access_token'];
	$txID = drope_txid();
	$webhook_url = drope_gn_setwebhook($tokenType, $client_chave_pix, $accessToken, $api_pix_certificate);
	$pix_url_cob = 'https://api-pix.gerencianet.com.br/v2/cob/' . $txID;
	$pix_expire = $order_expiration;
	$pix_expire_time = $pix_expire * 60;
	$amount = number_format((float) $amount, 2, '.', '');
	if (!$pix_expire || $pix_expire == '0') {
		$pix_expire_time = 260000;
	}

	$body = [
		'calendario'         => ['expiracao' => $pix_expire_time],
		'valor'              => ['original' => $amount],
		'chave'              => $client_chave_pix,
		'solicitacaoPagador' => 'Reserva #' . $order_id,
		'infoAdicionais'     => [
			['nome' => 'Pedido', 'valor' => 'Reserva #' . $order_id]
		]
	];
	$dados = drope_gn_emite_pix($pix_url_cob, $api_pix_certificate, $body, $tokenType, $accessToken);
	$loc_id = $dados['loc']['id'];
	$pix = drope_gn_get_qrcode($loc_id, $tokenType, $accessToken, $api_pix_certificate);
	$pix_code = $pix['qrcode'];
	$pix_qrcode = $pix['imagemQrcode'];
	$txid = $dados['txid'];
	$payment_method = 'Gerencianet';
	$sql = "UPDATE order_list
        SET payment_method = '$payment_method',
            pix_code = '$pix_code',
            pix_qrcode = '$pix_qrcode',
            order_expiration = '$order_expiration',
            txid = '$txid'
        WHERE id = $order_id";


	if ($conn->query($sql)) {
	}
}

function decode_brcode($brcode)
{
	$n = 0;

	while ($n < strlen($brcode)) {
		$codigo = substr($brcode, $n, 2);
		$n += 2;
		$tamanho = (int) substr($brcode, $n, 2);

		if (!is_numeric($tamanho)) {
			return false;
		}

		$n += 2;
		$valor = substr($brcode, $n, $tamanho);
		$n += $tamanho;
		if (preg_match('/^[0-9]{4}.+$/', $valor) && $codigo != 54) {
			$bug_fix = (isset($retorno[26]['01']) ? $retorno[26]['01'] : '');

			if (is_array($bug_fix)) {
				$extrai = strstr($brcode, 'PIX');
				$extrai = substr($extrai, 7);
				$extrai = substr($extrai, 0, 36);
				$retorno[26]['01'] = $extrai;
				unset($retorno[26][26]);
			}

			$retorno[$codigo] = decode_brcode($valor);
			continue;
		}

		$retorno[$codigo] = (string) $valor;
	}

	return $retorno;
}

 function drope_paggue_get_info($info)
    {

        global $_settings;
        $client_key = $_settings->info('paggue_client_key');
        $client_secret = $_settings->info('paggue_client_secret');

        $access_token = '';
        $curl = curl_init();
        $data = ['client_key' => $client_key, 'client_secret' => $client_secret];
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://ms.paggue.io/auth/v1/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($data)
        ]);
        $response = curl_exec($curl);
        $get = json_decode($response, true);
        curl_close($curl);

        if ($info == 'access_token') {
            $info = $get['access_token'];
        }

        if ($info == 'company_id') {
            $info = $get['user']['companies'][0]['id'];
        }

        return $info;
    }

function drope_paggue_create_order($order_user, $order_item, $order_amount, $order_id)
    {
        global $_settings;
        $client_key = $_settings->info('paggue_client_key');
        $client_secret = $_settings->info('paggue_client_secret');

        $curl = curl_init();
        $data = ['payer_name' => $order_user, 'amount' => $order_amount, 'external_id' => $order_id, 'description' => $order_item];

        $signature = hash_hmac('sha256', json_encode($data), $client_secret);
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . drope_paggue_get_info('access_token'),
            'X-Company-ID: ' . drope_paggue_get_info('company_id'),
            'Signature: ' . $signature
        ];
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://ms.paggue.io/cashin/api/billing_order',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers
        ]);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  // Get HTTP status code
        curl_close($curl);

        $get = json_decode($response, true);
        $data = [];
        if (isset($get['payment']) && isset($get['hash'])) {
            $data = ['pix' => $get['payment'], 'hash' => $get['hash']];
        } else {
            $data = ['ERRO - PIX INDISPONÍVEL'];
        }
        return $data;
    }



function drope_pagstar_create_order($client_name, $order_item, $amount, $order_id)
{


	global $_settings;
	$client_key = $_settings->info('pagstar_client_key');
	$client_secret = $_settings->info('pagstar_client_secret');

	$curl = curl_init();

	$body = [
		"value" => $amount,
		"name" => $client_name,
		"document" => '525.291.198-30',
		"tenant_id" => $client_key,
		"expiration" => 500,
		"transaction_id" => $order_id,

	];

	curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://api.pagstar.com/api/v2/wallet/partner/transactions/generate-anonymous-pix',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',

		CURLOPT_POSTFIELDS => json_encode($body),

		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'User-Agent: Futurama (thomasagfranca@gmail.com)',
			'Authorization: Bearer ' . $client_secret
		),
	));


	$response = curl_exec($curl);


	$data = json_decode($response, true);
	$qrcode = $data['data']['qr_code_url'];

	$pix = $data['data']['pix_key'];
$external_reference =$data['data']['external_reference'];



	curl_close($curl);


	return json_encode(['pix' => $pix, 'qrcode' => $qrcode ,'external_reference' => $external_reference]);
}


 function paggue_generate_pix($oid, $total_amount, $customer_name, $customer_email, $order_expiration)
    {
        global $conn;
        $order_user = $customer_name;


        require_once('gateway/phpqrcode/qrlib.php');
        require_once('gateway/funcoes_pix.php');

        $order_id = $oid;
        $order_item = $order_id;
        $tax = 2;
        $order_amount = drope_normalize_price($total_amount);
        $order_amount = number_format($order_amount, 2, '.', '');

        if ($tax) {
            $percentage = $order_amount * ($tax / 100);
            $percentage = $percentage * 100;
        }

        $order_amount = $order_amount * 100;
        $order_amount = (int) $order_amount;

        if ($tax) {
            $order_amount = $order_amount + (int) $percentage;
        }

        $data = drope_paggue_create_order($order_user, $order_item, $order_amount, $order_id);
        $pix_code = $data['pix'];
        $hash = $data['hash'];
        $px = decode_brcode($pix_code);
        $monta_pix = montaPix($px);
        ob_start();
        QRCode::png($monta_pix, NULL, 'M', 5);
        $imageString = base64_encode(ob_get_contents());
        ob_end_clean();
        $pix_qrcode = $imageString;
        $payment_method = 'Paggue';
        
        
        // Assume $db is your database connection object
        $sql = 'UPDATE order_list' . "\r\n" . 'SET payment_method = \'' . $payment_method . '\', pix_code = \'' . $pix_code . '\', pix_qrcode = \'' . $pix_qrcode . '\', order_expiration = \'' . 15 . '\', id_mp = \'' . $hash . '\'' . "\r\n" . 'WHERE id = ' . $order_id;

        $result = $conn->query($sql);

        if ($result) {
            return true;
        }
    }


function drope_normalize_price($price)
{
	$price = trim(preg_replace('`(R|\\$|\\x20)`i', '', $price));

	if (preg_match('`^([0-9]+(?:\\.[0-9]+)+)\\,([0-9]+)$`', $price, $match)) {
		return str_replace('.', '', $match[1]) . '.' . $match[2];
	}

	if (preg_match('`^([0-9]+)\\,([0-9]+)$`', $price, $match)) {
		return $match[1] . '.' . $match[2];
	}

	if (preg_match('`^([0-9]+(?:\\,[0-9]+)+)\\.([0-9]+)$`', $price, $match)) {
		return str_replace(',', '', $match[1]) . '.' . $match[2];
	}

	if (preg_match('`^([0-9]+)\\.([0-9]+)$`', $price, $match)) {
		return $match[1] . '.' . $match[2];
	}

	if (preg_match('`^([0-9]+)$`', $price, $match)) {
		return $match[1];
	}

	$price = preg_replace('`(\\.|\\,)`', '', $price);

	if (preg_match('`^([0-9]+)$`', $price, $match)) {
		return $match[1];
	}

	return false;
}

function pagstar_generate_pix($oid, $total_amount, $customer_name, $customer_email, $order_expiration)
{
	global $conn;
	require_once 'gateway/phpqrcode/qrlib.php';
	require_once 'gateway/funcoes_pix.php';
	global $_settings;

	$order_id = $oid;
	$order_item = $order_id;
	$order_amount = drope_normalize_price($total_amount);
	$order_amount = number_format($order_amount, 2, '.', '');


	$order_user = $customer_name;
	$pxdata = drope_pagstar_create_order($order_user, $order_item, $order_amount, $order_id);
	$px = json_decode($pxdata, true);
	$pix_code = $px['pix'];
	$pix_qrcode = $px['qrcode'];
	
$external_reference=$px['external_reference'];






	$payment_method = 'Pagstar';
	$sql = "UPDATE order_list
        SET payment_method = '$payment_method',
            pix_code = '$pix_code',
            pix_qrcode = '$pix_qrcode',
            order_expiration = '$order_expiration',
            id_mp = '$external_reference'
        WHERE id = $order_id";


	if ($conn->query($sql)) {
	}
}





function blockHTML($replStr)
{
	return html_entity_decode($replStr);
}

function send_event_pixel($event, $dados)
{
	global $_settings;
	$enable_pixel = $_settings->info('enable_pixel');
	$facebook_access_token = $_settings->info('facebook_access_token');
	$facebook_pixel_id = $_settings->info('facebook_pixel_id');
	if (($enable_pixel == 1) && !empty($facebook_pixel_id) && !empty($facebook_access_token)) {
		$url = 'https://graph.facebook.com/v18.0/' . $facebook_pixel_id . '/events?access_token=' . $facebook_access_token;
		$fn = hash('sha256', $dados['first_name']);
		$ln = hash('sha256', $dados['last_name']);
		$ph = hash('sha256', $dados['phone']);

		switch ($event) {
			case 'Purchase':
				$data = [
					[
						'event_name'    => $event,
						'event_time'    => time(),
						'action_source' => 'website',
						'user_data'     => [
							'ph'          => [$ph],
							'fn'          => [$fn],
							'ln'          => [$ln],
							'external_id' => [hash('sha256', $dados['id'])]
						],
						'custom_data'   => ['currency' => 'BRL', 'value' => (float) number_format($dados['total_amount'], 2, '.', '')]
					]
				];
				break;
			case 'InitiateCheckout':
				$data = [
					[
						'event_name'    => $event,
						'event_time'    => time(),
						'action_source' => 'website',
						'user_data'     => [
							'ph'          => [$ph],
							'fn'          => [$fn],
							'ln'          => [$ln],
							'external_id' => [hash('sha256', $dados['id'])]
						],
						'custom_data'   => ['currency' => 'BRL', 'value' => (float) number_format($dados['total_amount'], 2, '.', '')]
					]
				];
				break;
			case 'CompleteRegistration':
				$data = [
					[
						'event_name'    => $event,
						'event_time'    => time(),
						'action_source' => 'website',
						'user_data'     => [
							'ph'          => [$ph],
							'fn'          => [$fn],
							'ln'          => [$ln],
							'external_id' => [hash('sha256', $dados['customer_id'])]
						]
					]
				];
				break;
			default:
				$data = [];
				break;
		}

		$options = [
			CURLOPT_URL => $url,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode(['data' => $data]),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => ['Content-Type: application/json']
		];
		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$response = curl_exec($curl);
		curl_close($curl);
	}
}

function uniqidReal($lenght = 13)
{
	if (function_exists('random_bytes')) {
		$bytes = random_bytes(ceil($lenght / 2));
	} else if (function_exists('openssl_random_pseudo_bytes')) {
		$bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
	} else {
		throw new PHPMailer\PHPMailer\Exception('no cryptographically secure random function available');
	}

	return substr(bin2hex($bytes), 0, $lenght);
}

function revert_product($id)
{
	global $_settings;
	$query = $_settings->conn->query("SELECT status, pending_numbers, paid_numbers, qty_numbers FROM product_list WHERE id = '$id'");


	if (0 < $query->num_rows) {
		$row = $query->fetch_assoc();
		$status = $row['status'];
		$pending_numbers = $row['pending_numbers'];
		$paid_numbers = $row['paid_numbers'];
		$qty_numbers = $row['qty_numbers'];
		if ((($pending_numbers + $paid_numbers) < $qty_numbers) && 1 < $status) {
			$update = $_settings->conn->query("UPDATE product_list SET status = '1', status_display = '1' WHERE id = '$id'");
		}
	}
}

 function check_order_pg($order_id, $id_mp)
    {
        global $_settings;
        $headers = ['Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer ' . drope_paggue_get_info('access_token'), 'X-Company-ID: ' . drope_paggue_get_info('company_id')];
        $curl = curl_init();
        curl_setopt_array($curl, [CURLOPT_URL => 'https://ms.paggue.io/cashin/api/billing_order', CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'GET', CURLOPT_HTTPHEADER => $headers]);
        $response = curl_exec($curl);
        curl_close($curl);
        $payment_info = json_decode($response, true);
        $status = null;


        foreach ($payment_info['data'] as $item) {
            if ($item['external_id'] == $order_id) {
                $status = $item['status'];
                break;
            }
        }
        if ($status == 1) {
            $qry = $_settings->conn->query('SELECT o.status, o.product_id, o.total_amount, o.quantity, c.firstname, c.lastname, c.phone, o.referral_id' . "\r\n" . '            FROM order_list o' . "\r\n" . '            INNER JOIN customer_list c ON o.customer_id = c.id' . "\r\n" . '            WHERE o.id = \'' . $order_id . '\'');

            if (0 < $qry->num_rows) {
                $row = $qry->fetch_assoc();
                $product_id = $row['product_id'];
                $quantity = $row['quantity'];
                $total_amount = $row['total_amount'];
                $firstname = $row['firstname'];
                $lastname = $row['lastname'];
                $phone = '55' . $row['phone'] . '';
                $ref = $row['referral_id'];
                $order_status = $row['status'];
            }

            $product_list = $_settings->conn->query("\r\n" . '            SELECT pending_numbers, paid_numbers' . "\r\n" . '            FROM product_list' . "\r\n" . '            WHERE id = \'' . $product_id . '\'' . "\r\n" . '            ');

            if (0 < $product_list->num_rows) {
                $row = $product_list->fetch_assoc();
                $pendingNumbers = $row['pending_numbers'];
                $updatePending = $pendingNumbers - $quantity;
                $paidNumbers = $row['paid_numbers'];
                $updatePaid = $paidNumbers + $quantity;
            }

            if ($ref) {
                $referral = $_settings->conn->query('SELECT * FROM referral WHERE referral_code = \'' . $ref . '\'');

                if (0 < $referral->num_rows) {
                    $row = $referral->fetch_assoc();
                    $status_affiliate = $row['status'];
                    $percentage_affiliate = $row['percentage'];
                }
            }

            date_default_timezone_set('America/Sao_Paulo');
            $payment_date = date('Y-m-d H:i:s');
            if ($order_status == 1) {
                $sql_ol = 'UPDATE order_list SET status = \'2\', date_updated = \'' . $payment_date . '\', whatsapp_status = \'\' WHERE id = \'' . $order_id . '\' AND status = \'1\'';
                $_settings->conn->query($sql_ol);
                $sql_pl = 'UPDATE product_list SET pending_numbers = \'' . $updatePending . '\', paid_numbers = \'' . $updatePaid . '\' WHERE id = \'' . $product_id . '\' ';
                $_settings->conn->query($sql_pl);
            }
            if ($ref) {
                if ($ref) {
                    if ($status_affiliate == 1) {
                        $value = $total_amount * $percentage_affiliate;
                        $value = $value / 100;
                        $aff_sql = 'UPDATE referral SET amount_pending = amount_pending + ' . $value . ' WHERE referral_code = ' . $ref;
                        $_settings->conn->query($aff_sql);
                    }
                }
            }

            $dados = ['first_name' => $firstname, 'last_name' => $lastname, 'phone' => $phone, 'id' => $order_id, 'total_amount' => $total_amount];
            send_event_pixel('Purchase', $dados);
            order_email($_settings->info('email_purchase'), '[' . $_settings->info('name') . '] - Pagamento aprovado', $order_id);
            return 'approved';
        } else {
            return 'failed';
        }
    }

function check_order_pagstar($order_id, $id_mp)
{

	global $_settings;

	$client_secret = $_settings->info('pagstar_client_secret');


	$curl = curl_init();
	curl_setopt_array($curl, [CURLOPT_URL => 'https://api.pagstar.com/api/v2/wallet/partner/transactions/' . $id_mp, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '', CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => 'GET', CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json',
		'User-Agent: Futurama (thomasagfranca@gmail.com)',
		'Authorization: Bearer ' . $client_secret
	)]);
	$response = curl_exec($curl);
	curl_close($curl);



	$payment_info = json_decode($response, true);
	$status = $payment_info['data']['status'];


	if ($status == 1) {
		$qry = $_settings->conn->query("
		SELECT o.status, o.product_id, o.total_amount, o.quantity, c.firstname, c.lastname, c.phone, o.referral_id
		FROM order_list o
		INNER JOIN customer_list c ON o.customer_id = c.id
		WHERE o.id = '$order_id'
	  ");

		if (0 < $qry->num_rows) {
			$row = $qry->fetch_assoc();
			$product_id = $row['product_id'];
			$quantity = $row['quantity'];
			$total_amount = $row['total_amount'];
			$firstname = $row['firstname'];
			$lastname = $row['lastname'];
			$phone = '55' . $row['phone'] . '';
			$ref = $row['referral_id'];
		}

		$product_list = $_settings->conn->query("
       SELECT pending_numbers, paid_numbers
      FROM product_list
       WHERE id = '$product_id'
          ");


		if (0 < $product_list->num_rows) {
			$row = $product_list->fetch_assoc();
			$pendingNumbers = $row['pending_numbers'];
			$updatePending = $pendingNumbers - $quantity;
			$paidNumbers = $row['paid_numbers'];
			$updatePaid = $paidNumbers + $quantity;
		}

		if ($ref) {
			$referral = $_settings->conn->query("SELECT * FROM referral WHERE referral_code = '$ref'");


			if (0 < $referral->num_rows) {
				$row = $referral->fetch_assoc();
				$status_affiliate = $row['status'];
				$percentage_affiliate = $row['percentage'];
			}
		}

		corrigir_duplicidade($order_id);
		date_default_timezone_set('America/Sao_Paulo');
		$payment_date = date('Y-m-d H:i:s');
		$sql_ol = "UPDATE order_list SET status = '2', date_updated = '$payment_date', whatsapp_status = '' WHERE id = '$order_id'";

		$_settings->conn->query($sql_ol);
		$sql_pl = "UPDATE product_list SET pending_numbers = '$updatePending', paid_numbers = '$updatePaid' WHERE id = '$product_id'";

		$_settings->conn->query($sql_pl);

		if ($ref) {
			if ($ref) {
				if ($status_affiliate == 1) {
					$value = $total_amount * $percentage_affiliate;
					$value = $value / 100;
					$aff_sql = 'UPDATE referral SET amount_pending = amount_pending + ' . $value . ' WHERE referral_code = ' . $ref;
					$_settings->conn->query($aff_sql);
				}
			}
		}

	
		return 'approved';
	} else {
		return 'failed';
	}
}








function check_order_mp($order_id, $id_mp)
{
	global $_settings;
	$mercadopago_access_token = $_settings->info('mercadopago_access_token');
	$url = 'https://api.mercadopago.com/v1/payments/' . $id_mp;
	$headers = ['Accept: application/json', 'Authorization: Bearer ' . $mercadopago_access_token];
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$resposta = curl_exec($ch);
	curl_close($ch);
	$payment_info = json_decode($resposta, true);
	$status = $payment_info['status'];

	if ($status == 'approved') {
		$qry = $_settings->conn->query("
    SELECT o.status, o.product_id, o.total_amount, o.quantity, c.firstname, c.lastname, c.phone, o.referral_id
    FROM order_list o
    INNER JOIN customer_list c ON o.customer_id = c.id
    WHERE o.id = '$order_id'
");


		if (0 < $qry->num_rows) {
			$row = $qry->fetch_assoc();
			$product_id = $row['product_id'];
			$quantity = $row['quantity'];
			$total_amount = $row['total_amount'];
			$firstname = $row['firstname'];
			$lastname = $row['lastname'];
			$phone = '55' . $row['phone'] . '';
			$ref = $row['referral_id'];
		}

		$product_list = $_settings->conn->query("
    SELECT pending_numbers, paid_numbers
    FROM product_list
    WHERE id = '$product_id'
");


		if (0 < $product_list->num_rows) {
			$row = $product_list->fetch_assoc();
			$pendingNumbers = $row['pending_numbers'];
			$updatePending = $pendingNumbers - $quantity;
			$paidNumbers = $row['paid_numbers'];
			$updatePaid = $paidNumbers + $quantity;
		}

		if ($ref) {
			$referral = $_settings->conn->query("SELECT * FROM referral WHERE referral_code = '$ref'");


			if (0 < $referral->num_rows) {
				$row = $referral->fetch_assoc();
				$status_affiliate = $row['status'];
				$percentage_affiliate = $row['percentage'];
			}
		}

		corrigir_duplicidade($order_id);
		date_default_timezone_set('America/Sao_Paulo');
		$payment_date = date('Y-m-d H:i:s');
		$sql_ol = "UPDATE order_list SET status = '2', date_updated = '$payment_date', whatsapp_status = '' WHERE id = '$order_id'";

		$_settings->conn->query($sql_ol);
		$sql_pl = "UPDATE product_list SET pending_numbers = '$updatePending', paid_numbers = '$updatePaid' WHERE id = '$product_id'";

		$_settings->conn->query($sql_pl);

		if ($ref) {
			if ($ref) {
				if ($status_affiliate == 1) {
					$value = $total_amount * $percentage_affiliate;
					$value = $value / 100;
					$aff_sql = 'UPDATE referral SET amount_pending = amount_pending + ' . $value . ' WHERE referral_code = ' . $ref;
					$_settings->conn->query($aff_sql);
				}
			}
		}

		$dados = ['first_name' => $firstname, 'last_name' => $lastname, 'phone' => $phone, 'id' => $order_id, 'total_amount' => $total_amount];
		send_event_pixel('Purchase', $dados);
		order_email($_settings->info('email_purchase'), '[' . $_settings->info('name') . '] - Pagamento aprovado', $order_id);
		return 'approved';
	} else {
		return 'failed';
	}
}

function corrigir_duplicidade($oid)
{
	global $_settings;
	$order = $_settings->conn->query("SELECT o.code, o.order_numbers, p.qty_numbers, p.id AS pid
    FROM order_list o
    INNER JOIN product_list p ON o.product_id = p.id
    WHERE o.id = '$oid'");


	if (0 < $order->num_rows) {
		$row = $order->fetch_assoc();
		$qty_numbers = $row['qty_numbers'];
		$pid = $row['pid'];
		$order_numbers = $row['order_numbers'];
	}

	$orders = $_settings->conn->query("SELECT order_numbers FROM order_list WHERE status <> 3 AND product_id = '$pid'");

	$all_lucky_numbers = [];

	while ($row = $orders->fetch_assoc()) {
		$all_lucky_numbers[] = $row['order_numbers'];
	}
	$all_lucky_numbers = implode(',', $all_lucky_numbers);
	$all_lucky_numbers = explode(',', $all_lucky_numbers);
	$numeros_ja_vendidos = array_filter($all_lucky_numbers);
	$qty_numbers = $qty_numbers - 1;
	$globos = strlen($qty_numbers);
	$numeris = range(0, $qty_numbers);
	$numeris = array_map(function ($item) use ($qty_numbers, $globos) {
		return str_pad($item, max((int) $globos, strlen($qty_numbers)), '0', STR_PAD_LEFT);
	}, $numeris);
	$array_without_ja_vendidos = array_filter(array_diff($numeris, $numeros_ja_vendidos));
	shuffle($array_without_ja_vendidos);
	$numbers = explode(',', $order_numbers);
	$numbers = array_filter($numbers);

	switch (count($numbers)) {
		case 600 < count($numbers):
			$partiion = 2;
			break;
		case 1000 < count($numbers):
			$partiion = 2;
			break;
		case 1500 < count($numbers):
			$partiion = 3;
			break;
		case 2000 < count($numbers):
			$partiion = 4;
			break;
		case 2500 < count($numbers):
			$partiion = 5;
			break;
		case 3000 < count($numbers):
			$partiion = 6;
			break;
		case 4000 < count($numbers):
			$partiion = 8;
			break;
		case 5000 < count($numbers):
			$partiion = 10;
			break;
		default:
			$partiion = 1;
			break;
	}

	$cotas = partition($numbers, $partiion);

	for ($i = 0; $i < count($cotas); ++$i) {
		$numbers = $cotas[$i];
		$find_orders_query = $_settings->conn->query("SELECT * FROM order_list WHERE product_id='$pid' AND id<>'$oid' AND status<>3 AND order_numbers REGEXP '" . implode('|', $numbers) . "'");


		if (0 < $find_orders_query->num_rows) {
			$count = 0;
			$row = $find_orders_query->fetch_assoc();

			foreach ($numbers as $number) {
				$query = $_settings->conn->query("SELECT id FROM order_list WHERE product_id='$pid' AND order_numbers REGEXP '$number' AND status <> 3 AND id <> '$oid'");


				if (0 < $query->num_rows) {
					$row = $query->fetch_assoc();
					$new_number = $array_without_ja_vendidos[$count];
					$update = $_settings->conn->query("UPDATE order_list SET order_numbers = REPLACE(order_numbers, '$number', '$new_number') WHERE id = '$oid'");

					++$count;
				}
			}
		}
	}
}

function partition($list, $p)
{
	$listlen = count($list);
	$partlen = floor($listlen / $p);
	$partrem = $listlen % $p;
	$partition = [];
	$mark = 0;

	for ($px = 0; $px < $p; ++$px) {
		$incr = ($px < $partrem ? $partlen + 1 : $partlen);
		$partition[$px] = array_slice($list, $mark, $incr);
		$mark += $incr;
	}

	return $partition;
}

function order_email($message, $title, $order_id)
{
	global $_settings;
	$enable_email = $_settings->info('enable_email');

	if ($enable_email == 1) {
		$customer = $_settings->conn->query('SELECT o.product_name, o.product_id, o.status, o.order_numbers, o.total_amount, c.* FROM order_list o INNER JOIN customer_list c ON c.id = o.customer_id WHERE o.id = ' . $order_id);

		if (0 < $customer->num_rows) {
			$row = $customer->fetch_assoc();
			$customer_name = $row['firstname'] . ' ' . $row['lastname'];
			$customer_email = $row['email'];
			$product_name = $row['product_name'];
			$order_numbers = $row['order_numbers'];
			$order_total = $row['total_amount'];
			$product_id = $row['product_id'];
			$status = $row['status'];
		}

		if (!empty($message)) {
			$message = str_replace('[CAMPANHA]', $product_name, $message);
			$message = str_replace('[CLIENTE]', $customer_name, $message);
			$message = str_replace('[COTAS]', $order_numbers, $message);
			$message = str_replace('[TOTAL]', 'R$' . format_num($order_total, 2), $message);

			if (!$_settings->info('smtp_host')) {
				$headers = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";

				try {
					$mailSent = mail($customer_email, $title, $message, $headers);
					return true;
				} catch (PHPMailer\PHPMailer\Exception $e) {
					echo 'Não foi possível enviar a mensagem. Mail Error.';
					return false;
				}
			} else {
				require_once 'libs/phpmailer/src/Exception.php';
				require_once 'libs/phpmailer/src/PHPMailer.php';
				require_once 'libs/phpmailer/src/SMTP.php';
				$mail = new PHPMailer\PHPMailer\PHPMailer(true);

				try {
					$mail->isSMTP();
					$mail->SMTPOptions = [
						'ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]
					];
					$mail->SMTPAuth = true;
					$mail->Host = $_settings->info('smtp_host');
					$mail->Username = $_settings->info('smtp_user');
					$mail->Password = $_settings->info('smtp_pass');
					$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
					$mail->Port = $_settings->info('smtp_port');
					$mail->CharSet = 'UTF-8';

					if ($status == 2) {
						if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/uploads/attachment/' . $product_id . '.pdf')) {
							$file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/attachment/' . $product_id . '.pdf';
							$mail->AddAttachment($file, $product_name);
						}
					}

					$mail->setFrom($_settings->info('smtp_user'), $_settings->info('name'));
					$mail->addAddress($customer_email, $customer_name);
					$mail->isHTML(true);
					$mail->Subject = $title;
					$mail->Body = $message;
					$mail->send();
					return true;
				} catch (PHPMailer\PHPMailer\Exception $e) {
					echo 'No foi possível enviar a mensagem. Mailer Error: ' . $mail->ErrorInfo;
					return false;
				}
			}
		}
	}
}

function correct_stock($id)
{
	global $_settings;

	if (empty($id)) {
		$id = $_GET['id'];
	}

	$sql_pending = $_settings->conn->query("SELECT p.pending_numbers, SUM(o.quantity) as quantity FROM product_list as p LEFT JOIN order_list as o ON p.id = o.product_id WHERE p.id = '" . $id . "' AND o.status = '1'");

	if ($sql_pending && 0 < $sql_pending->num_rows) {
		while ($row = $sql_pending->fetch_assoc()) {
			$pl_pending = $row['pending_numbers'];
			$ol_pending = $row['quantity'];
			if (empty($ol_pending) || $ol_pending == NULL) {
				$ol_pending = 0;
			}

			if ($pl_pending != $ol_pending) {
				$update = $_settings->conn->query("UPDATE product_list SET pending_numbers = '" . $ol_pending . "' WHERE id = '" . $id . "'");


				if ($update) {
					$resp['status'] = 'success';
					continue;
				}

				$resp['status'] = 'failed';
				$resp['msg'] = $_settings->conn->error;
			}
		}
	}

	$sql_paid = $_settings->conn->query("SELECT p.paid_numbers, SUM(o.quantity) as quantity FROM product_list as p LEFT JOIN order_list as o ON p.id = o.product_id WHERE p.id = '" . $id . "' AND o.status = '2'");

	if ($sql_paid && 0 < $sql_paid->num_rows) {
		while ($row = $sql_paid->fetch_assoc()) {
			$pl_paid = $row['paid_numbers'];
			$ol_paid = $row['quantity'];
			if (empty($ol_paid) || $ol_paid == NULL) {
				$ol_paid = 0;
			}

			if ($pl_paid != $ol_paid) {
				$update = $_settings->conn->query("UPDATE product_list SET paid_numbers = '" . $ol_paid . "' WHERE id = '" . $id . "'");


				if ($update) {
					$resp['status'] = 'success';
					continue;
				}

				$resp['status'] = 'failed';
				$resp['msg'] = $_settings->conn->error;
			}
		}
	}

	return json_encode($resp);
}


if (!defined('APP_NAME')) {
	define('APP_NAME', 'Sistemas');
}

if (!defined('APP_VERSION')) {
	define('APP_VERSION', '3.0');
}

if (!defined('DEV_NAME')) {
	define('DEV_NAME', 'Sistemas');
}

if (!defined('DEV_URL')) {
	define('DEV_URL', 'https://#');
}

if (!defined('SUPPORT_URL')) {
	define('SUPPORT_URL', 'https://#');
}

if (!defined('LICENSE_VIEW')) {
	define('LICENSE_VIEW', '1');
}

if (!defined('CONTACT_TYPE')) {
	define('CONTACT_TYPE', '1');
}

ob_end_flush();

?>