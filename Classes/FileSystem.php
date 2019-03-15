<?php
namespace SayHello\PostToPDF;

class FileSystem
{

	private $folders;

	public function getBaseDir()
	{
		$this->folders = wp_upload_dir();
		return $this->folders['basedir'];
	}

	public function getFilepath($post_id)
	{

		$folder = implode(DIRECTORY_SEPARATOR, [
			$this->getBaseDir(),
			get_post_type($post_id)
		]);

		$this->ensureFolderExists($folder);

		$filepath = implode(DIRECTORY_SEPARATOR, [
			$folder,
			get_post_field('post_name', get_post($post_id)).'.pdf'
		]);

		return $filepath;
	}

	public function ensureFolderExists($folder)
	{
		if (!is_dir($folder)) {
			@mkdir($folder, 0755, true);
		}
	}

	public function downloadFile($filepath, $filename)
	{
		header('Content-Type: '.mime_content_type($filepath));
		header('Pragma: public');
		header('Expires: -1');
		header('Cache-Control: public, must-revalidate, post-check=0, pre-check=0');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		readfile($filepath);
		exit;
	}
}
