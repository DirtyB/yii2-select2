<?php
/**
 * @link https://github.com/borodulin/yii2-select2
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-select2/blob/master/LICENSE
 */

namespace conquer\select2;

use Yii;
use yii\helpers\Html;
use conquer\helpers\Json;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * @link https://select2.github.io
 * @author Andrey Borodulin
 */
class Select2Widget extends \yii\widgets\InputWidget
{
    /**
     * Points to use Bootstrap theme
     * @var boolean
     */
    public $bootstrap = true;
    /**
     * Language code
     * @var string
     */
    public $language;
    /**
     * Array data
     * @example [['id'=>1, 'text'=>'enhancement'], ['id'=>2, 'text'=>'bug']]
     * @var array
     */
    public $data;
    /**
     * You can use Select2Action to provide AJAX data
     * @see \yii\helpers\BaseUrl::to()
     * @var array|string
     */
    public $ajax;
    /**
     * @see \yii\helpers\BaseArrayHelper::map()
     * @var array
     */
    public $items;
    /**
     * A placeholder value can be defined and will be displayed until a selection is made
     * @var string
     */
    public $placeholder;
    /**
     * Multiple select boxes
     * @var boolean
     */
    public $multiple;
    /**
     * Tagging support
     * @var boolean
     */
    public $tags;
    /**
     * @link https://select2.github.io/options.html
     * @var array
     */
    public $settings = [];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (isset($this->tags)) {
            $this->options['data-tags'] = $this->tags;
            $this->options['multiple'] = true;
        }
        if (isset($this->language))
            $this->options['data-language'] = $this->language;
        if (isset($this->ajax)) {
            $this->options['data-ajax--url'] = Url::to($this->ajax);
            $this->options['data-ajax--cache'] = 'true';
        }
        if (isset($this->placeholder))
            $this->options['data-placeholder'] = $this->placeholder;
        if (isset($this->multiple)) {
            $this->options['data-multiple'] = 'true';
            $this->options['multiple'] = true;
        }
        if (isset($this->data))
            $this->options['data-data'] = Json::encode($this->data);
        if (!isset($this->options['class']))
            $this->options['class'] = 'form-control';
        if (!empty($this->multiple) || !empty($this->settings['multiple'])) {
            $name = isset($this->options['name']) ? $this->options['name'] : Html::getInputName($this->model, $this->attribute);
            if (substr($name,-2)!='[]')
                $this->options['name'] = $name.'[]';
        }
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            if(isset($this->items)) {
                $input = Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
            }
            else {
                $input = Html::activeTextInput($this->model, $this->attribute, $this->options);
            }
        } else {
            if(isset($this->items)) {
                $input = Html::dropDownList($this->name, $this->value, $this->items, $this->options);
            }
            else {
                $input = Html::textInput($this->name, $this->value, $this->options);
            }
        }
        echo $input;
        $this->registerAssets();
    }
    
    /**
     * Registers Assets
     */
    public function registerAssets()
    {
        $view = $this->getView();

        Select2Asset::register($view);
        if ($this->bootstrap)
            Select2BootstrapAsset::register($view);

        if(isset($this->settings['language'])){
            $asset = Yii::$app->assetManager->bundles[Select2Asset::className()];
            $asset->js[] = 'js/i18n/'.$this->settings['language'].'.js';
        }
        
        $id = $this->options['id'];
       
        $settings = Json::encode($this->settings);
        $js = "jQuery('#$id').select2($settings);";
        $view->registerJs($js);
    }
    
}