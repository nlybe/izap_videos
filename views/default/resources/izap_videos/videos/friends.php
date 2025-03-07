<?php

$owner = elgg_get_page_owner_entity();

if (!$owner) {
	$guid = elgg_extract('guid', $vars);
	$owner = get_user(intval($guid));
}

if (!$owner) {
	$username = elgg_extract('username', $vars);
	$owner = get_user_by_username($username);
}

if (!$owner) {
	$owner = elgg_get_logged_in_user_entity();
}

if (!($owner instanceof ElggUser)) {
	throw new \Elgg\EntityNotFoundException();
}

elgg_push_collection_breadcrumbs('object', 'izap_videos', $owner, true);

elgg_register_title_button('add', 'object', 'izap_videos');

$title = elgg_echo('collection:friends', [elgg_echo('collection:object:izap_videos')]);


$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'relationship' => 'friend',
	'relationship_guid' => (int) $owner->guid,
	'relationship_join_on' => 'owner_guid',
	'full_view' => false,
	'distinct' => false,
	'pagination' => true,
	'list_type_toggle' => false,
	'no_results' => elgg_echo('izap_videos:notfound'),
]);

echo elgg_view_page($title, [
    'content' => $result,
    'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'friends']),
	'filter_id' => 'izap_videos_tabs',
	'filter_value' => 'friends',
]);
