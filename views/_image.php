<?php
/**
 * Created by PhpStorm.
 * Date: 04.08.2016
 * Time: 18:55
 */
/* @var $modelName int */
/* @var $id int */
/* @var $object_id int */
/* @var $images_num int */
/* @var $images_label string */
/* @var $images_temp string */
/* @var $imageSmallWidth string */
/* @var $imageSmallHeight string */
/* @var $idObject int */
/* @var $createImageText string */
/* @var $updateImageText string */
/* @var $deleteImageText string */
/* @var $frontendUrl string */
/* @var $baseUrl string */
/* @var $imagePath string */
/* @var $noImage string */
/* @var $loaderImage string */
/* @var $backend boolean */
/* @var $imageClass string */
/* @var $buttonDeleteClass string */
/* @var $imageContainerClass string */
/* @var $formImagesContainerClass string */

/* @var $image \Bodasia\cropper\models\Photo */
/* @var $imagesObject array */
/* @var $modelImageForm \Bodasia\cropper\models\ImageForm */

use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use phpnt\bootstrapNotify\BootstrapNotify;

Pjax::begin([
    'id' => 'images-widget-'.$id,
    'enablePushState' => false,
]);
?>
<?= BootstrapNotify::widget(); ?>
<?php
if(isset($isImage) && $isImage != '0'):
	$image =  Html::img('/'.$isImage);
else:
	$image = Html::img($noImage);
endif;
$form = ActiveForm::begin(
    [
        'action' => Url::to(['/images/autoload-image']),
        'options' => [
            'enctype' => 'multipart/form-data',
            'data-pjax' => true,
            'id' => 'image-form-'.$id,
            'timeout' => '7000',
        ],
    ]);
?>
<?= $form->field($modelImageForm, 'image', ['template' => '<div id="crop-url-'.$id.'" class="btn-file">{input}</div>'])
    ->input('file', ['id' => 'imageform-image-'.$id, 'onchange' => 'loadFile(event)'])->label(false)->error(false);
	?>
<?php

if ($imagesObject):
    foreach($imagesObject as $image):
        $imageFileSmall = $frontendUrl.$image->$previewSize;
        $imageOriginal = $frontendUrl.$image->file; ?>
        <div class="<?= $imageContainerClass ?> image-padding">
            <?= Html::button('', ['id' => "button-delete-image-id-".$image->id, 'class' => $buttonDeleteClass, 'onClick' => "window.idImage = '".$image->id."'; deleteImage(event);"]); ?>
            <?= Html::img($imageFileSmall, ['id' => 'preview-image-f-' . $image->id,'original_image' => $imageOriginal,'class' => $imageClass, 'onclick' => "window.idImage = '".$image->id."'; $('#imageform-image-$id').click();"]); ?>
            <?= Html::button($updateImageText, ['id' => "change-image-btn", 'class' => $buttonClass, 'style' => 'width: 100%;', 'onclick' => "window.idImage = '".$image->id."'; $('#imageform-image-$id').click();"]) ?>
        </div>
    <?php endforeach;
else:
    ?>
    <div class="<?= $imageContainerClass; ?> image-padding">
        <?= Html::img($noImage, ['id' => "preview-image-f", 'class' => $imageClass, 'onclick' => "window.idImage = 0; $('#imageform-image-$id').click();"]); ?>
        <?= Html::button($createImageText, ['id' => "change-image-btn", 'class' => $buttonClass, 'style' => 'width: 100%;', 'onclick' => " window.idImage = 0; $('#imageform-image-$id').click();"]) ?>
    </div>
    <?php
endif;

echo Html::input('hidden', 'imageData[modelName]', $modelName);
echo Html::input('hidden', 'imageData[id]', $id);
echo Html::input('hidden', 'imageData[object_id]', $object_id);
echo Html::input('hidden', 'imageData[image_id]', null, ['id' => 'image_id-'.$id]);
echo Html::input('hidden', 'imageData[images_num]', $images_num);
echo Html::input('hidden', 'imageData[images_label]', $images_label);
echo Html::input('hidden', 'imageData[images_temp]', $images_temp);
echo Html::input('hidden', 'imageData[imageSmallWidth]', $imageSmallWidth);
echo Html::input('hidden', 'imageData[imageSmallHeight]', $imageSmallHeight);
echo Html::input('hidden', 'imageData[createImageText]', $createImageText);
echo Html::input('hidden', 'imageData[buttonClass]', $buttonClass);
echo Html::input('hidden', 'imageData[previewSize]', $previewSize);
echo Html::input('hidden', 'imageData[updateImageText]', $updateImageText);
echo Html::input('hidden', 'imageData[deleteImageText]', $deleteImageText);
echo Html::input('hidden', 'imageData[frontendUrl]', $frontendUrl);
echo Html::input('hidden', 'imageData[baseUrl]', $baseUrl);
echo Html::input('hidden', 'imageData[imagePath]', $imagePath);
echo Html::input('hidden', 'imageData[noImage]', $noImage);
echo Html::input('hidden', 'imageData[loaderImage]', $loaderImage);
echo Html::input('hidden', 'imageData[backend]', $backend);
echo Html::input('hidden', 'imageData[imageCrop]', null, ['id' => 'imageCrop-'.$id]);
echo Html::input('hidden', 'imageData[imageClass]', $imageClass);
echo Html::input('hidden', 'imageData[buttonDeleteClass]', $buttonDeleteClass);
echo Html::input('hidden', 'imageData[imageContainerClass]', $imageContainerClass);
echo Html::input('hidden', 'imageData[formImagesContainerClass]', $formImagesContainerClass);
ActiveForm::end();
?>
<?php
Pjax::end();