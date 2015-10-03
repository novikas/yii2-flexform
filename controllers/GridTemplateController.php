<?php

namespace vendor\novik\flexform\controllers;

use yii\web\Controller;
use vendor\novik\interfaces\Templatable;
use vendor\novik\flexform\models\FlexgridTemplate;

class GridTemplateController extends Controller
{
	public function actionCreateGridTemplate()
	{
		$model = new FlexformTemplate();
	
		$model->setAttributes(\Yii::$app->request->post());
		$model->FK_user = \Yii::$app->user->identity->id;
		$model->type = Templatable::TEMPLATE_GRID;
		$model->current = 1;
		$model->public = 0;
	
		if ( $model->save() )
		{
			$model->refresh();
			$model->setCurrent();
		}
	
		return json_encode( [ 'status' => 1 ] );
	}
	
	public function actionConfigure()
	{
		if( isset($_POST['cols']) && isset( $_POST['id'] ) )
		{
			$template = FlexgridTemplate::findOne( $_POST['id'] );
			
			$template->configure( $_POST['cols'] );
		}
	}
}