<?php

return [
	'view' => [
		'paths' => [
			__DIR__.'/../src/View/views'
		],
		'compiled' => __DIR__.'/../storage/views'
	],
	'app' => [
		'locale' => 'en',
		'fallback_locale' => 'en',
	],
	'style' => [
		'css' => null,

		/**
		 * Incremented at the iframe size height
		 */
		'iframe_size_fix' => 5,
		'theme' => 'semantic'
	],
	'query_log' => true
];