<?php

namespace srag\ActiveRecordConfig\H5P;

use srag\CustomInputGUIs\H5P\PropertyFormGUI\ConfigPropertyFormGUI;

/**
 * Class ActiveRecordConfigFormGUI
 *
 * @package    srag\ActiveRecordConfig\H5P
 *
 * @author     studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @deprecated Please use ConfigPropertyFormGUI from CustomInputGUIs instead
 */
abstract class ActiveRecordConfigFormGUI extends ConfigPropertyFormGUI
{

    /**
     * @var string
     *
     * @deprecated
     */
    const LANG_MODULE = ActiveRecordConfigGUI::LANG_MODULE_CONFIG;
    /**
     * @var string
     *
     * @deprecated
     */
    protected $tab_id;


    /**
     * ActiveRecordConfigFormGUI constructor
     *
     * @param ActiveRecordConfigGUI $parent
     * @param string                $tab_id
     *
     * @deprecated
     */
    public function __construct(
        ActiveRecordConfigGUI $parent, /*string*/
        $tab_id
    ) {
        $this->tab_id = $tab_id;

        parent::__construct($parent);
    }


    /**
     * @inheritdoc
     *
     * @deprecated
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ActiveRecordConfigGUI::CMD_UPDATE_CONFIGURE . "_" . $this->tab_id, $this->txt("save"));
    }


    /**
     * @inheritdoc
     *
     * @deprecated
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritdoc
     *
     * @deprecated
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt($this->tab_id));
    }
}
