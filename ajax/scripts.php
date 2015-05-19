<?php

if($scripts == 'uploader'){
	$plugin_url_uploader = 	plugins_url( '/js/blueimp-jQuery-File-Upload-d45deb1/', dirname(__FILE__) );
?>
	<script src="<?php echo $plugin_url_uploader.'js/vendor/jquery.ui.widget.js';?>"></script>
	<script src="<?php echo $plugin_url_uploader.'js/load-image.all.min.js';?>"></script>
	<script src="<?php echo $plugin_url_uploader.'js/canvas-to-blob.min.js';?>"></script>
	<script src="<?php echo $plugin_url_uploader.'js/jquery.iframe-transport.js';?>"></script>
	<script src="<?php echo $plugin_url_uploader.'js/jquery.fileupload.js';?>"></script>
	<script src="<?php echo $plugin_url_uploader.'js/jquery.fileupload-process.js';?>"></script>
	<script src="<?php echo $plugin_url_uploader.'js/jquery.fileupload-image.js';?>"></script>
	<script src="<?php echo $plugin_url_uploader.'js/jquery.fileupload-validate.js';?>"></script>
<?	
}