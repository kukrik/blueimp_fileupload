<?php
// This example header.inc.php is intended to be modfied for your application.

use QCubed as Q;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?php echo(QCUBED_ENCODING); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<?php if (isset($strPageTitle)) { ?>
    <title><?php _p(Q\QString::htmlEntities($strPageTitle)); ?></title><?php } ?>
    <link href="http://www.ead.ee/img/icons/favicon.ico" rel="shortcut icon" />
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700&subset=all" rel="stylesheet" type="text/css"/>
<?php if (isset($this)) {
    $this->renderStyles();
    } else { // for start page and other pages without form ?>
    <link href="<?= QCUBED_CSS_URL ?>/qcubed.css" rel="stylesheet">
<?php } ?>
    <style>
        body {font-size: 14px;}
        p, footer {font-size: medium;}
        #title {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            margin-top: 30px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">