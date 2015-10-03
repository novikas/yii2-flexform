<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;

Modal::begin([
		'id' => 'grid-config',
		'header' => '<h2>Настройка вида</h2>',
		'toggleButton' => [
			'label' => '<span class = "glyphicon glyphicon-cog"></span>',	
			'class' => 'btn btn-default',			
		],
		'footer' => "<button id = 'submit-grid-config' class = 'btn btn-success pull-left'>Сохранить</button>",
]);
$html = "<ul id = 'columns-list' value = $className name = $templateId >";
foreach ( $columns as $key => $value )
{
	$html .= '<li>'.Html::checkbox( $value, in_array($value, $selected), [
			'label' => ( isset($attrLabels[$value])?$attrLabels[$value]:$value ),
			'class' => 'visible-column',  
	]).'</li>';
	
}
$html .= '</ul>';
?>

<?php echo $html  ?>

<?php 
Modal::end();