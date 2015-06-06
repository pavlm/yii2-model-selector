<?php
namespace pavlm\modelSelector;

use Yii;
use yii\widgets\InputWidget;
use yii\db\ActiveQuery;
use yii\helpers\Html;
use yii\db\ActiveRecord;

/**
 * 
 * Выбор из списка моделей на базе select2
 * @author pavlm
 *
 */
class ModelSelector extends InputWidget
{
    /**
     * @var ActiveQuery query to listed items
     */
    public $query;

    /**
     * @var array - predefined model query criteria
     */
    public $queryConfig = [];
    
    /**
     * @var string active record model class name wich listed in dropdown. Alternative to $query
     */
    public $itemType;
    
    /**
     * @var Closure - returns criteria to filter entities by query (first parameter)
     */
    public $itemSearchQueryFunc;
    
    /**
     * @var string model id field name
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
    public $showItemClear = false;
    
    /**
     * @var int - size of dataset for server paging, if zero than all records loaded
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
        //'style' => 'width:100%',
    ];
    
    /**
     * @var array - additional plugin options
     */
    public $jsOptions = [
        //'select2Options' => [],
    ];
    
    public $hiddenOptions = [];
    
    /**
     * @var bool if true then no auto init also embeds widget options into wrapper tag, requires manual $(...).modelSelector() call
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
        if ($val instanceof ActiveRecord)
            $val = $val->{$this->itemId};
        return $val;
    }
    
    public function getJSOptions()
    {
        $val = $this->getAttribValue();
        $m = $val ? $this->formatModel($this->loadModel($val)) : false;
        $data = array(
            'ajaxId' => $this->getAjaxId(),
            'value' => $val,
            'ajaxUrl' => $this->ajaxRoute ? Yii::$app->createUrl($this->ajaxRoute) : null,
            'ajaxView' => $this->ajaxRoute ? $this->ajaxView : null,
            'listPageSize' => $this->listPageSize,
            'model' => $m,
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
        $fms = $this->formatModels($es);
        echo json_encode($fms);
        die();
    }
    
    public function loadModels()
    {
        $q = $this->getActiveQuery();
        $query = Yii::$app->request->post('query');
        $page = intval(Yii::$app->request->post('page', 0));
        
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
        $q = $this->getActiveQuery();
        $q->where([$this->itemId => $pk]);
        return $q->one();
    }
    
    public function getActiveQuery()
    {
        if ($this->query) {
            $q = $this->query;
        } else {
            $class = $this->itemType;
            $q = $class::find();
        }
        \Yii::configure($q, $this->queryConfig);
        return $q;
    }
    
    public function formatModels($ms)
    {
        $fms = [];
        foreach ($ms as $m) {
            $fms[] = $this->formatModel($m);
        }
        return $fms;
    }
    
    public function formatModel($m)
    {
        if (!$m)
            return $m;
        $itemLabel = $this->itemLabel;
        $fm = array(
            'id' => $m->{$this->itemId},
            'text' => is_callable($itemLabel) ? $itemLabel($m) : $m->{$itemLabel},
        );
        if (!empty($this->itemLink)) {
            $func = $this->itemLink;
            $fm['link'] = $func($m);
        }
        return $fm;
    }
    
    /**
     * bind to this directory for easy decorating
     */
    public function getViewPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'views';
    }
    
}