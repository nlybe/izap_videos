<?php

/**
 * Videos with highest vote count - world view only
 *
 */

elgg_register_title_button('izap_videos', 'add', 'object', 'izap_videos');

$title = elgg_echo('collection:object:izap_videos:highestvotecount');

elgg_push_collection_breadcrumbs('object', 'izap_videos');
elgg_push_breadcrumb($title);

$offset = (int) elgg_extract('offset', $vars);
$limit = (int) elgg_extract('limit', $vars);

$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'limit' => $limit,
	'offset' => $offset,
	'annotation_name' => 'fivestar',
	'annotation_sort_by_calculation' => 'count',
	'order_by' => [
		new \Elgg\Database\Clauses\OrderByClause('annotation_calculation', 'DESC'),
	],
	'full_view' => false,
	'no_results' => elgg_echo('izap_videos:highestvotecount:nosuccess'),
]);

$body = elgg_view_layout('default', [
	'filter' => '',
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'all']),
]);

// Draw it
echo elgg_view_page($title, $body);
