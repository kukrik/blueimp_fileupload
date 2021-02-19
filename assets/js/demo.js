/*
 * jQuery File Upload Demo
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

/* global $ */

jQuery(function () {
  'use strict';

   //Initialize the jQuery File Upload widget:
  jQuery('#c1').fileupload({
    // Uncomment the following to send cross-domain cookies:
    //xhrFields: {withCredentials: true},
    //url: 'server/php/'

    url: 'http://localhost/qcubed-4/vendor/kukrik/blueimp_fileupload/examples/fileupload.php'
  });

  //$('#fileupload').fileupload({
  //  url: 'server/php/index.php'
  //}).on('fileuploadsubmit', function (e, data) {
  //  data.formData = data.context.find(':input').serializeArray();
  //});

  // Enable iframe cross-domain access via redirect option:
  jQuery('#c1').fileupload(
    'option',
    'redirect',
    window.location.href.replace(/\/[^/]*jQuery/, '/cors/result.html?%s')
  );

  if (window.location.hostname === 'demo-test') {
    // Demo settings:
    jQuery('#c1').fileupload('option', {
      //url: '//jquery-file-upload.appspot.com/',

      url: 'http://localhost/qcubed-4/',
      // Enable image resizing, except for Android and Opera,
      // which actually support image resizing, but fail to
      // send Blob objects via XHR requests:
      disableImageResize: /Android(?!.*Chrome)|Opera/.test(
        window.navigator.userAgent
      ),
      //maxFileSize: 999000,
      //acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
      acceptFileTypes: /(\.|\/)(gif|jpe?g|png|pdf|doc|docx|xls|xlsx)$/i
    });
    // Upload server status check for browsers with CORS support:
    if (jQuery.support.cors) {
      jQuery.ajax({
        url: '//demo-test/',
        type: 'HEAD'
      }).fail(function () {
        jQuery('<div class="alert alert-danger"></div>')
          .text('Upload server currently unavailable - ' + new Date())
          .appendTo('#c1');
      });
    }
  } else {

    console.log(window.location.hostname);
    // Load existing files:
    jQuery('#c1').addClass('fileupload-processing');
    jQuery.ajax({
      // Uncomment the following to send cross-domain cookies:
      //xhrFields: {withCredentials: true},
      url: jQuery('#c1').fileupload('option', 'url'),
      dataType: 'json',
      //dataType: 'post',
      context: jQuery('#c1')[0]
    })
      .always(function () {
        jQuery(this).removeClass('fileupload-processing');
      })
      .done(function (result) {
        jQuery(this)
          .fileupload('option', 'done')
          // eslint-disable-next-line new-cap
          .call(this, jQuery.Event('done'), { result: result });


      });
  }
});
