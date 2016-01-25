<?
	$imageToRemove = urldecode($_REQUEST['img']);
	if (file_exists($imageToRemove))
	{
		$path_parts = pathinfo($imageToRemove);
		unlink($imageToRemove);
		$src_imageToRemove = $path_parts['dirname'].'/src/'.$path_parts['basename'];
		if (file_exists($src_imageToRemove))
			unlink($src_imageToRemove);
	}
?>