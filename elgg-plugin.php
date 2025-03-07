<?php

require_once(dirname(__FILE__) . '/lib/settings.php');
require_once(dirname(__FILE__) . '/lib/functions.php');
require_once(dirname(__FILE__) . '/lib/events.php');

return [
    'plugin' => [
        'name' => 'iZAP Videos',
		'version' => '5.4.0',
		'dependencies' => [
			'widget_manager' => [
				'must_be_active' => false,
            ],
			'elggx_fivestar' => [
				'must_be_active' => false,
            ],
        ],
	],	
	'bootstrap' => \IzapVideosBootstrap::class,
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'izap_videos',
			'class' => 'IzapVideos',
            'capabilities' => [
				'commentable' => true,
				'searchable' => true,
				'likable' => true,
			],
		],
	],
	'actions' => [
		'izap_videos/settings/save' => [],
		'izap_videos/admin/api_keys' => ['access' => 'admin'],
		'izap_videos/admin/resetSettings' => ['access' => 'admin'],
		'izap_videos/admin/recycle' => ['access' => 'admin'],
		'izap_videos/admin/recycle_delete' => ['access' => 'admin'],
		'izap_videos/admin/reset' => ['access' => 'admin'],
		'izap_videos/admin/upgrade' => ['access' => 'admin'],
		'izap_videos/addEdit' => [
			'access' => 'logged_in',
		],
		'izap_videos/delete' => [
			'access' => 'logged_in',
		],
		'izap_videos/favorite_video' => [
			'access' => 'logged_in',
		],
	],
	'settings' => [
		'izap_cron_time' => 'minute',
		'izapVideoOptions' => 'OFFSERVER',
		'izapPhpInterpreter' => '/usr/bin/php',
		'izapVideoCommand' => '/usr/bin/ffmpeg -y -i [inputVideoPath] [outputVideoPath]',
		'izapVideoThumb' => '/usr/bin/ffmpeg -y -i [inputVideoPath] -vframes 1 -ss 00:00:10 -an -vcodec png -f rawvideo -s 320x240',
		'izapMaxFileSize' => 5,
		'izapKeepOriginal' => 'YES',
		'izapExtendedSidebarMenu' => 'YES',
		'izap_river_thumbnails' => 'medium',
	],
	'routes' => [
		'collection:object:izap_videos:owner' => [
			'path' => '/videos/owner/{username?}',
			'resource' => 'izap_videos/videos/owner',
		],
		'collection:object:izap_videos:friends' => [
			'path' => '/videos/friends/{username?}/{guid?}',
			'resource' => 'izap_videos/videos/friends',
			'middleware' => [
				\Elgg\Router\Middleware\Gatekeeper::class,
			],
		],
		'collection:object:izap_videos:favorites' => [
			'path' => '/videos/favorites/{username?}/{guid?}',
			'resource' => 'izap_videos/videos/favorites',
			'middleware' => [
				\Elgg\Router\Middleware\Gatekeeper::class,
			],
		],
		'view:object:izap_videos' => [
			'path' => '/videos/play/{username}/{guid}/{title?}',
			'resource' => 'izap_videos/videos/play',
			'requirements' => [
				'username' => '[\p{L}\p{Nd}:._-]+', // only allow valid usernames and : (group video uses "group:<group_guid>" as "username")
			],
		],
		'thumbs:object:izap_videos' => [
			'path' => '/videos/thumbs',
			'resource' => 'izap_videos/videos/thumbs',
		],
		'add:object:izap_videos' => [
			'path' => '/videos/add/{guid?}',
			'resource' => 'izap_videos/videos/add',
			'middleware' => [
				\Elgg\Router\Middleware\Gatekeeper::class,
			],
		],
		'edit:object:izap_videos' => [
			'path' => '/videos/edit/{guid}',
			'resource' => 'izap_videos/videos/edit',
			'middleware' => [
				\Elgg\Router\Middleware\Gatekeeper::class,
			],
		],
		'collection:object:izap_videos:mostviewed' => [
			'path' => '/videos/mostviewed',
			'resource' => 'izap_videos/lists/mostviewedvideos',
		],
		'collection:object:izap_videos:mostviewedtoday' => [
			'path' => '/videos/mostviewedtoday',
			'resource' => 'izap_videos/lists/mostviewedvideostoday',
		],
		'collection:object:izap_videos:mostviewedthismonth' => [
			'path' => '/videos/mostviewedthismonth',
			'resource' => 'izap_videos/lists/mostviewedvideosthismonth',
		],
		'collection:object:izap_videos:mostviewedlastmonth' => [
			'path' => '/videos/mostviewedlastmonth',
			'resource' => 'izap_videos/lists/mostviewedvideoslastmonth',
		],
		'collection:object:izap_videos:mostviewedthisyear' => [
			'path' => '/videos/mostviewedthisyear',
			'resource' => 'izap_videos/lists/mostviewedvideosthisyear',
		],
		'collection:object:izap_videos:mostcommented' => [
			'path' => '/videos/mostcommented',
			'resource' => 'izap_videos/lists/mostcommentedvideos',
		],
		'collection:object:izap_videos:mostcommentedtoday' => [
			'path' => '/videos/mostcommentedtoday',
			'resource' => 'izap_videos/lists/mostcommentedvideostoday',
		],
		'collection:object:izap_videos:mostcommentedthismonth' => [
			'path' => '/videos/mostcommentedthismonth',
			'resource' => 'izap_videos/lists/mostcommentedvideosthismonth',
		],
		'collection:object:izap_videos:mostcommentedlastmonth' => [
			'path' => '/videos/mostcommentedlastmonth',
			'resource' => 'izap_videos/lists/mostcommentedvideoslastmonth',
		],
		'collection:object:izap_videos:mostcommentedthisyear' => [
			'path' => '/videos/mostcommentedthisyear',
			'resource' => 'izap_videos/lists/mostcommentedvideosthisyear',
		],
		'collection:object:izap_videos:recentlyviewed' => [
			'path' => '/videos/recentlyviewed',
			'resource' => 'izap_videos/lists/recentlyviewed',
		],
		'collection:object:izap_videos:recentlycommented' => [
			'path' => '/videos/recentlycommented',
			'resource' => 'izap_videos/lists/recentlycommented',
		],
		'collection:object:izap_videos:recentvotes' => [
			'path' => '/videos/recentvotes',
			'resource' => 'izap_videos/lists/recentvotes',
			'required_plugins' => [
				'elggx_fivestar',
			],
		],
		'collection:object:izap_videos:highestrated' => [
			'path' => '/videos/highestrated',
			'resource' => 'izap_videos/lists/highestrated',
			'required_plugins' => [
				'elggx_fivestar',
			],
		],
		'collection:object:izap_videos:highestvotecount' => [
			'path' => '/videos/highestvotecount',
			'resource' => 'izap_videos/lists/highestvotecount',
			'required_plugins' => [
				'elggx_fivestar',
			],
		],
		'collection:object:izap_videos:group' => [
			'path' => '/videos/group/{guid}',
			'resource' => 'izap_videos/videos/group',
			'required_plugins' => [
				'groups',
			],
		],
		'collection:object:izap_videos:all' => [
			'path' => '/videos/all',
			'resource' => 'izap_videos/videos/all',
		],
		'default:object:izap_videos' => [
			'path' => '/videos',
			'resource' => 'izap_videos/videos/all',
		],
		'thumbs:izap_videos_files' => [
			'path' => '/izap_videos_files/{what}/{videoID}',
			'resource' => 'izap_videos/videos/thumbs',
		],
		'default:izap_videos_files' => [
			'path' => '/izap_videos_files',
			'resource' => 'izap_videos/videos/thumbs',
		],	
	],
    'events' => [
        'entity:url' => [
            'object' => [
				'izap_videos_widget_urls' => [],
                'izap_videos_urlhandler' => [],
            ],
        ],
        'register' => [        
            'menu:social' => [
                'izap_videos_social_menu_setup' => [],
            ],      
            'menu:owner_block' => [
                'izap_videos_owner_block_menu' => [],
            ],     
            'menu:entity' => [
                'izap_videos_entity_menu_setup' => [],
            ],     
            'menu:filter:izap_videos_tabs' => [
                'izap_videos_setup_tabs' => [],
            ],
        ],
        'view' => [ 
            'river/object/comment/create' => [
                'izap_videos_river_comment' => [],
            ],
        ],
        'prepare' => [ 
            'notification:create:object:izap_videos' => [
                'izap_videos_notify_message' => [],
            ],
        ],
        'group_tool_widgets' => [ 
            'widget_manager' => [
                'izap_videos_tool_widget_handler' => [],
            ],
        ],
    ],
	'widgets' => [
		'izap_videos' => [
			'context' => ['profile', 'dashboard'],
		],
		'index_latest_videos' => [
			'context' => ['index'],
		],
		'groups_latest_videos' => [
			'context' => ['groups'],
		],
	],
	'views' => [
		'default' => [
			'izap_videos/' => __DIR__ . '/graphics',
			'izap_videos_videojs/' => __DIR__ . '/vendors/videojs',
		],
	],
	'view_extensions' => [
		'css/elgg' => [
			'izap_videos/css' => [],
		],
		'css/admin' => [
			'izap_videos/css' => [],
		],
	],
];