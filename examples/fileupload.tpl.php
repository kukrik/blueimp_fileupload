<?php require('header.inc.php'); ?>

<?php $this->RenderBegin(); ?>

<h1 id="title">Using Blueimp FileUpload</h1>
<blockquote id="description">
    <p>
        File Upload widget with multiple file selection, drag&amp;drop
        support, progress bars, validation and preview images, audio and video
        for jQuery.<br />
        Supports cross-domain, chunked and resumable file uploads and
        client-side image resizing.<br />
        Works with any server-side platform (PHP, Python, Ruby on Rails, Java,
        Node.js, Go etc.) that supports standard HTML form file uploads.
    </p>
</blockquote>
    <?= _r($this->objFileUpload); ?>

    <?= _r($this->lblStatus); ?>



<?php $this->RenderEnd(); ?>
<?php require('footer.inc.php'); ?>
