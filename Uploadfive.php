<?php
namespace bignerd\extension;
use yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use bignerd\extension\UploadfiveAsset;

class Uploadfive extends InputWidget
{
	/** 绑定的上传input id */
	public $targetId = '';
	/** 是否多文件上传  */
	public $multi = 'false';
	/** 上传文件folder*/
	public $category = 'image';
	/** 按钮文字 */
	public $buttonText = '选择文件';
	/** 上传按钮的宽度 */
	public $width = 120;
	/** 上传按钮的高度 */
	public $height = 40;
	/** 文件上传大小限制 */
	public $fileSizeLimit = '2MB';
	/** 绑定上传按钮的 input hidden id */
	public $hiddenInputId = 'upload';
	/** 文件扩展名 */
	public $fileTypeExts = '*.jpg;*.png;*.gif,*.mp4;';
	/** 上传成功后的回调方法名，空则只对绑定到的input赋值 */
	public $jsCallbackFunc = "";
	/** 文件上传七牛需要用到 */
	public $upToken = "";
	/** 上传文件到七牛服务器地址 */
	public $qiniuServer = "http://up.qiniu.com/";

	public $upload2qiniu = false;
	/** 上传文件表单名 */
	private $fileObjName  = 'Filedata';
	private $extensionPath = '/vendor/bignerd/yii2-uploadify';

	public function init()
	{
		parent::init();

		if (empty($this->targetId)) {
			$this->targetId = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
		}
		//未设置input name
		if(!$this->name){
			$formName = $this->model->formName();
			$attribute = $this->attribute;
			$this->name = $formName.'['.$attribute.']';
		}
	}

	public function run()
	{
		echo "<input class='form-control' type='text' name='".$this->name."' id='".$this->targetId."' />";

		$category = $this->category;

		$view = $this->getView();
		/** 加载uploadfive所需资源 */
		UploadfiveAsset::register($view);

		// $view->registerCssFile(Yii::getAlias('@pluginPath').'/uploadifive/uploadifive.css',['position' => \yii\web\View::POS_HEAD]);

		echo '<input type="hidden" id="'.$this->hiddenInputId.'" />';

		/** 处理上传 */
		$uploader = $this->extensionPath.'/assets/uploadifive/uploadifive.php';
		if($this->upload2qiniu){
			$uploader = $this->qiniuServer;
			$this->fileObjName = 'file';
		}
		// $uploader = Yii::getAlias('@pluginPath').'/uploadify/uploadify.php';
		$view->registerJs('
			$("#'.$this->hiddenInputId.'").uploadifive({
				"onSelect" : function(file){

	            },
	            "auto":true,
				// "fileObjName" : "'.$this->fileObjName.'",
	            "uploadScript" : "'.$uploader.'",
	            "buttonText":"'.$this->buttonText.'",
	            "method"   : "POST",
	            "formData" : { "path": "'.$this->category.'" },
	            "multi" : '.$this->multi.',
	            "height":'.$this->height.',
	            "width":'.$this->width.',
	            "fileSizeLimit" : "'.$this->fileSizeLimit.'",
	            "fileTypeExts" : "'.$this->fileTypeExts.'",
	            "onUploadComplete" : function(file, url) {
						if(url == "0"){
							alert("上传出错");
							return;
						}else{
							var data = $.parseJSON(url);
							console.log(data);
							$("#'.$this->targetId.'").val(data.fileName);
							var callbackFunc = "'.$this->jsCallbackFunc.'";
							//回调自定义js方法
							if(callbackFunc !== ""){
								callbackFunc(data);
							}
						}
                },
		        "onFallback" : function() {
		            alert("Flash was not detected.");
		        },
	            "onSelectError" : function(file,errorCode,errorMsg) {
					switch (errorCode) {
						case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
							this.queueData.errorMsg = "上传图片格式不合法";
							break;
						case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
							this.queueData.errorMsg = "上传尺寸最大"+this.settings.fileSizeLimit;
							break;
					}
				}
			});
		');

	}
}

?>