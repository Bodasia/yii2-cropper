<?php
/**
 * Created by PhpStorm.
 * Date: 24.07.2016
 * Time: 21:57
 */

namespace Bodasia\cropper\assets;

use yii\web\AssetBundle;

class DistAsset extends AssetBundle
{
    public $sourcePath = '@vendor/Bodasia/yii2-cropper';

    public $css = [
        'css/crop.css'
    ];

    public $images = [
        'images/'
    ];
}
