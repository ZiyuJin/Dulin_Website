<?php
/**
 * Base storage manager implementation.
 *
 * @package mediapress.
 */

// Exit if the file is accessed directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Storage Manager base class.
 *
 * All the storage managers must implement this class
 */
abstract class MPP_Storage_Manager {
	/**
	 * Upload a file.
	 *
	 * @param resource $file uploaded file.
	 * @param array    $args extra args.
	 *
	 * @return mixed
	 */
	abstract public function upload( $file, $args );

	/**
	 * Get the media meta based on the given upload info.
	 *
	 * @param array $uploaded_info upload details.
	 *
	 * @return mixed
	 */
	abstract public function get_meta( $uploaded_info );

	/**
	 * Genetare metadata for the attachment.
	 *
	 * @param int    $id media id.
	 * @param string $file file name.
	 *
	 * @return mixed
	 */
	abstract public function generate_metadata( $id, $file );

	/**
	 * Cleanup media. Is used after media is deleted from database.
	 *
	 * @param MPP_Media $media media object.
	 *
	 * @return mixed
	 */
	abstract public function delete_media( $media );

	/**
	 * Move Media to the given gallery
	 *
	 * @param int $media_id media id.
	 * @param int $destination_gallery gallery id where we want to move.
	 *
	 * @return mixed
	 */
	abstract public function move_media( $media_id, $destination_gallery );

	/**
	 * Called when a Gallery is being deleted
	 * Use it to cleanup any remnant of the gallery
	 *
	 * @param MPP_Gallery $gallery gallery that was deleted.
	 */
	abstract public function delete_gallery( $gallery );

	/**
	 * Get the used space for the given component
	 *
	 * @param string $component component name.
	 * @param int    $component_id component id.
	 */
	abstract public function get_used_space( $component, $component_id );

	/**
	 * Get the absolute url to a media file
	 * e.g http://example.com/wp-content/uploads/mediapress/members/1/xyz.jpg
	 *
	 * @param string $size media size(thumbnail,mid etc).
	 * @param int    $id media id.
	 */
	public abstract function get_src( $size = '', $id = null );

	/**
	 * Get the absolute file system path to the
	 *
	 * @param string $size media size(thumbnail,mid etc).
	 * @param int    $id media id.
	 */
	public abstract function get_path( $size = '', $id = null );

	/**
	 * An alias for self::get_src()
	 *
	 * @param string $size media size(thumbnail,mid etc).
	 * @param int    $id media id.
	 *
	 * @return string absolute url of the image
	 */
	public function get_url( $size, $id ) {
		return $this->get_src( $size, $id );
	}

	/**
	 * Assume that the server can handle upload
	 *
	 * Mainly used in case of local uploader for checking postmax size etc
	 * If you are implementing, return false if the upload data can be handled otherwise return false
	 *
	 * @return boolean
	 */
	public function can_handle() {
		return true;
	}
}
