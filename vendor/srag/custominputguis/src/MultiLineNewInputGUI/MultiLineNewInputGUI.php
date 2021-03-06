<?php

namespace srag\CustomInputGUIs\H5P\MultiLineNewInputGUI;

use ilFormPropertyGUI;
use ilTableFilterItem;
use ilTemplate;
use ilToolbarItem;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\Items\Items;
use srag\DIC\H5P\DICTrait;

/**
 * Class MultiLineNewInputGUI
 *
 * @package srag\CustomInputGUIs\H5P\MultiLineNewInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MultiLineNewInputGUI extends ilFormPropertyGUI implements ilTableFilterItem, ilToolbarItem
{

    use DICTrait;
    const SHOW_INPUT_LABEL_NONE = 1;
    const SHOW_INPUT_LABEL_ONCE = 2;
    const SHOW_INPUT_LABEL_ALWAYS = 3;
    /**
     * @var ilFormPropertyGUI[]
     */
    protected $inputs = [];
    /**
     * @var ilFormPropertyGUI[]|null
     */
    protected $inputs_generated = null;
    /**
     * @var int
     */
    protected $show_input_label = self::SHOW_INPUT_LABEL_ONCE;
    /**
     * @var bool
     */
    protected $show_sort = true;
    /**
     * @var array
     */
    protected $value = [];


    /**
     * MultiLineNewInputGUI constructor
     *
     * @param string $title
     * @param string $post_var
     */
    public function __construct($title = "", $post_var = "")
    {
        parent::__construct($title, $post_var);
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
     * @return bool
     */
    public function checkInput()
    {
        $ok = true;

        foreach ($this->getInputs() as $i => $inputs) {
            foreach ($inputs as $org_post_var => $input) {
                $b_value = $_POST[$input->getPostVar()];

                $_POST[$input->getPostVar()] = $_POST[$this->getPostVar()][$i][$org_post_var];

                /*if ($this->getRequired()) {
                   $input->setRequired(true);
               }*/

                if (!$input->checkInput()) {
                    $ok = false;
                }

                $_POST[$input->getPostVar()] = $b_value;
            }
        }

        if ($ok) {
            return true;
        } else {
            //$this->setAlert(self::dic()->language()->txt("form_input_not_valid"));

            return false;
        }
    }


    /**
     * @return ilFormPropertyGUI[][]
     */
    public function getInputs()
    {
        if ($this->inputs_generated === null) {
            $this->inputs_generated = [];

            foreach (array_values($this->getValue()) as $i => $value) {
                $inputs = [];

                foreach ($this->inputs as $input) {
                    $input = clone $input;

                    $org_post_var = $input->getPostVar();

                    Items::setValueToItem($input, $value[$org_post_var]);

                    $post_var = $this->getPostVar() . "[" . $i . "][";
                    if (strpos($org_post_var, "[") !== false) {
                        $post_var .= strstr($input->getPostVar(), "[", true) . "][" . strstr($org_post_var, "[");
                    } else {
                        $post_var .= $org_post_var . "]";
                    }
                    $input->setPostVar($post_var);

                    $inputs[$org_post_var] = $input;
                }

                $this->inputs_generated[] = $inputs;
            }
        }

        return $this->inputs_generated;
    }


    /**
     * @return int
     */
    public function getShowInputLabel()
    {
        return $this->show_input_label;
    }


    /**
     * @inheritDoc
     */
    public function getTableFilterHTML()
    {
        return $this->render();
    }


    /**
     * @inheritDoc
     */
    public function getToolbarHTML()
    {
        return $this->render();
    }


    /**
     * @return array
     */
    public function getValue()
    {
        $values = $this->value;

        if (empty($values)) {
            $values = [[]];
        }

        return $values;
    }


    /**
     * @param ilTemplate $tpl
     */
    public function insert(ilTemplate $tpl) /*: void*/
    {
        $html = $this->render();

        $tpl->setCurrentBlock("prop_generic");
        $tpl->setVariable("PROP_GENERIC", $html);
        $tpl->parseCurrentBlock();
    }


    /**
     * @return bool
     */
    public function isShowSort()
    {
        return $this->show_sort;
    }


    /**
     * @return string
     */
    public function render()
    {
        $dir = __DIR__;
        $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);
        self::dic()->mainTemplate()->addCss($dir . "/css/multi_line_new_input_gui.css");
        self::dic()->mainTemplate()->addJavaScript($dir . "/js/multi_line_new_input_gui.min.js");

        $tpl = new ilTemplate(__DIR__ . "/templates/multi_line_new_input_gui.html", true, true);

        $tpl->setVariable("SHOW_INPUT_LABEL", $this->show_input_label);

        $tpl->setCurrentBlock("line");

        foreach ($this->getInputs() as $inputs) {
            $tpl->setVariable("INPUTS", Items::renderInputs($inputs));

            $tpl->setVariable("ADD", self::output()->getHTML(self::dic()->ui()->factory()->glyph()->add()->withAdditionalOnLoadCode(function ($id) {    return 'il.MultiLineNewInputGUI.init($("#' . $id . '").parent().parent().parent())';
})));

            if ($this->show_sort) {
                $sort_tpl = new ilTemplate(__DIR__ . "/templates/multi_line_new_input_gui_sort.html", true, true);

                $sort_tpl->setVariable("UP", self::output()->getHTML(self::dic()->ui()->factory()->glyph()->sortAscending()));

                $sort_tpl->setVariable("DOWN", self::output()->getHTML(self::dic()->ui()->factory()->glyph()->sortDescending()));

                $tpl->setVariable("SORT", self::output()->getHTML($sort_tpl));
            }

            $tpl->setVariable("REMOVE", self::output()->getHTML(self::dic()->ui()->factory()->glyph()->remove()));

            $tpl->parseCurrentBlock();
        }

        return self::output()->getHTML($tpl);
    }


    /**
     * @param ilFormPropertyGUI[] $inputs
     */
    public function setInputs(array $inputs) /*: void*/
    {
        $this->inputs = $inputs;
        $this->inputs_generated = null;
    }


    /**
     * @param int $show_input_label
     */
    public function setShowInputLabel($show_input_label)/* : void*/
    {
        $this->show_input_label = $show_input_label;
    }


    /**
     * @param bool $show_sort
     */
    public function setShowSort($show_sort)/* : void*/
    {
        $this->show_sort = $show_sort;
    }


    /**
     * @param array $value
     */
    public function setValue(/*array*/ $value)/*: void*/
    {
        if (is_array($value)) {
            $this->value = $value;
        } else {
            $this->value = [];
        }
    }


    /**
     * @param array $value
     */
    public function setValueByArray(/*array*/ $value)/*: void*/
    {
        $this->setValue($value[$this->getPostVar()]);
    }
}
