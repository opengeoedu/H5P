<?php

namespace srag\CustomInputGUIs\H5P\TabsInputGUI;

use ilFormPropertyGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\Items\Items;
use srag\DIC\H5P\DICTrait;

/**
 * Class TabsInputGUITab
 *
 * @package srag\CustomInputGUIs\H5P\TabsInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TabsInputGUITab
{

    use DICTrait;
    /**
     * @var string
     */
    protected $title = "";
    /**
     * @var string
     */
    protected $post_var = "";
    /**
     * @var bool
     */
    protected $active = false;
    /**
     * @var string
     */
    protected $info = "";
    /**
     * @var ilFormPropertyGUI[]
     */
    protected $inputs = [];
    /**
     * @var ilFormPropertyGUI[]|null
     */
    protected $inputs_generated = null;


    /**
     * TabsInputGUITab constructor
     *
     * @param string $title
     * @param string $post_var
     */
    public function __construct($title = "", $post_var = "")
    {
        $this->title = $title;
        $this->post_var = $post_var;
    }


    /**
     * @param ilFormPropertyGUI $input
     */
    public function addInput(ilFormPropertyGUI $input)/*: void*/
    {
        $this->inputs[] = $input;
        $this->inputs_generated = null;
    }


    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }


    /**
     * @param string $post_var
     * @param array  $init_value
     *
     * @return ilFormPropertyGUI[]
     */
    public function getInputs($post_var, array $init_value)
    {
        if ($this->inputs_generated === null) {
            $this->inputs_generated = [];

            foreach ($this->inputs as $input) {
                $input = clone $input;

                $org_post_var = $input->getPostVar();

                if (is_array($init_value[$this->post_var]) && isset($init_value[$this->post_var][$org_post_var])) {
                    Items::setValueToItem($input, $init_value[$this->post_var][$org_post_var]);
                }

                $input->setPostVar($post_var . "[" . $this->post_var . "][" . $org_post_var . "]");

                $this->inputs_generated[$org_post_var] = $input;
            }
        }

        return $this->inputs_generated;
    }


    /**
     * @return string
     */
    public function getPostVar()
    {
        return $this->post_var;
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }


    /**
     * @param bool $active
     */
    public function setActive($active)/* : void*/
    {
        $this->active = $active;
    }


    /**
     * @param string $info
     */
    public function setInfo($info)/* : void*/
    {
        $this->info = $info;
    }


    /**
     * @param ilFormPropertyGUI[] $inputs
     */
    public function setInputs(array $inputs)/* : void*/
    {
        $this->inputs = $inputs;
        $this->inputs_generated = null;
    }


    /**
     * @param string $post_var
     */
    public function setPostVar($post_var)/* : void*/
    {
        $this->post_var = $post_var;
    }


    /**
     * @param string $title
     */
    public function setTitle($title)/* : void*/
    {
        $this->title = $title;
    }


    /**
     *
     */
    public function __clone()/*:void*/
    {
        if ($this->inputs_generated !== null) {
            $this->inputs_generated = array_map(function (ilFormPropertyGUI $input) {    return clone $input;
}, $this->inputs_generated);
        }
    }
}

