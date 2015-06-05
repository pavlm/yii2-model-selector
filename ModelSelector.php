<?php
namespace pavlm\modelSelector;

use yii\widgets\InputWidget;
use yii\db\ActiveQuery;
use yii\helpers\Html;

/**
 * 
 * Выбор из списка моделей на базе select2
 * @author pavlm
 *
 */
class ModelSelector extends InputWidget
{

    /**
     * @var string active record entity class name wich listed in dropdown
     */
    public $itemType;
    
    /**
     * @var array - predefined entity query criteria
     */
    public $queryConfig = [];
    
    /**
     * @var Closure - returns criteria to filter entities by query (first parameter)
     */
    public $itemSearchQueryFunc;
    /**
     * @var string entity id field name
     */
    public $itemId = 'id';
    
    /**
     * @var string|Closure for list labels generation
     */
    public $itemLabel = 'name';
    
    /**
     * @var string name for searching, if not set then $itemLabel used
     */
    public $itemFieldSearch;
    
    /**
     * @var string|Closure shows ui link to selected item
     */
    public $itemLink;
    
    /**
     * @var boolean TODO check
     */
    public $showItemLink = false;
    
    /**
     * @var boolean TODO check
     */
    public $showItemClear = false;
    
    /**
     * @var int - size of dataset for server paging, if zero - all records loaded
     */
    public $listPageSize = 20;
    
    /**
     * @var string optional route for ModelSelectorAjaxAction
     */
    public $ajaxRoute;
    
    /**
     * @var string
     */
    public $ajaxId;
    
    /**
     * @var string partial view where ModelSelector widget rendered (in case of ajaxRoute using)
     */
    public $ajaxView;
    
    /**
     * @var array
     */
    public $options = [
        'style' => 'width:100%',
    ];
    
    /**
     * @var array - additional plugin options
     */
    public $jsOptions = [
        //'select2Options' => [],
    ];
    
    public $hiddenOptions = [];
    
    /**
     * @var bool if true then no autoinit also embeds widget options into wrapper tag, requires $(...).modelSelector() call
     */
    public $manualInit = false;
    
    public function run()
    {
        if (\Yii::$app->request->isAjax && @$_REQUEST['ajaxId'] == $this->getAjaxId()) {
            return $this->actionAjaxLoad();
        } else {
            return $this->actionDraw();
        }
    }
    
    public function getAjaxId()
    {
        return $this->ajaxId ?: $this->id.'-model-selector';
    }

    public function getAttribValue() {
        $val = $this->model ? Html::getAttributeValue($this->model, $this->attribute) : null;
        if ($val instanceof CActiveRecord)
            $val = $val->{$this->itemId};
        return $val;
    }
    
    public function getJSOptions()
    {
        $val = $this->getAttribValue();
        $e = $val ? $this->formatModel($this->loadModel($val)) : false;
        $data = array(
            'ajaxId' => $this->getAjaxId(),
            'value' => $val,
            'ajaxUrl' => $this->ajaxRoute ? Yii::$app->createUrl($this->ajaxRoute) : null,
            'ajaxView' => $this->ajaxRoute ? $this->ajaxView : null,
            'listPageSize' => $this->listPageSize,
            'model' => $e,
        );
        $data = array_merge($data, $this->jsOptions);
        return $data;
    }

    public function actionDraw()
    {
        return $this->render('selector');
    }
    
    public function actionAjaxLoad()
    {
        while (@ob_end_clean()) {}
        $es = $this->loadModels();
        $fes = $this->formatModels($es);
        echo json_encode($fes);
        die();
    }
    
    public function loadModels()
    {
        $class = $this->itemType;
        //$q = new ActiveQuery($this->itemType, $this->queryConfig);
        $q = $class::find();
        \Yii::configure($q, $this->queryConfig);
        $query = @$_REQUEST['query'];
        $page = intval(@$_REQUEST['page']); // todo make pageParamName
        
        if ($this->itemSearchQueryFunc) {
            // search by user criteria
            $searchFunc = $this->itemSearchQueryFunc;
            $searchFunc($q, $query);
        } elseif (!empty($query)) {
            $field = $this->itemFieldSearch ?: (is_string($this->itemLabel) ? $this->itemLabel : null);
            if ($field) {
                // search by label
                $q->andWhere(['like', $field, $query]);
            }
        }
        if ($this->listPageSize) {
            $q->limit($this->listPageSize);
            $q->offset($this->listPageSize * $page);
        }
        return $q->all();
    }
    
    public function loadModel($pk)
    {
        $q = new ActiveQuery($this->itemType);
        $q->where([$this->itemId => $pk]);
        return $q->one();
        /*
        $cr = $this->itemCriteria ? (!is_array($this->itemCriteria) ? $this->itemCriteria : new CDbCriteria($this->itemCriteria)) : new CDbCriteria();
        $e = CActiveRecord::model($this->itemType)->findByPk($pk, $cr);
        return $e;
        */
    }
    
    public function formatModels($es)
    {
        $fes = [];
        foreach ($es as $e) {
            $fes[] = $this->formatModel($e);
        }
        return $fes;
    }
    
    public function formatModel($e)
    {
        if (!$e)
            return $e;
        $itemLabel = $this->itemLabel;
        $fe = array(
            'id' => $e->{$this->itemId},
            'text' => is_callable($itemLabel) ? $itemLabel($e) : $e->{$itemLabel},
        );
        if (!empty($this->itemLink)) {
            $func = $this->itemLink;
            $fe['link'] = $func($e);
        }
        return $fe;
    }
    
    /**
     * bind to this directory for easy decorating
     */
    public function getViewPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'views';
    }
    
}