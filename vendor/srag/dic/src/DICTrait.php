<?php

namespace srag\DIC\H5P;

use srag\DIC\H5P\DIC\DICInterface;
use srag\DIC\H5P\Exception\DICException;
use srag\DIC\H5P\Output\OutputInterface;
use srag\DIC\H5P\Plugin\PluginInterface;
use srag\DIC\H5P\Version\VersionInterface;

/**
 * Trait DICTrait
 *
 * @package srag\DIC\H5P
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait DICTrait
{

    /* *
     * @var string
     *
     * @abstract
     *
     * TODO: Implement Constants in Traits in PHP Core
     * /
    const PLUGIN_CLASS_NAME = "";*/

    /**
     * Get DIC interface
     *
     * @return DICInterface DIC interface
     */
    protected static final function dic()
    {
        return DICStatic::dic();
    }


    /**
     * Get output interface
     *
     * @return OutputInterface Output interface
     */
    protected static final function output()
    {
        return DICStatic::output();
    }


    /**
     * Get plugin interface
     *
     * @return PluginInterface Plugin interface
     *
     * @throws DICException Class $plugin_class_name not exists!
     * @throws DICException Class $plugin_class_name not extends ilPlugin!
     * @logs   DEBUG Please implement $plugin_class_name::getInstance()!
     */
    protected static final function plugin()
    {
        self::checkPluginClassNameConst();

        return DICStatic::plugin(static::PLUGIN_CLASS_NAME);
    }


    /**
     * Get version interface
     *
     * @return VersionInterface Version interface
     */
    protected static final function version()
    {
        return DICStatic::version();
    }


    /**
     * @throws DICException Your class needs to implement the PLUGIN_CLASS_NAME constant!
     */
    private static final function checkPluginClassNameConst()/*: void*/
    {
        if (!defined("static::PLUGIN_CLASS_NAME") || empty(static::PLUGIN_CLASS_NAME)) {
            throw new DICException("Your class needs to implement the PLUGIN_CLASS_NAME constant!", DICException::CODE_MISSING_CONST_PLUGIN_CLASS_NAME);
        }
    }
}
