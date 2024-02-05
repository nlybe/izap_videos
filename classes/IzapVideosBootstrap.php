<?php

use Elgg\DefaultPluginBootstrap;

class IzapVideosBootstrap extends DefaultPluginBootstrap {

	public function init() {
		elgg_register_ajax_view('izap_videos/admin/getQueue');
		elgg_register_ajax_view('izap_videos/playpopup');

		// Register video.js stuff
		elgg_define_js('izap_videos_videojs_js', [
			'src' => elgg_get_simplecache_url('izap_videos_videojs/video.min.js'),
		]);

		// Set up the site menu
		elgg_register_menu_item('site', [
			'name' => 'videos',
			'icon' => 'video-camera',
			'text' => elgg_echo('collection:object:izap_videos'),
			'href' => elgg_generate_url('collection:object:izap_videos:all'),
		]);

		// Add admin menu item
		elgg_register_menu_item('page', [
			'name' => 'administer_utilities:izap_videos',
			'href' => 'admin/administer_utilities/izap_videos',
			'text' => elgg_echo('admin:administer_utilities:izap_videos'),
			'context' => 'admin',
			'parent_name' => 'administer_utilities',
			'section' => 'administer'
		]);

		// Register notification hook
		elgg_register_notification_event('object', 'izap_videos', ['create']);

		// Register cronjob that triggers on-site video conversion
		$period = izapAdminSettings_izap_videos('izap_cron_time');
		if ($period != 'none') {
			elgg_register_event_handler('cron', $period, 'izap_queue_cron');
		}

		// Group videos
		elgg()->group_tools->register('izap_videos', [
			'default_on' => false,
			'label' => elgg_echo('izap_videos:group:enablevideo'),
		]);
	}

	public function activate() {
		// save current version number
		$old_version_izap_videos = elgg_get_plugin_setting('version_izap_videos', 'izap_videos');
		$new_version_izap_videos = '3.3.1';
		if (version_compare($new_version_izap_videos, $old_version_izap_videos, '!=')) {
			// Set new version
			$plugin = elgg_get_plugin_from_id('izap_videos');
			$plugin->setSetting('version_izap_videos', $new_version_izap_videos);
		}	
	}
}
