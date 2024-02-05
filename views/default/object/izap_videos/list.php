<?php

$video = elgg_extract('entity', $vars, false);

if (!$video instanceof \IzapVideos) {
	return true;
}

$excerpt = $video->description ? elgg_get_excerpt($video->description) : '';

if (elgg_in_context('widgets') || elgg_in_context('front') || elgg_in_context('groups')) {
	$size = 'small';
} else {
	$size = 'medium';
}
$video_icon = $video->getThumb($size);

$params = [
	'entity' => $video,
	'content' => $excerpt,
];
$params = $params + $vars;
$list_body = elgg_view('object/elements/summary', $params);

echo elgg_view_image_block($video_icon, $list_body);
