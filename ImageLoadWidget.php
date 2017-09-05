<?php
	/**
	 * Created by PhpStorm.
	 * User:
	 * Date: 06.10.2015
	 * Time: 19:29
	 */

	namespace Bodasia\cropper;

	use yii\web\View;
	use yii\base\Widget;
	use yii\helpers\Json;
	use Bodasia\cropper\assets\CropperAsset;
	use Bodasia\cropper\assets\DistAsset;
	use Bodasia\cropper\models\ImageForm;
	use yii\helpers\Url;

	class ImageLoadWidget extends Widget
	{
		public $modelName;
		public $id;
		public $object_id;
		public $imagesObject;
		public $images_num;
		public $images_label;
		public $images_temp;
		public $imageSmallWidth;
		public $imageSmallHeight;
		public $deleteUrl;
		public $autoloadUrl;
		public $createImageText = 'Загрузить фото';
		public $updateImageText = 'Изменить фото';
		public $deleteImageText = 'Удалить фото';
		public $headerModal = 'Загрузить аватар';   // заголовок в модальном окне
		public $buttonClass = 'btm btn-info';       // класс для кнопок - загрузить/обновить
		public $previewSize = 'file_small';         // размер изображения для превью(либо file_small, либо просто file)
		public $sizeModal = 'modal-lg';           // размер модального окна
		public $frontendUrl = '';
		public $baseUrl = '@webroot';           // алиас к изображениям
		public $imagePath;
		public $noImage = 1;                    // 1 - no-logo, 2 - no-avatar или свое значение
		public $loaderImage = 1;                    // 1 - 1x1, 2 - 3x4
		public $backend = false;
		public $classesWidget = [ 'imageClass' => 'imageLoaderClass', 'buttonDeleteClass' => 'btn btn-xs btn-danger btn-imageDelete glyphicon glyphicon-trash glyphicon', 'imageContainerClass' => 'col-md-3', 'formImagesContainerClass' => 'formImageContainer', ];
		public $options = [];
		public $pluginOptions = [];
		public $cropBoxData = [ 'left' => 10,                                   // смещение слева
			'top' => 10,                                    // смещение вниз
		];
		public $canvasData = [                              // начальные настройки холста
			//'width' => 500,                               // ширина
			//'height' => 500                               // высота
		];

		private $modelImageForm;

		public function init()
		{
			parent::init();
			$this->modelImageForm = new ImageForm();
			$this->deleteUrl = Url::to([ '/images/delete-image' ]);
			$this->autoloadUrl = Url::to([ '/images/autoload-image' ]);

			if($this->images_num == 1) {
				$this->registerClientScriptOne();
			} else {
				$this->registerClientScriptMany();
			}
		}

		public function run()
		{

			return $this->render('view', [ 'widget' => $this, 'modelImageForm' => $this->modelImageForm, ]);
		}

		public function registerClientScript()
		{
			$view = $this->getView();
			CropperAsset::register($view);
			$assets = DistAsset::register($view);
			if($this->noImage == 1) {
				$this->noImage = $assets->baseUrl.'/images/no-logo.png';
			} elseif($this->noImage == 2) {
				$this->noImage = $assets->baseUrl.'/images/no-avatar.png';
			} elseif($this->noImage == 3) {
				$ratio = round($this->pluginOptions[ 'aspectRatio' ], 2);
				switch( $ratio ) {
					case 0:
						$this->noImage = $assets->baseUrl.'/images/no-img-1x1.png';
						break;
					case 1:
						$this->noImage = $assets->baseUrl.'/images/no-img-1x1.png';
						break;
					case 1.33:
						$this->noImage = $assets->baseUrl.'/images/no-img-4x3.png';
						break;
					case 0.75:
						$this->noImage = $assets->baseUrl.'/images/no-img-3x4.png';
						break;
					case 1.78:
						$this->noImage = $assets->baseUrl.'/images/no-img-16x9.png';
						break;
					case 0.56:
						$this->noImage = $assets->baseUrl.'/images/no-img-9x16.png';
						break;
				}
			}

			if($this->loaderImage == 1) {
				$this->loaderImage = $assets->baseUrl.'/images/loader_1x1.gif';
			} elseif($this->noImage == 2) {
				$this->loaderImage = $assets->baseUrl.'/images/loader_3x4.gif';
			}
		}

		public function registerClientScriptMany()
		{
			$this->registerClientScript();
			$view = $this->getView();
			// Пользовательские настройки переводим в JSON
			$options = Json::encode($this->pluginOptions);
			$cropBoxData = Json::encode($this->cropBoxData);
			$canvasData = Json::encode($this->canvasData);

			$imageClass = $this->classesWidget[ 'imageClass' ];
			$buttonDeleteClass = $this->classesWidget[ 'buttonDeleteClass' ];
			$imageContainerClass = $this->classesWidget[ 'imageContainerClass' ];
			$formImagesContainerClass = $this->classesWidget[ 'formImagesContainerClass' ];

			$js = <<< JS
            var loadFileMany = function(event) {                               
                var outputMany = document.getElementById("previewImg-$this->id");        
                outputMany.src = URL.createObjectURL(event.target.files[0]);  
                $("#modal-$this->id").modal('show');             
            };
JS;
			$view->registerJs($js, View::POS_HEAD);

			$js = <<< JS
            var deleteImageMany = function(event) {                        
                if (confirm("Удалить изображение?")) {                                  
                    var imageDataMany = JSON.stringify({
                        modelName: "$this->modelName",
                        id: "$this->id",
                        object_id: "$this->object_id",
                        image_id: window.idImage,
                        images_num: "$this->images_num",
                        images_label: "$this->images_label",
                        buttonClass: "$this->buttonClass",
                        previewSize: "$this->previewSize",
                        images_temp: "$this->images_temp",
                        imageSmallWidth: "$this->imageSmallWidth",
                        imageSmallHeight: "$this->imageSmallHeight",
                        createImageText:  "$this->createImageText",
                        updateImageText:  "$this->updateImageText",
                        deleteImageText:  "$this->deleteImageText",
                        deleteUrl: "$this->deleteUrl",
                        frontendUrl: "$this->frontendUrl",
                        baseUrl: "$this->baseUrl",
                        imagePath: "$this->imagePath",
                        noImage: "$this->noImage",
                        loaderImage: "$this->loaderImage",
                        backend: "$this->backend",
                        imageClass: "$imageClass",
                        buttonDeleteClass: "$buttonDeleteClass",
                        imageContainerClass: "$imageContainerClass",
                        formImagesContainerClass: "$formImagesContainerClass"
                    });
                    $.pjax({
                        type: "POST",
                        url: "$this->deleteUrl",
                        data: {imageData: imageDataMany},
                        container: "#images-widget-$this->id",
                        scrollTo: false,
                        push: false,
                    });
                } else {
                return false;
                }
            };
JS;
			$view->registerJs($js, View::POS_HEAD);

			$js = <<< JS
            var modalBoxMany = $("#modal-$this->id"),                                
                imageMany = $("#modal-$this->id .crop-image-container-$this->id > img"),
                cropBoxData = $cropBoxData,
                canvasData = $canvasData,
                cropUrl;                                                   

            modalBoxMany.on("shown.bs.modal", function (event) {              
                cropUrl = $("#crop-url-$this->id").attr("$this->autoloadUrl");
                imageMany.cropper($.extend({                                
                    built: function () {                                    
                        imageMany.cropper('setCropBoxData', cropBoxData);
                        imageMany.cropper('setCanvasData', canvasData);
                    },
                    dragend: function() {                                   
                        cropBoxData = imageMany.cropper('getCropBoxData');  
                        canvasData = imageMany.cropper('getCanvasData');    
                    }
                }, $options));                                              

            }).on('hidden.bs.modal', function () {                          
                cropBoxData = imageMany.cropper('getCropBoxData');          
                canvasData = imageMany.cropper('getCanvasData');            
                imageMany.cropper('destroy');                               
            });
JS;
			$view->registerJs($js);

			$js = <<< JS
				
                $(document).on("click", "#modal-$this->id .crop-submit", function(e) {                       
                    e.preventDefault();
                    
                    cropBoxData = imageMany.cropper('getCropBoxData');              
                    canvasData = imageMany.cropper('getCanvasData');   
                    $("#image_id-$this->id").attr("value", window.idImage);     
					var cropData = JSON.stringify(imageMany.cropper("getData"));
					$("#imageCrop-$this->id").attr("value", cropData);
                    
                    var imageData = JSON.stringify({
                        modelName: "$this->modelName",
                        id: "$this->id",
                        object_id: "$this->object_id",
                        image_id: window.idImage,
                        images_num: "$this->images_num",
                        images_label: "$this->images_label",
                        buttonClass: "$this->buttonClass",
                        previewSize: "$this->previewSize",
                        images_temp: "$this->images_temp",
                        imageSmallWidth: "$this->imageSmallWidth",
                        imageSmallHeight: "$this->imageSmallHeight",
                        createImageText:  "$this->createImageText",
                        updateImageText:  "$this->updateImageText",
                        deleteImageText:  "$this->deleteImageText",
                        deleteUrl: "$this->deleteUrl",
                        frontendUrl: "$this->frontendUrl",
                        baseUrl: "$this->baseUrl",
                        imagePath: "$this->imagePath",
                        noImage: "$this->noImage",
                        loaderImage: "$this->loaderImage",
                        backend: "$this->backend",
                        imageClass: "$imageClass",
                        buttonDeleteClass: "$buttonDeleteClass",
                        imageContainerClass: "$imageContainerClass",
                        formImagesContainerClass: "$formImagesContainerClass",
                        imageCrop: cropData
                    });
                    
                    var formdata = new FormData();
                   
                    formdata.append('imageData', imageData);
                    formdata.append('ImageForm[image]', document.getElementById('imageform-image-$this->id').files[0]);
                    $.ajax({
                        type: "POST",
                        url: "$this->autoloadUrl",
                        data: formdata,
                        processData: false,
        				contentType: false,
                        success: function(data){                           
                            var json = $.parseJSON(data);
                            if (window.idImage != 0) {
                              $('#image_container_id_' + window.idImage).remove();
                            }
                            json.forEach(function(item, i, json) {  
                              if ($(document).find("#image_container_id_"+item['id'])[0] == undefined) {  
								  $(createImageContainer(item, "$this->id")).insertAfter("#image_container_id_none");
                              }
                            });                             
                        }
                    });
                    modalBoxMany.modal("hide");                                    
                });
                
                function createImageContainer(imageItem, widgetId) {
                  var imageContainer = $(document.createElement('div'));
                  imageContainer.attr('id', 'image_container_id_' + imageItem['id'])
                  .attr('class', '$imageContainerClass')
                  .attr('style','display: inline-block; white-space: normal');
                  var delButton = $(document.createElement('button'))
                  	.attr('type', 'button')
                  	.attr('class', '$buttonDeleteClass')
                  	.attr('onclick', 'window.idImage = \'' + imageItem['id'] + '\'; deleteImageMany(event);');
                  var imgPreview = $(document.createElement('img'))
                  	.attr('id', 'preview-image-f-' + imageItem['id'])
                  	.attr('class', '$imageClass')
                  	.attr('src', imageItem['file_small']);
                  var updButton = $(document.createElement('button'))
                  	.attr('type', 'button')
                  	.attr('class', 'btm btn-info')
                  	.attr('style', 'width: 100%;')
                  	.attr('onclick', 'window.idImage = \'' + imageItem['id'] + '\'; $(\'#imageform-image-' + widgetId + '\').click();')
                  	.append('Изменить фото');
                  imageContainer.append(delButton)
                  .append(imgPreview)
                  .append(updButton);
                  return imageContainer;
                }
JS;
			$view->registerJs($js);
		}

		public function registerClientScriptOne()
		{
			$this->registerClientScript();

			$view = $this->getView();

			$options = Json::encode($this->pluginOptions);
			$cropBoxData = Json::encode($this->cropBoxData);
			$canvasData = Json::encode($this->canvasData);

			$imageClass = $this->classesWidget[ 'imageClass' ];
			$buttonDeleteClass = $this->classesWidget[ 'buttonDeleteClass' ];
			$imageContainerClass = $this->classesWidget[ 'imageContainerClass' ];
			$formImagesContainerClass = $this->classesWidget[ 'formImagesContainerClass' ];

			$js = <<< JS
            var loadFile = function(event) {                                
                var output = document.getElementById("previewImg-$this->id");
                output.src = URL.createObjectURL(event.target.files[0]);
                /*if (window.idImage == 0) {
					 output.src = URL.createObjectURL(event.target.files[0]);
				}
                else {
                    origImage = $("#preview-image-f").attr("original_image");
                    output.src = origImage;
                }*/
                $("#modal-$this->id").modal('show');                
            };			
			
JS;
			$view->registerJs($js, View::POS_HEAD);

			$js = <<< JS
            var deleteImage = function(event) {                     
                if (confirm("Удалить изображение?")) {              
                    var imageData = JSON.stringify({
                        modelName: "$this->modelName",
                        id: "$this->id",
                        object_id: "$this->object_id",
                        image_id: window.idImage,
                        images_num: "$this->images_num",
                        images_label: "$this->images_label",
                        buttonClass: "$this->buttonClass",
                        previewSize: "$this->previewSize",
                        images_temp: "$this->images_temp",
                        imageSmallWidth: "$this->imageSmallWidth",
                        imageSmallHeight: "$this->imageSmallHeight",
                        createImageText:  "$this->createImageText",
                        updateImageText:  "$this->updateImageText",
                        deleteImageText:  "$this->deleteImageText",
                        deleteUrl: "$this->deleteUrl",
                        frontendUrl: "$this->frontendUrl",
                        baseUrl: "$this->baseUrl",
                        imagePath: "$this->imagePath",
                        noImage: "$this->noImage",
                        loaderImage: "$this->loaderImage",
                        backend: "$this->backend",
                        imageClass: "$imageClass",
                        buttonDeleteClass: "$buttonDeleteClass",
                        imageContainerClass: "$imageContainerClass",
                        formImagesContainerClass: "$formImagesContainerClass"
                    });
                    $.pjax({
                        type: "POST",
                        url: "$this->deleteUrl",
                        data: {imageData: imageData},
                        container: "#images-widget-$this->id",
                        scrollTo: false,
                        push: false
                    });
                } else {
                return false;
                }
            };
JS;
			$view->registerJs($js, View::POS_HEAD);

			$js = <<< JS
            var modalBox = $("#modal-$this->id"),                                 
                image = $("#modal-$this->id .crop-image-container-$this->id > img"),       
                cropBoxData = $cropBoxData,
                canvasData = $canvasData,
                cropUrl;                                                    

            modalBox.on("shown.bs.modal", function (event) {                
                cropUrl = $("#crop-url-$this->id").attr("$this->autoloadUrl");   
                image.cropper($.extend({                                   
                    built: function () {                                   
                        // Начальные настройки изображения
                        image.cropper('setCropBoxData', cropBoxData);
                        image.cropper('setCanvasData', canvasData);
                    },
                    dragend: function() {                                   
                        cropBoxData = image.cropper('getCropBoxData');      
                        canvasData = image.cropper('getCanvasData');       
                    }
                }, $options));                                           

            }).on('hidden.bs.modal', function () {                          
                cropBoxData = image.cropper('getCropBoxData');              
                canvasData = image.cropper('getCanvasData');               
                image.cropper('destroy');                                   
            });
JS;
			$view->registerJs($js);

			$js = <<< JS
					
                	$(document).on("click", "#modal-$this->id .crop-submit", function(e) {                       
                    e.preventDefault();
                    
                    cropBoxData = image.cropper('getCropBoxData');              
                    canvasData = image.cropper('getCanvasData');   
                    $("#image_id-$this->id").attr("value", window.idImage);     
					var cropData = JSON.stringify(image.cropper("getData"));
					$("#imageCrop-$this->id").attr("value", cropData);
                    
                    var imageData = JSON.stringify({
                        modelName: "$this->modelName",
                        id: "$this->id",
                        object_id: "$this->object_id",
                        image_id: window.idImage,
                        images_num: "$this->images_num",
                        images_label: "$this->images_label",
                        buttonClass: "$this->buttonClass",
                        previewSize: "$this->previewSize",
                        images_temp: "$this->images_temp",
                        imageSmallWidth: "$this->imageSmallWidth",
                        imageSmallHeight: "$this->imageSmallHeight",
                        createImageText:  "$this->createImageText",
                        updateImageText:  "$this->updateImageText",
                        deleteImageText:  "$this->deleteImageText",
                        deleteUrl: "$this->deleteUrl",
                        frontendUrl: "$this->frontendUrl",
                        baseUrl: "$this->baseUrl",
                        imagePath: "$this->imagePath",
                        noImage: "$this->noImage",
                        loaderImage: "$this->loaderImage",
                        backend: "$this->backend",
                        imageClass: "$imageClass",
                        buttonDeleteClass: "$buttonDeleteClass",
                        imageContainerClass: "$imageContainerClass",
                        formImagesContainerClass: "$formImagesContainerClass",
                        imageCrop: cropData
                    });
                    
                    var formdata = new FormData();
                   
                    formdata.append('imageData', imageData);
                    formdata.append('ImageForm[image]', document.getElementById('imageform-image-$this->id').files[0]);
                       
                    $.ajax({
                        type: "POST",
                        url: "$this->autoloadUrl",
                        data: formdata,
                        processData: false,
        				contentType: false,
                        /*container: "#images-widget-$this->id",
                        scrollTo: false,
                        push: false,*/
                        success: function(data){                           
                            var json = $.parseJSON(data);
                            id_image = json[0]["id"];
                            del_butt = "<button type=\"button\" id=\"button-delete-image-id-"+ id_image + "\" class=\"btn btn-xs btn-danger btn-imageDelete glyphicon glyphicon-trash glyphicon\" onclick=\"window.idImage = '"+id_image+"'; deleteImage(event);\"></button>";
							upd_butt = "<button type=\"button\" class=\"btm btn-info\" style=\"width: 100%;\" onclick=\"window.idImage = '"+id_image+"'; $('#imageform-image-load-action-image').click();\">Изменить фото</button>";
									
                            if (window.idImage > 0) {
							  $("#preview-image-f-"+window.idImage).attr("src", json[0]["file_small"]);							
							  $("#button-delete-image-id-" + window.idImage).replaceWith(del_butt);
							  $("#change-image-btn").replaceWith(upd_butt);
       						} else {
                              $(del_butt).insertBefore("#preview-image-f");
                              $("#preview-image-f").attr("src", json[0]["file_small"])
                              .attr('id', '#preview-image-f-' + id_image);	
							  $("#change-image-btn").replaceWith(upd_butt);
       						}
                        }
                    });
                    modalBox.modal("hide");                                    
                });
JS;
			$view->registerJs($js);

		}
	}
