<?php 

namespace vendor\novik\flexform\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "flexfrom_template".
 *
 * @property integer $id
 * @property string $name
 * @property boolean $public
 * @property integer $type
 * @property integer $FK_user
 * @property boolean $current
 * @property string  $modelClass
 *
 * @property FlexformField[] $flexFields
 * @property array $fields
 */
class Template extends ActiveRecord
{
	public static function tableName()
	{	
		return 'flexform_template';
	}
	
	public function rules()
	{
		return [
				[['name', 'FK_user'], 'required'],
				[['FK_user'], 'integer'],
				[['name', 'modelClass'], 'string'],
				[['name', 'modelClass'], 'safe'],
		];	
	}
	
	/**
	 * 
	 * @return ActiveQuery
	 */
	public function getFlexFields()
	{
		return $this->hasMany( FlexformField::className(), ['FK_template' => 'id'] );
	}
	
	/**
	 *
	 * @return number of updated rows
	 */
	public function setCurrent()
	{
		$this->current = 1;
		$this->save();
		$this->ensureImOnlyCurrent();
	}
	
	/**
	 *
	 */
	protected function ensureImOnlyCurrent()
	{
		return self::updateAll(['current' => 0],
				"current = 1
				AND	FK_user = $this->FK_user
				AND id != $this->id
				AND modelClass = '$this->modelClass'");
	}
	
	protected function randomTemplate()
	{
		return self::find()->where(['modelClass' => $this->modelClass ])->andWhere("id != $this->id")->one();
	}
	
	/**
	 *
	 * @return boolean
	 */
	public function beforeDelete()
	{
		if( parent::beforeDelete() )
		{
			FlexformField::deleteAll(['FK_template' => $this->id ]);
			$follower = $this->randomTemplate(); 
			if( $follower )
				$follower->setCurrent();
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * 
	 * @return array of fields or an empty array if there are no fields
	 */
	public function getFields()
	{
		$flexFields = $this->flexFields;
		if( isset( $flexFields ) )
		{
			return ArrayHelper::map( $flexFields, 'id', 'name' );
		}
		else 
		{
			return [];	
		}
	}
	 
}

?>