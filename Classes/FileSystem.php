<?php
namespace SayHello\PostToPDF;

class FileSystem {


	private $folders;

	public function getBaseDir() {
		$this->folders = wp_upload_dir();
		return $this->folders['basedir'];
	}

	public function getFilepath( $post_id ) {

		$folder = implode(
			DIRECTORY_SEPARATOR,
			[
				$this->getBaseDir(),
				get_post_type( $post_id ),
			]
		);

		$this->ensureFolderExists( $folder );

		$language_suffix = '';

		if ( function_exists( 'wpml_get_language_information' ) ) {
			$language_information = wpml_get_language_information( $post_id );
			$language_suffix      = isset( $language_information['language_code'] ) && ! empty( $language_information['language_code'] ) ? '-' . $language_information['language_code'] : '';
		}

		$filepath = implode(
			DIRECTORY_SEPARATOR,
			[
				$folder,
				get_post_field( 'post_name', get_post( $post_id ) ) . $language_suffix . '.pdf',
			]
		);

		return $filepath;
	}

	public function ensureFolderExists( $folder ) {
		if ( ! is_dir( $folder ) ) {
			@mkdir( $folder, 0755, true );
		}
	}

}
