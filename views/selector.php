<?php
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\Json;
use pavlm\modelSelector\ModelSelectorAsset;
use pavlm\modelSelector\Select2Asset;

/* @var $widget pavlm\modelSelector\widgets\ModelSelector */
/* @var $this yii\web\View */
Select2Asset::register($this);
ModelSelectorAsset::register($this);

$widget = $this->context;
$opts = $widget->getJSOptions();
?>

<? 
echo Html::beginTag('div', ['id' => $widget->id, 'class' => 'model-selector', 'data-options' => ($widget->manualInit ? $opts : null) ]);
?>
    <div class="input-group ms-input-group">
	<? 
	$name = $widget->name ?: Html::getInputName($widget->model, $widget->attribute);
	echo Html::hiddenInput($name, $widget->getAttribValue(), array_merge(['class' => 'ms-value'], $widget->hiddenOptions));
	echo Html::textInput($widget->id.'-edit', '', array_merge($widget->options, ['class' => 'ms-field'])); 
	?>
	<? if (!empty($opts['model']['link'])): ?>
    	<? echo Html::a('<i class="glyphicon glyphicon-share-alt"></i>', $opts['model']['link'], 
    	    ['class' => 'btn btn-default btn-sm input-group-addon ms-link', 'target' => '_blank']); ?>
	<? endif; ?>
	</div>
<?
echo Html::endTag('div');
if (!$widget->manualInit) {
    $this->registerJs("\$('#".$widget->id."').modelSelector(".Json::encode($opts).");", View::POS_READY, $widget->id);
} 
$this->registerCss('
.ms-input-group { width: 100%; }
');
?>