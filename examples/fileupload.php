<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require('qcubed.inc.php');


use QCubed as Q;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase as Form;

class ExamplesForm extends Form
{
    protected $objFileUpload;
    protected $lblStatus;

    protected function formCreate()
    {
        $this->objFileUpload = new Q\Plugin\FileUpload($this);
        $this->objFileUpload->setHtmlAttribute('multiple', 'multiple');
        $this->objFileUpload->DataType= 'json';
        //$this->objFileUpload->AcceptFileTypes = '/(\.|\/)(gif|jpeg|jpg|png)$/i';
        //$this->objFileUpload->MaxFileSize = 5000000;
    }

//    public function uploadDone($strFormId, $strControlId, $strParameter) {
//        $this->lblStatus->Text = 'Upload Done!';
//    }

}


ExamplesForm::run('ExamplesForm');