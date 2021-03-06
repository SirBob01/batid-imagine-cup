<?php
	require_once "config.php";
	
	// Upload the report data
	if(!isset($_FILES['attachment']) || $_FILES['attachment']['error'] == UPLOAD_ERR_NO_FILE) {
		$multimedia_bin = 0;
	} else {
		$multimedia_bin = 1;
	}

	$insert = "INSERT INTO batid_db.comments (author, content, post_id, multimedia) VALUES ";
	$value = "('".$_POST['author']."', '".$_POST['content']."', ".$_POST['post_id'].", ".$multimedia_bin.")";
	$command = $insert . $value;
	echo $command;
	
	$query = mysqli_query($conn, $command);
	if (!$query) {
		die('Invalid query: ' . mysqli_error($conn));
	}

	// Upload multimedia
	require_once 'vendor/autoload.php';
	use MicrosoftAzure\Storage\Blob\BlobRestProxy;
	use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

	$connectionString = "DefaultEndpointsProtocol=https;AccountName=batid;AccountKey=Vq8tzPy1nGrtTERtNOqCGdoEyY8ceO41LkIn6SRuLUGIxNK1ofuhC4idwQy5moLjMiJgIx/isBFHE4zoietWkw==;";
	$blobClient = BlobRestProxy::createBlobService($connectionString);

	$file_content = fopen($_FILES["attachment"]["tmp_name"], 'r');
	$ext = pathinfo($_FILES["attachment"]["name"], PATHINFO_EXTENSION);

	try	{
		$blobClient->createBlockBlob("multimedia", $_POST['post_id'].'_'.mysqli_insert_id($conn).'.'.$ext, $file_content);
	}
	catch(ServiceException $e){
		$code = $e->getCode();
		$error_message = $e->getMessage();
		echo $code.": ".$error_message."<br/>";
	}

	header('Location: '.$_SERVER['HTTP_REFERER']);
	exit;
?>
