<?php

/**
 * Open Source Social Network
 *
 * @packageOpen Source Social Network
 * @author    Open Social Website Core Team <info@informatikon.com>
 * @copyright 2014 iNFORMATIKON TECHNOLOGIES
 * @license   General Public Licence http://www.opensource-socialnetwork.org/licence
 * @link      http://www.opensource-socialnetwork.org/licence
 */
define("__who_view_profile_type__", 'profile:viewed');
function who_viewed_my_profile_init()
{
	ossn_extend_view('css/ossn.default', 'profileviews/css');
	if (ossn_isLoggedin()) {
		ossn_register_callback('page', 'load:profile', 'who_viewed_my_profile');
		ossn_register_page('profileviews', 'profileviews');
		ossn_register_sections_menu('newsfeed', array(
			'name' => 'profileviews',
			'text' => ossn_print('profileviews'),
			'url' => ossn_site_url('profileviews'),
			'parent' => 'links',
			'icon' => true
		));
	}
}
function profileviews()
{
	$looks = ossn_get_relationships(array(
		'to' => ossn_loggedin_user()->guid,
		'type' => __who_view_profile_type__
	));
	$count = ossn_get_relationships(array(
		'to' => ossn_loggedin_user()->guid,
		'type' => __who_view_profile_type__,
		'count' => true
	));
	if ($looks) {
		foreach ($looks as $item) {
			$user = ossn_user_by_guid($item->relation_from);
			if ($user) {
				$users[] = $user;
			}
		}
	}

	$vars['users']     = $users;
	$vars['icon_size'] = 'small';

	$lists = "<div class='ossn-page-contents'>";
	$lists .= "<p><strong>" . ossn_print('profileviews') . "</strong></p>";
	$lists .= ossn_plugin_view("output/users_list", $vars);
	$lists .= ossn_view_pagination($count);
	$lists .= "</div>";

	$contents = array(
		'content' => $lists
	);
	$content  = ossn_set_page_layout('newsfeed', $contents);
	echo ossn_view_page($title, $content);
}
function who_viewed_my_profile()
{
	$profile = ossn_user_by_guid(ossn_get_page_owner_guid());
	$user    = ossn_loggedin_user();
	if (!$profile || !$user) {
		return false;
	}
	if (!ossn_relation_exists($profile->guid, $user->guid, __who_view_profile_type__)) {
		ossn_add_relation($profile->guid, $user->guid, __who_view_profile_type__);
	}
}
ossn_register_callback('ossn', 'init', 'who_viewed_my_profile_init');
