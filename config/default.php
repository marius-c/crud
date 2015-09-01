<?php

return [
    'path' => [
        'storage' => __DIR__.'/../storage'
    ],
	'view' => [
		'paths' => [
			__DIR__.'/../src/View/views'
		],
		'compiled' => __DIR__.'/../storage/views'
	],
	'app' => [
		'locale' => 'en',
		'fallback_locale' => 'en',
		'key' => 'F1ejeEGoiSTu6GRtZQTrLjMxWLIZ4DFI',
		'cipher' => 'AES-256-CBC'
	],
    'attachments' => [
        'model' => 'App\Models\Attachment',
        'upload' => [
            'url' => '/attachments/upload'
        ]
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