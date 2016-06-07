<?  
/*редактируемые параметры*/
$uploadPath 	= "/home/ukazatelvetra/www/test/form/"; //путь от корня, куда сохраним присланные формой файлы
$mailTo 		= 'ruslan@aevrika.ru'; // кому
$mailToName 	= ''; // кому имя (не обязательно)
$replyTo		= ''; // копия (не обязательно)
$replyToName 	= ''; // копия имя (не обязательно)
$from			= 'ruslan@aevrika.ru'; // от кого
$fromName 		= ''; // от кого имя (не обязательно)
$subject 		= 'Заявка'; // тема письма

/* 
составляем HTML шаблон письма, используя теги
например <input id="name" name="name" type="text" required> будет тегом {name}
*/
$bodyTemplate = "
<html> 
<head>
  <title>Заказ</title>
</head>
<body>
	<table>
		<tr>
			<td>Имя:</td>
			<td>{name}</td>
		</tr>
		<tr>
			<td>Телефон:</td>
			<td>{phone}</td>
		</tr>
		<tr>
			<td>Email:</td>
			<td>{email}</td>
		</tr>
		<tr>
			<td>Услуга:</td>
			<td>{service}</td>
		</tr>
		<tr>
			<td>Комментарий:</td>
			<td>{comment}</td>
		</tr>
	</table>
</body>"; 
/*код дальше не трогаем*/

function sendRequest($msg = '', $success = 0) {
	echo json_encode( array('msg' => $msg, 'success' => $success) );
} 

if (!isset($_POST)) {
	sendRequest('Данные не получены',0);
	exit;
}

foreach($_POST as $key => $elem){
	$bodyTemplate = str_replace("{" . $key . "}", strip_tags($elem), $bodyTemplate);
}

require 'phpmailer/PHPMailerAutoload.php';

$mail = new PHPMailer;
$mail->CharSet = 'UTF-8';
$mail->setLanguage('ru', '/phpmailer/language/');
$mail->setFrom($from,$fromName);
if ($replyTo) {$mail->addReplyTo($replyTo,$replyToName);}
$mail->addAddress($mailTo,$mailTo);
$mail->Subject = $subject;
$mail->Body = $bodyTemplate;
$mail->isHTML(true);   
$i = 0;
foreach($_FILES as $file) { 
	$i++;
	$path_info = pathinfo($file['name']);
	$r = rand();
	$fn = 'file-'.$i.'-'.$r.'.'.$path_info['extension']; 
	$filePath = $uploadPath.$fn;
	move_uploaded_file( $file["tmp_name"], $filePath );
	$mail->addAttachment($filePath); 
}

if (!$mail->send()) {
	sendRequest($mail->ErrorInfo, 0);
} else {
    sendRequest('Успешно отправлено', 1);
}
?>