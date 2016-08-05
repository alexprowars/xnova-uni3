<?php

$rules[] = array(
	'source'  => '/^(.+)\.html/i',
	'target'  => 'content/article/{1}',
	'action'  => 'rewrite'
);

?>