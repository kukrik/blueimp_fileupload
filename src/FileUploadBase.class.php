<?php

namespace QCubed\Plugin;

use QCubed as Q;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Action\JavaScript;
use QCubed\Event\Click;
use QCubed\Html;
use QCubed\Type;
use QCubed\Js;
use QCubed\Plugin\Event\FileUploadProgressAll;
use QCubed\Plugin\Event\FileUploadDone;
use QCubed\Plugin\Event\FileUploadAdd;
use QCubed\Plugin\Event\FileUploadProcessAlways;
use QCubed\Plugin\Event\FileUploadProcessFail;

//use QCubed\Plugin\UploadHandler;

/**
 * Class FileUploadBase
 *
 * @property string $UploadDir
 * @property string $UploadTempDir
 * @property string $SelectedFileTypes
 * @property boolean $PerUserDirs
 * @property array $UploadHandler
 */
class FileUploadBase extends \QCubed\Plugin\FileUploadGen
{
    public function __construct($objParentObject, $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);
        $this->Url = '';

        $this->registerFiles();
        $this->blnUseWrapper = false;

        $this->setupDefaultEventHandlers();
    }

    protected function registerFiles()
    {
        $this->AddCssFile(QCUBED_BOOTSTRAP_CSS); // make sure they know
        $this->addCssFile("https://blueimp.github.io/Gallery/css/blueimp-gallery.min.css");
        $this->addCssFile(QCUBED_FILEUPLOAD_ASSETS_URL . "/css/jquery.fileupload.css");
        $this->addCssFile(QCUBED_FILEUPLOAD_ASSETS_URL . "/css/jquery.fileupload-ui.css");
        $this->addJavascriptFile(QCUBED_FILEUPLOAD_ASSETS_URL . "/js/vendor/jquery.ui.widget.js");
        $this->addJavascriptFile("https://blueimp.github.io/JavaScript-Templates/js/tmpl.min.js");
        $this->addJavascriptFile("https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js");
        $this->addJavascriptFile("https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js");
        $this->addJavascriptFile("https://blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js");
        $this->addJavascriptFile(QCUBED_FILEUPLOAD_ASSETS_URL . "/js/jquery.iframe-transport.js");
        $this->addJavascriptFile(QCUBED_FILEUPLOAD_ASSETS_URL . "/js/jquery.fileupload.js");
        $this->addJavascriptFile(QCUBED_FILEUPLOAD_ASSETS_URL . "/js/jquery.fileupload-process.js");
        $this->addJavascriptFile(QCUBED_FILEUPLOAD_ASSETS_URL . "/js/jquery.fileupload-image.js");
        $this->addJavascriptFile(QCUBED_FILEUPLOAD_ASSETS_URL . "/js/jquery.fileupload-audio.js");
        $this->addJavascriptFile(QCUBED_FILEUPLOAD_ASSETS_URL . "/js/jquery.fileupload-video.js");
        $this->addJavascriptFile(QCUBED_FILEUPLOAD_ASSETS_URL . "/js/jquery.fileupload-validate.js");
        $this->addJavascriptFile(QCUBED_FILEUPLOAD_ASSETS_URL . "/js/jquery.fileupload-ui.js");
        $this->addJavascriptFile(QCUBED_FILEUPLOAD_ASSETS_URL . "/js/cors/jquery.xdr-transport.js");
    }

    protected function setupDefaultEventHandlers()
    {
        $this->setupAddHandler();
    }

    protected function setupAddHandler()
    {
        $objAction = new JavaScript('

        console.log(ui.files);

        //console.log("add");
                 jQuery.each(ui.files, function (index, file) {
                    //console.log("Added file: " + file.name);
                    alert(jQuery("#template-upload").tmpl(ui));
                    //jQuery("#template-upload").appendTo(".files");
                });
                ');
        $this->addAction(new FileUploadAdd(), $objAction);
    }

    public function getUploadTemplate()
    {
        $strEditLabel = t('Edit');
        $strStartLabel = t('Start');
        $strCancelLabel = t('Cancel');
        $strProcessingLabel = t('Processing...'); // {$this->strParamName}

        $strResult = <<<SCRIPT
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade{%=o.options.loadImageFileTypes.test(file.type)?' image':''%}">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">$strProcessingLabel</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!o.options.autoUpload && o.options.edit && o.options.loadImageFileTypes.test(file.type)) { %}
                <button class="btn btn-success edit" data-index="{%=i%}" disabled>
                    <span>$strEditLabel</span>
                </button>
            {% } %}
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <span>$strStartLabel</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <span>$strCancelLabel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
SCRIPT;
        return $strResult;
    }

    public function getDownloadTemplate()
    {
        $strErrorLabel = t('Error');
        $strDeleteLabel = t('Delete');
        $strCancelLabel = t('Cancel'); // {$this->strParamName}

        $strResult = <<<SCRIPT
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade{%=file.thumbnailUrl?' image':''%}">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">$strErrorLabel</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <span>$strDeleteLabel</span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <span>$strCancelLabel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
SCRIPT;
        return $strResult;
    }

    protected function getControlHtml()
    {
        $attrOverride = array('type'=>'file', 'name'=> 'files[]');
        $strFileControlHtml = $this->renderTag('input',
            $attrOverride,
            null,
            null,
            true);

        $strAddFilesLabel = t('Add files...');
        $strStartUploadLabel = t('Start upload');
        $strCancelUploadLabel = t('Cancel upload');
        $strDeleteLabel = t('Delete selected');

        $strResult = <<<SCRIPT
<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
<div class="row fileupload-buttonbar">
	<div class="col-lg-7">
	    <!-- The fileinput-button span is used to style the file input field as button -->
		<span class="btn btn-success fileinput-button">
			<span>$strAddFilesLabel</span>
			$strFileControlHtml
		</span>
		<button type="submit" class="btn btn-primary start">
			<span>$strStartUploadLabel</span>
		</button>
		<button type="reset" class="btn btn-warning cancel">
			<span>$strCancelUploadLabel</span>
		</button>
		<button type="button" class="btn btn-danger delete">
			<span>$strDeleteLabel</span>
		</button>
		<input type="checkbox" class="toggle" />

        <!-- The global file processing state -->
        <span class="fileupload-process"></span>
    </div>

    <!-- The global progress state -->
    <div class="col-lg-5 fileupload-progress fade">

        <!-- The global progress bar -->
        <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar progress-bar-success" style="width: 0%;">
            </div>
        </div>

        <!-- The extended global progress state -->
        <div class="progress-extended">&nbsp;</div>
    </div>
</div>

<!-- The table listing the files available for upload/download -->
<table role="presentation" class="table table-striped">
    <tbody class="files"></tbody>
</table>

<!-- The blueimp Gallery widget -->
<div
    id="blueimp-gallery"
    class="blueimp-gallery blueimp-gallery-controls"
    aria-label="image gallery"
    aria-modal="true"
    role="dialog"
    data-filter=":even"
   >
<div class="slides" aria-live="polite"></div>
    <h3 class="title"></h3>
    <a
      class="prev"
      aria-controls="blueimp-gallery"
      aria-label="previous slide"
      aria-keyshortcuts="ArrowLeft"
    ></a>
    <a
      class="next"
      aria-controls="blueimp-gallery"
      aria-label="next slide"
      aria-keyshortcuts="ArrowRight"
    ></a>
    <a
      class="close"
      aria-controls="blueimp-gallery"
      aria-label="close"
      aria-keyshortcuts="Escape"
    ></a>
    <a
      class="play-pause"
      aria-controls="blueimp-gallery"
      aria-label="play slideshow"
      aria-keyshortcuts="Space"
      aria-pressed="false"
      role="button"
    ></a>
    <ol class="indicator"></ol>
</div>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
    {$this->getUploadTemplate()}
</script>

<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
    {$this->getDownloadTemplate()}
</script>
SCRIPT;
        return $strResult;
    }

}

