<?php

namespace Bodasia\cropper\models;

use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "photo".
 *
 * @property integer $id
 * @property string $file
 * @property string $file_small
 * @property string $type
 * @property integer $object_id
 * @property integer $action_id
 * @property integer $deleted
 * @property integer $created_at
 * @property integer $updated_at
 */
class Photo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'photo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
            [['object_id', 'action_id', 'deleted', 'created_at', 'updated_at'], 'integer'],
            [['file', 'file_small'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'file' => Yii::t('app', 'File'),
            'file_small' => Yii::t('app', 'File Small'),
            'type' => Yii::t('app', 'Type'),
            'object_id' => Yii::t('app', 'Object ID'),
            'action_id' => Yii::t('app', 'action ID'),
            'deleted' => Yii::t('app', 'Deleted'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAction($modelActions)
	{
        return $modelActions::findOne($this->action_id);
    }

    public function getPhotosByLabel($label, $objectId)
    {
		return self::find()
			->where([
				'type'      => $label,
				'object_id' => $objectId,
				'action_id'   => \yii::$app->id,
				'deleted'   => 0
			])
			->orderBy('id')
			->all();
    }


}
