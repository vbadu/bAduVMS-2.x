<?php
//url解析扩展函数
/*
.htaccess

<IfModule mod_rewrite.c>
RewriteEngine on
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php
</IfModule>

*/
if( !function_exists('url_parse_ext')) {
	function url_parse_ext(){
		return url_ext();
	}
}

?>