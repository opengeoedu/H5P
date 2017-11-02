<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";

/**
 * H5P package active record
 */
class H5PPackage extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_package";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param string $name
	 *
	 * @return H5PPackage|null
	 */
	static function getPackage($name) {
		/**
		 * @var H5PPackage $h5p_package
		 */

		$h5p_package = self::where([ "name" => $name ])->first();

		return $h5p_package;
	}


	/**
	 * @return H5PPackage|null
	 */
	static function getCurrentPackage() {
		/**
		 * @var H5PPackage $h5p_package
		 */

		$id = (isset($_GET["xhfp_package"]) ? $_GET["xhfp_package"] : "");

		$h5p_package = self::where([ "id" => $id ])->first();

		return $h5p_package;
	}


	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 * @con_is_primary   true
	 * @con_sequence     true
	 */
	protected $id;
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 * @con_is_unique   true
	 */
	protected $name;
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 */
	protected $content_folder;


	/**
	 *
	 */
	public function delete() {
		H5PPackageInstaller::removePackage($this);

		parent::delete();
		// TODO: Delete all repositories objects
	}


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}


	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}


	/**
	 * @return string
	 */
	public function getContentFolder() {
		return $this->content_folder;
	}


	/**
	 * @param string $content_folder
	 */
	public function setContentFolder($content_folder) {
		$this->content_folder = $content_folder;
	}
}
