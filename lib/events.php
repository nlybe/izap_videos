<?php

/**
 * Add a menu item to an ownerblock
 */
function izap_videos_owner_block_menu(\Elgg\Event $event) {
	$menu = $event->getValue();
	$entity = $event->getParam('entity');

	if ($entity instanceof \ElggUser) {
		$url = "videos/owner/{$entity->username}";
		$item = new ElggMenuItem('izap_videos', elgg_echo('videos'), $url);
		$menu[] = $item;
	} else {
		if ($entity->isToolEnabled('izap_videos')) {
			$url = "videos/group/{$entity->guid}";
			$item = new ElggMenuItem('izap_videos', elgg_echo('collection:object:izap_videos:group'), $url);
			$menu[] = $item;
		}
	}

	return $menu;
}

/**
 * Add entries to entity menu
 */
function izap_videos_entity_menu_setup(\Elgg\Event $event) {
	$menu = $event->getValue();

	$entity = $event->getParam('entity');

	if (!($entity instanceof IzapVideos)) {
		return $menu;
	}

	foreach ($menu as $key => $item) {
		switch ($item->getName()) {
			case 'delete':
				$item->setHref(elgg_get_site_url() . 'action/izap_videos/delete?guid=' . $entity->getGUID());
				break;
			case 'edit':
				if (!($entity->converted == 'yes')) {
					unset($menu[$key]);
				}
				break;
		}
	}

	return $menu;
}

/**
 * Add entries to social menu
 */
function izap_videos_social_menu_setup(\Elgg\Event $event) {
	$menu = $event->getValue();

	if (elgg_in_context('widgets')) {
		return $menu;
	}

	$entity = $event->getParam('entity');
	if (!($entity instanceof \IzapVideos)) {
		return $menu;
	}

	if ($entity->converted == 'yes') {
		if (izap_is_my_favorited($entity)) {
			$menu[] = ElggMenuItem::factory([
				'name' => 'remove_favorite',
				'href' => elgg_get_site_url() . 'action/izap_videos/favorite_video?guid=' . $entity->guid . '&izap_action=remove',
				'text' => elgg_view('output/img', [
					'src' => elgg_get_simplecache_url('izap_videos/favorite_remove.png'), 
					'alt' => elgg_echo('izap_videos:remove_favorite'),
				]),
				'priority' => 80,
				'title' => elgg_echo('izap_videos:remove_favorite'),
				'is_action' => true,
				'is_trusted' => true,
			]);

		} else {
			$menu[] = ElggMenuItem::factory([
				'name' => 'make_favorite',
				'text' => elgg_view('output/img', [
					'src' => elgg_get_simplecache_url('izap_videos/favorite_add.png'), 
					'alt' => elgg_echo('izap_videos:save_favorite'),
				]),
				'href' => elgg_get_site_url() . 'action/izap_videos/favorite_video?guid=' . $entity->guid,
				'priority' => 80,
				'title' => elgg_echo('izap_videos:save_favorite'),
				'is_action' => true,
				'is_trusted' => true,
			]);
		}
	}

	$view_info = $entity->getViews();
	$view_info = (!$view_info) ? 0 : $view_info;
	$text = elgg_echo('izap_videos:views', [(int) $view_info]);
	$options = [
		'name' => 'views',
		'text' => elgg_format_element('span', [], $text),
		'href' => false,
		'priority' => 90,
	];
	$menu[] = ElggMenuItem::factory($options);

	return $menu;
}

/**
 * Returns the url for the video to play
 *
 */
function izap_videos_urlhandler(\Elgg\Event $event) {
	$entity = $event->getParam('entity');
	if ($entity instanceof \IzapVideos) {
		if (!$entity->getOwnerEntity()) {
			// default to a standard view if no owner.
			return false;
		}
		$container = get_entity($entity->container_guid);
		if ($container instanceof ElggUser) {
			$username = $container->username;
		} else if ($container instanceof ElggGroup) {
			$username = "group:" . $container->guid;
		} else {
			return false;
		}

		$url = elgg_generate_url('view:object:izap_videos', [
			'username' => $username,
			'guid' => $entity->guid,
			'title' => elgg_get_friendly_title($entity->title),
		]);

		return $url;
	}
}

function izap_videos_river_comment(\Elgg\Event $event) {
	$return_value = $event->getValue();
	$params = $event->getParams();

	$view = $params["view"];

	if ($view == 'river/object/comment/create') {
		$entity = $params['vars']['item']->getTargetEntity();
		if ($entity instanceof IzapVideos) {
			$return_value = elgg_view('river/object/comment/izap_videos', $params['vars']);
		}
	}
	return $return_value;
}

/**
 *
 * Prepare a notification message about a new video added to the site
 *
 */
function izap_videos_notify_message(\Elgg\Event $event) {
	$notification = $event->getValue();
	$params = $event->getParams();

	$entity = $params['event']->getObject();

	if ($entity instanceof IzapVideos) {
		$owner = $params['event']->getActor();
		$recipient = $params['recipient'];
		$language = $params['language'];
		$method = $params['method'];

		$descr = $entity->description;
		$title = $entity->title;

		$notification->subject = elgg_echo('izap_videos:notify:subject_newvideo', [$title], $language);
		$notification->body = elgg_echo('izap_videos:notify:body_newvideo', [$owner->name, $title, $entity->getURL()], $language);
		$notification->summary = elgg_echo('izap_videos:notify:summary_newvideo', [$title, $language]);

		return $notification;
	}
}

function izap_queue_cron(\Elgg\Event $event) {
	izapTrigger_izap_videos();
}

function izap_videos_widget_urls(\Elgg\Event $event) {
	$result = $event->getValue();
	$widget = $event->getParam('entity');

	if (empty($result) && ($widget instanceof ElggWidget)) {
		$owner = $widget->getOwnerEntity();
		switch ($widget->handler) {
			case "izap_videos":
				$result = "videos/owner/{$owner->username}";
				break;
			case "index_latest_videos":
				$result = "/videos/all";
				break;
			case "groups_latest_videos":
				if ($owner instanceof ElggGroup) {
					$result = "videos/group/{$owner->guid}";
				} else {
					$result = "videos/owner/{$owner->username}";
				}
				break;
		}
	}
	return $result;
}

// Add or remove a group's iZAP Videos widget based on the corresponding group tools option
function izap_videos_tool_widget_handler(\Elgg\Event $event) {
	$return_value = $event->getValue();
	$entity = $event->getParam('entity', false);

	if ($entity && ($entity instanceof ElggGroup)) {
		if (!is_array($return_value)) {
			$return_value = [];
		}

		if (!isset($return_value["enable"])) {
			$return_value["enable"] = [];
		}
		if (!isset($return_value["disable"])) {
			$return_value["disable"] = [];
		}

		if ($entity->izap_videos_enable == "yes") {
			$return_value["enable"][] = "groups_latest_videos";
		} else {
			$return_value["disable"][] = "groups_latest_videos";
		}
	}

	return $return_value;
}

/**
 * Add favorites tab to /videos/all /videos/mine /videos/friends /videos/favorites pages
 *
 * @param \Elgg\Event $event "register", "menu:filter:izap_videos_tabs"
 *
 * @return ElggMenuItem[]
 */
function izap_videos_setup_tabs(\Elgg\Event $event) {
	$result = $event->getValue();
	$filter_value = $event->getParam('filter_value');
	
	$result['all'] = \ElggMenuItem::factory([
		'name' => 'izap_videos_all_tab',
		'text' => elgg_echo('all'),
		'href' => elgg_generate_url('collection:object:izap_videos:all'),
		'selected' => $filter_value === 'all',
		'priority' => 200,
	]);
	$result['mine'] =\ElggMenuItem::factory([
		'name' => 'izap_videos_mine_tab',
		'text' => elgg_echo('mine'),
		'href' => elgg_generate_url('collection:object:izap_videos:owner'),
		'selected' => $filter_value === 'mine',
		'priority' => 300,
	]);
	$result['friends'] = \ElggMenuItem::factory([
		'name' => 'izap_videos_friends_tab',
		'text' => elgg_echo('friends'),
		'href' => elgg_generate_url('collection:object:izap_videos:friends'),
		'selected' => $filter_value === 'friends',
		'priority' => 400,
	]);
	$result['favorite'] = \ElggMenuItem::factory([
		'name' => 'izap_videos_favorites_tab',
		'text' => elgg_echo('izap_videos:favorites_short'),
		'href' => elgg_generate_url('collection:object:izap_videos:favorites'),
		'selected' => $filter_value === 'favorites',
		'priority' => 500,
	]);
	
	return $result;
}
