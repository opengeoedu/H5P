<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";

/**
 * H5P content library active record
 */
class ilH5PContentLibrary extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_cont_lib";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param int         $content_id
	 * @param string|null $dependency_type
	 *
	 * @return ilH5PContentLibrary[]
	 */
	static function getContentLibraries($content_id, $dependency_type = NULL) {
		/**
		 * @var ilH5PContentLibrary[] $h5p_content_libraries
		 */

		$where = [
			"content_id" => $content_id
		];

		if ($dependency_type !== NULL) {
			$where["dependency_type"] = $dependency_type;
		}

		$h5p_content_libraries = self::where($where)->orderBy("weight", "asc")->get();

		return $h5p_content_libraries;
	}


	/**
	 * Workaround for multiple primary keys: content_id, library_id, dependency_type
	 *
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
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 */
	protected $content_id;
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 */
	protected $library_id;
	/**
	 * @var string
	 *
	 * @con_has_field      true
	 * @con_fieldtype      text
	 * @con_length         31
	 * @con_is_notnull     true
	 */
	protected $dependency_type = "";
	/**
	 * @var int
	 *
	 * @con_has_field     true
	 * @con_fieldtype     integer
	 * @con_length        2
	 * @con_is_notnull    true
	 */
	protected $weight = 0;
	/**
	 * @var bool
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $drop_css = false;


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
	 * @return int
	 */
	public function getContentId() {
		return $this->content_id;
	}


	/**
	 * @param int $content_id
	 */
	public function setContentId($content_id) {
		$this->content_id = $content_id;
	}


	/**
	 * @return int
	 */
	public function getLibraryId() {
		return $this->library_id;
	}


	/**
	 * @param int $library_id
	 */
	public function setLibraryId($library_id) {
		$this->library_id = $library_id;
	}


	/**
	 * @return string
	 */
	public function getDependencyType() {
		return $this->dependency_type;
	}


	/**
	 * @param string $dependency_type
	 */
	public function setDependencyType($dependency_type) {
		$this->dependency_type = $dependency_type;
	}


	/**
	 * @return int
	 */
	public function getWeight() {
		return $this->weight;
	}


	/**
	 * @param int $weight
	 */
	public function setWeight($weight) {
		$this->weight = $weight;
	}


	/**
	 * @return bool
	 */
	public function isDropCss() {
		return $this->drop_css;
	}


	/**
	 * @param bool $drop_css
	 */
	public function setDropCss($drop_css) {
		$this->drop_css = $drop_css;
	}
}
