<?php

namespace srag\Plugins\H5P\Library;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class LibraryCachedAsset
 *
 * @package srag\Plugins\H5P\Library
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class LibraryCachedAsset extends ActiveRecord
{

    use DICTrait;
    use H5PTrait;
    const TABLE_NAME = "rep_robj_xhfp_lib_ca";
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


    /**
     * @return string
     */
    public function getConnectorContainerName()
    {
        return self::TABLE_NAME;
    }


    /**
     * @return string
     *
     * @deprecated
     */
    public static function returnDbTableName()
    {
        return self::TABLE_NAME;
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
     * @var int
     *
     * @con_has_field      true
     * @con_fieldtype      integer
     * @con_length         8
     * @con_is_notnull     true
     * @__con_is_primary   true
     */
    protected $library_id;
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       64
     * @con_is_notnull   true
     */
    protected $hash = "";


    /**
     * LibraryCachedAsset constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct($primary_key_value = 0, arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @param string $field_name
     *
     * @return mixed|null
     */
    public function sleep($field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            default:
                return null;
        }
    }


    /**
     * @param string $field_name
     * @param mixed  $field_value
     *
     * @return mixed|null
     */
    public function wakeUp($field_name, $field_value)
    {
        switch ($field_name) {
            case "id":
            case "library_id":
                return intval($field_value);

            default:
                return null;
        }
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getLibraryId()
    {
        return $this->library_id;
    }


    /**
     * @param int $library_id
     */
    public function setLibraryId($library_id)
    {
        $this->library_id = $library_id;
    }


    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }


    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }
}
