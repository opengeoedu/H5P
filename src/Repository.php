<?php

namespace srag\Plugins\H5P;

use ilDateTime;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Content\Repository as ContentsRepository;
use srag\Plugins\H5P\Event\Repository as EventsRepository;
use srag\Plugins\H5P\Hub\Repository as HubRepository;
use srag\Plugins\H5P\Library\Repository as LibrariesRepository;
use srag\Plugins\H5P\ObjectSettings\Repository as ObjectSettingsRepository;
use srag\Plugins\H5P\Option\Option;
use srag\Plugins\H5P\Option\OptionOld;
use srag\Plugins\H5P\Result\Repository as ResultsRepository;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\H5P
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use H5PTrait;
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var string
     *
     * @deprecated
     */
    const CSV_SEPARATOR = ", ";
    /**
     * @var self
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance()/* : self*/
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @return ContentsRepository
     */
    public function contents()/* : ContentsRepository*/
    {
        return ContentsRepository::getInstance();
    }


    /**
     *
     */
    public function dropTables()/*: void*/
    {
        self::dic()->database()->dropTable(Option::TABLE_NAME, false);
        self::dic()->database()->dropTable(OptionOld::TABLE_NAME, false);
        $this->contents()->dropTables();
        $this->events()->dropTables();
        $this->hub()->dropTables();
        $this->libraries()->dropTables();
        $this->objectSettings()->dropTables();
        $this->results()->dropTables();
    }


    /**
     * @return EventsRepository
     */
    public function events()/* : EventsRepository*/
    {
        return EventsRepository::getInstance();
    }


    /**
     * @return HubRepository
     */
    public function hub()/* : HubRepository*/
    {
        return HubRepository::getInstance();
    }


    /**
     *
     */
    public function installTables()/*: void*/
    {
        Option::updateDB();
        $this->contents()->installTables();
        $this->events()->installTables();
        $this->hub()->installTables();
        $this->libraries()->installTables();
        $this->objectSettings()->installTables();
        $this->results()->installTables();

        if (self::dic()->database()->tableExists(OptionOld::TABLE_NAME)) {
            OptionOld::updateDB();

            foreach (OptionOld::get() as $option) {
                /**
                 * @var OptionOld $option
                 */
                Option::setOption($option->getName(), $option->getValue());
            }

            self::dic()->database()->dropTable(OptionOld::TABLE_NAME);
        }
    }


    /**
     * @return LibrariesRepository
     */
    public function libraries()/* : LibrariesRepository*/
    {
        return LibrariesRepository::getInstance();
    }


    /**
     * @return ObjectSettingsRepository
     */
    public function objectSettings()/* : ResultRepository*/
    {
        return ObjectSettingsRepository::getInstance();
    }


    /**
     * @return ResultsRepository
     */
    public function results()/* : ResultsRepository*/
    {
        return ResultsRepository::getInstance();
    }


    /**
     * @param string $csvp
     *
     * @return string[]
     *
     * @deprecated
     */
    public function splitCsv(/*:string*/ $csv)/*:array*/
    {
        return explode(self::CSV_SEPARATOR, $csv);
    }


    /**
     * @param string[] $array
     *
     * @return string
     *
     * @deprecated
     */
    public function joinCsv(array $array)/*:string*/
    {
        return implode(self::CSV_SEPARATOR, $array);
    }


    /**
     * @param int $timestamp
     *
     * @return string
     *
     * @deprecated
     */
    public function timestampToDbDate(/*:int*/ $timestamp)/*:string*/
    {
        $date_time = new ilDateTime($timestamp, IL_CAL_UNIX);

        $formated = $date_time->get(IL_CAL_DATETIME);

        return $formated;
    }


    /**
     * @param string $formatted
     *
     * @return int
     *
     * @deprecated
     */
    public function dbDateToTimestamp(/*string*/ $formatted)/*:int*/
    {
        $date_time = new ilDateTime($formatted, IL_CAL_DATETIME);

        $timestamp = $date_time->getUnixTime();

        return $timestamp;
    }
}
