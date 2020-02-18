<?php

namespace srag\Plugins\H5P\Content;

use H5PCore;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Action\H5PActionGUI;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ShowContent
 *
 * @package srag\Plugins\H5P\Content
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ShowContent
{

    use DICTrait;
    use H5PTrait;
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var array|null
     */
    public $core = null;
    /**
     * @var bool
     */
    protected $core_output = false;
    /**
     * @var array
     */
    public $js_files = [];
    /**
     * @var array
     */
    public $css_files = [];
    /**
     * @var array
     */
    protected $js_files_output = [];
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
     * ShowContent constructor
     */
    private function __construct()
    {

    }


    /**
     *
     */
    public function initCore()
    {
        if ($this->core === null) {
            $this->core = [
                "baseUrl"            => $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"],
                "url"                => ILIAS_HTTP_PATH . "/" . self::h5p()->objectSettings()->getH5PFolder(),
                "postUserStatistics" => true,
                "ajax"               => [
                    H5PActionGUI::H5P_ACTION_SET_FINISHED      => H5PActionGUI::getUrl(H5PActionGUI::H5P_ACTION_SET_FINISHED),
                    H5PActionGUI::H5P_ACTION_CONTENT_USER_DATA => H5PActionGUI::getUrl(H5PActionGUI::H5P_ACTION_CONTENT_USER_DATA)
                        . "&content_id=:contentId&data_type=:dataType&sub_content_id=:subContentId",
                ],
                "saveFreq"           => false,
                "user"               => [
                    "name" => self::dic()->user()->getFullname(),
                    "mail" => self::dic()->user()->getEmail()
                ],
                "siteUrl"            => $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"],
                "l10n"               => [
                    "H5P" => self::h5p()->contents()->core()->getLocalization()
                ],
                "hubIsEnabled"       => false,
                "core"               => [
                    "styles"  => [],
                    "scripts" => []
                ],
                "loadedCss"          => [],
                "loadedJs"           => []
            ];

            $core_path = self::h5p()->contents()->getCorePath() . "/";

            foreach (H5PCore::$styles as $style) {
                $this->core["core"]["styles"][] = $this->css_files[] = $core_path . $style;
            }

            foreach (H5PCore::$scripts as $script) {
                $this->core["core"]["scripts"][] = $this->js_files[] = $core_path . $script;
            }
        }
    }


    /**
     *
     */
    protected function initCoreForContents()
    {
        if ($this->core === null) {
            $this->initCore();

            $this->core["contents"] = [];

            $this->js_files[] = substr(self::plugin()->directory(), 2) . "/js/H5PContents.min.js";
        }
    }


    /**
     *
     */
    public function initCoreToOutput()
    {
        if (!$this->core_output) {
            $this->core_output = true;

            $core_tpl = self::plugin()->template("H5PCore.min.js");
            $core_tpl->setVariable("H5P_CORE", json_encode($this->core));
            $this->js_files[] = "data:application/javascript;base64," . base64_encode(self::output()->getHTML($core_tpl));
        }
    }


    /**
     * @param Content     $h5p_content
     * @param int         $index
     * @param int         $count
     * @param string|null $text
     *
     * @return string
     */
    public function getH5PContentStep(Content $h5p_content, $index, $count, $text = null)
    {
        $h5p_tpl = self::plugin()->template("H5PContentStep.html");

        if ($text === null) {
            $h5p_tpl->setVariable("H5P_CONTENT", $this->getH5PContent($h5p_content, false));
        } else {
            $h5p_tpl->setVariable("H5P_CONTENT", $text);
        }

        $h5p_tpl->setVariable("H5P_TITLE", $count_text = self::plugin()->translate("content_count", "", [($index + 1), $count]) . " - "
            . $h5p_content->getTitle());

        return self::output()->getHTML([$h5p_tpl, self::dic()->toolbar()]);
    }


    /**
     * @param Content $h5p_content
     * @param bool    $title
     *
     * @return string
     */
    public function getH5PContent(Content $h5p_content, $title = true)
    {
        $this->initCoreForContents();

        $content_integration = $this->initContent($h5p_content);

        $this->initCoreToOutput();

        if ($title) {
            $title = $h5p_content->getTitle();
        } else {
            $title = null;
        }

        $output = $this->getH5PIntegration($content_integration, $h5p_content->getContentId(), $title, $content_integration["embedType"]);

        $this->outputHeader();

        return $output;
    }


    /**
     *
     */
    public function outputHeader()
    {
        foreach ($this->css_files as $css_file) {
            self::dic()->mainTemplate()->addCss($css_file);
        }

        foreach ($this->js_files as $js_file) {
            if (strpos($js_file, "data:application/javascript;base64,") === 0) {
                if (!isset($this->js_files_output[$js_file])) {
                    $this->js_files_output[$js_file] = true;

                    self::dic()->mainTemplate()->setCurrentBlock("js_file");
                    self::dic()->mainTemplate()->setVariable("JS_FILE", $js_file);
                    self::dic()->mainTemplate()->parseCurrentBlock();
                }
            } else {
                self::dic()->mainTemplate()->addJavaScript($js_file);
            }
        }
    }


    /**
     * @param Content $h5p_content
     *
     * @return array
     */
    protected function initContent(Content $h5p_content)
    {
        self::dic()->ctrl()->setParameter($this, "xhfp_content", $h5p_content->getContentId());

        $content = self::h5p()->contents()->core()->loadContent($h5p_content->getContentId());

        $safe_parameters = self::h5p()->contents()->core()->filterParameters($content);

        $user_id = self::dic()->user()->getId();

        $content_integration = [
            "library"         => H5PCore::libraryToString($content["library"]),
            "jsonContent"     => $safe_parameters,
            "fullScreen"      => $content["library"]["fullscreen"],
            "exportUrl"       => $this->ctrl->getLinkTarget($this, self::CMD_EXPORT_CONTENT, "", true, false),
            "embedCode"       => <iframe src="' . $this->ctrl->getLinkTarget($this, self::CMD_EMBED_CONTENT, "", true, false) . '" width=":w" height=":h" frameborder="0" allowfullscreen="allowfullscreen"></iframe>,
            "resizeCode"      => '<script src="/' . ilH5P::CORE_PATH . 'js/h5p-resizer.js" charset="UTF-8"></script>',,
            "url"             => $this->ctrl->getLinkTarget($this, self::CMD_SHOW_CONTENT, "", false, false),,
            "title"           => $h5p_content->getTitle(),
            "displayOptions"  => [
                "frame"     => true,
                "export"    => true,
                "embed"     => true,
                "copyright" => true,
                "icon"      => true
            ],
            "contentUserData" => [
                0 => [
                    "state" => "{}"
                ]
            ],
            "embedType"       => H5PCore::determineEmbedType($h5p_content->getEmbedType(), $content["library"]["embedTypes"])
        ];

        $content_dependencies = self::h5p()->contents()->core()->loadContentDependencies($h5p_content->getContentId(), "preloaded");

        $files = self::h5p()->contents()->core()->getDependenciesFiles($content_dependencies, self::h5p()->objectSettings()->getH5PFolder());
        $scripts = array_map(function ($file) {
            return $file->path;
        }, $files["scripts"]);
        $styles = array_map(function ($file) {
            return $file->path;
        }, $files["styles"]);

        switch ($content_integration["embedType"]) {
            case "div":
                foreach ($scripts as $script) {
                    $this->core["loadedJs"][] = $this->js_files[] = $script;
                }

                foreach ($styles as $style) {
                    $this->core["loadedCss"][] = $this->css_files[] = $style;
                }
                break;

            case "iframe":
                $content_integration["scripts"] = $scripts;
                $content_integration["styles"] = $styles;
                break;
        }

        $content_user_datas = self::h5p()->contents()->getUserDatasByUser($user_id, $h5p_content->getContentId());
        foreach ($content_user_datas as $content_user_data) {
            $content_integration["contentUserData"][$content_user_data->getSubContentId()][$content_user_data->getDataId()] = $content_user_data->getData();
        }

        return $content_integration;
    }


    /**
     * @param array       $content
     * @param int         $content_id
     * @param string|null $title
     * @param string      $embed_type
     *
     * @return string
     */
    protected function getH5PIntegration(array $content, $content_id, $title, $embed_type)
    {
        $content_tpl = self::plugin()->template("H5PContent.min.js");
        $content_tpl->setVariable("H5P_CONTENT", json_encode($content));
        $content_tpl->setVariable("H5P_CONTENT_ID", $content_id);
        $this->js_files[] = "data:application/javascript;base64," . base64_encode(self::output()->getHTML($content_tpl));

        $h5p_tpl = self::plugin()->template("H5PContent.html");

        $h5p_tpl->setVariable("H5P_CONTENT_ID", $content_id);

        if ($title !== null) {
            $h5p_tpl->setCurrentBlock("titleBlock");

            $h5p_tpl->setVariable("H5P_TITLE", $title);
        }

        switch ($embed_type) {
            case "div":
                $h5p_tpl->setCurrentBlock("contentDivBlock");
                break;

            case "iframe":
                $h5p_tpl->setCurrentBlock("contentFrameBlock");
                break;

            default:
                break;
        }

        $h5p_tpl->setVariable("H5P_CONTENT_ID", $content_id);

        $h5p_tpl->parseCurrentBlock();

        return self::output()->getHTML($h5p_tpl);
    }


    /**
     * @param int      $content_id
     * @param int      $score
     * @param int      $max_score
     * @param int      $opened
     * @param int      $finished
     * @param int|null $time
     */
    public function setFinished($content_id, $score, $max_score, $opened, $finished, $time = null)
    {
        $h5p_content = self::h5p()->contents()->getContentById($content_id);
        if ($h5p_content !== null && $h5p_content->getParentType() === Content::PARENT_TYPE_OBJECT) {
            $object_settings = self::h5p()->objectSettings()->getObjectSettingsById($h5p_content->getObjId());
        } else {
            $object_settings = null;
        }

        $user_id = self::dic()->user()->getId();

        $h5p_result = self::h5p()->results()->getResultByUserContent($user_id, $content_id);

        if ($h5p_result === null) {
            $h5p_result = self::h5p()->results()->factory()->newResultInstance();

            $h5p_result->setContentId($content_id);
        } else {
            // Prevent update result on a repository object with "Solve only once"
            if ($object_settings !== null && $object_settings->isSolveOnlyOnce()) {
                return;
            }
        }

        $h5p_result->setScore($score);

        $h5p_result->setMaxScore($max_score);

        $h5p_result->setOpened($opened);

        $h5p_result->setFinished($finished);

        if ($time !== null) {
            $h5p_result->setTime($time);
        }

        self::h5p()->results()->storeResult($h5p_result);

        if ($object_settings !== null) {
            // Store solve status because user may not scroll to contents
            self::h5p()->results()->setContentByUser($h5p_content->getObjId(), $user_id, $h5p_content->getContentId());
        }
    }


    /**
     * @param int         $content_id
     * @param string      $data_id
     * @param int         $sub_content_id
     * @param string|null $data
     * @param bool        $preload
     * @param bool        $invalidate
     *
     * @return string|null
     */
    public function contentsUserData($content_id, $data_id, $sub_content_id, $data = null, $preload = false, $invalidate = false)
    {
        $h5p_content = self::h5p()->contents()->getContentById($content_id);
        if ($h5p_content !== null && $h5p_content->getParentType() === Content::PARENT_TYPE_OBJECT) {
            $object_settings = self::h5p()->objectSettings()->getObjectSettingsById($h5p_content->getObjId());
        } else {
            $object_settings = null;
        }

        $user_id = self::dic()->user()->getId();

        $h5p_content_user_data = self::h5p()->contents()->getUserData($content_id, $data_id, $user_id, $sub_content_id);

        if ($data !== null) {
            if ($data === "0") {
                if ($h5p_content_user_data !== null) {
                    self::h5p()->contents()->deleteContentUserData($h5p_content_user_data);
                }
            } else {
                if ($h5p_content_user_data === null) {
                    $h5p_content_user_data = self::h5p()->contents()->factory()->newContentUserDataInstance();

                    $h5p_content_user_data->setContentId($content_id);

                    $h5p_content_user_data->setSubContentId($sub_content_id);

                    $h5p_content_user_data->setDataId($data_id);
                } else {
                    // Prevent update user data on a repository object with "Solve only once". But some contents may store date with editor so check has results
                    if ($object_settings !== null && $object_settings->isSolveOnlyOnce() && self::h5p()->results()->hasContentResults($h5p_content->getContentId())) {
                        return null;
                    }
                }

                $h5p_content_user_data->setData($data);

                $h5p_content_user_data->setPreload($preload);

                $h5p_content_user_data->setInvalidate($invalidate);

                self::h5p()->contents()->storeContentUserData($h5p_content_user_data);
            }

            return null;
        } else {
            return ($h5p_content_user_data !== null ? $h5p_content_user_data->getData() : null);
        }
    }
}
