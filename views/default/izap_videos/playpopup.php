<?php

$video_guid = get_input('guid', false);

if (!$video_guid) {
	return true;
}

$video = get_entity($video_guid);

if (!elgg_instanceof($video, 'object', 'izap_videos')) {
	return true;
}

$video->updateViews();

$owner_link = elgg_view('output/url', array(
	'href' => "videos/owner/" . $video->getOwnerEntity()->username,
	'text' => $video->getOwnerEntity()->name,
));
$author_text = elgg_echo('byline', array($owner_link));
$date = elgg_view_friendly_time($video->time_created);

$comments_count = $video->countComments();
//only display if there are commments
if ($comments_count != 0) {
	$text = elgg_echo("comments") . " ($comments_count)";
	$comments_link = elgg_view('output/url', array(
		'href' => $video->getURL() . '#comments',
		'text' => $text,
		'is_trusted' => true,
	));
} else {
	$comments_link = '';
}

$owner_icon = elgg_view_entity_icon($video->getOwnerEntity(), 'tiny');

$subtitle = "$author_text $date $comments_link";

$params = array(
	'entity' => $video,
	'title' => false,
	'metadata' => '',
	'subtitle' => $subtitle,
	'tags' => false,
);
$list_body = elgg_view('object/elements/summary', $params);

$params = array('class' => 'mbs');
$summary = elgg_view_image_block($owner_icon, $list_body, $params);

$title = elgg_view_title($video->title);

echo '<div style="max-width:640px;">' . $title . $summary;

// Display the video player to allow for the video to be played
echo '<div align="center" class="izapPlayer">';
echo '<div class="mbm">' . $video->getPlayer() . '</div>';
echo '<div class="mbm">' . elgg_view('output/url', array(
	'href' => $video->getURL() . '#comments',
	'text' => elgg_echo('generic_comments:add'),
	'is_trusted' => true,
	'class' => 'elgg-button elgg-button-action'
)) . '</div>';
echo '</div></div>';
