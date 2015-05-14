<?php 
/*
Plugin Name: RA - Registration Mail Address Domain Limiter
Description: This plug-in restricts the address which uses initial registration for the time of a user accounting, and the registration mail address of a profile page per domain.
Author: Rasin (skuraomoto)
Author URI: http://www.rains.jp/
Version: 1.2.8
License: GPLv2
Text Domain: rains-rmadl
Domain Path: /languages/
*/
/*
  Copyright 2013 Rains
  
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
load_plugin_textdomain('rains-rmadl', false, dirname(plugin_basename(__FILE__)) . "/languages/");
//プラグイン情報の翻訳用
__('This plug-in restricts the address which uses initial registration for the time of a user accounting, and the registration mail address of a profile page per domain.', 'rains-rmadl');

require_once(plugin_dir_path( __FILE__ ) . '/class/rmadl_common.php');
require_once(plugin_dir_path( __FILE__ ) . '/class/rmadl_settings_page.php');
require_once(plugin_dir_path( __FILE__ ) . '/class/rmadl_settings_page_html.php');
require_once(plugin_dir_path( __FILE__ ) . '/class/rmadl_execute_limiter.php');


if (!class_exists('RMADL')) :
class RMADL {

  function RMADL() {
    $this->__construct();
  }
  
  function __construct() {
  
  
    
    if(is_admin()) {
    
      //初期化
      register_activation_hook(__FILE__, array(&$this, 'rmadl_init'));
      
      //管理画面
      add_action('init', array(&$this, 'rmadl_load_admin_page'), 10 , 0);
      
      //プラグイン画面・管理ページリンク
      add_filter('plugin_action_links', array('rmadl_common', 'plugin_settings_page_links'), 10, 2);
    
    }
    
    //新規登録時制限
    add_filter('registration_errors', array(&$this, 'rmadl_new_user_regist_limiter'), 10, 3);
    
    //プロフィール上書き時制限処理
    add_action('profile_update', array(&$this, 'rmadl_profile_mailupdate_limiter'), 10, 2);
  
  }

  //初期化
  function rmadl_init() {
    RMADL_COMMON::rmadl_init();
  }
  
  //管理画面
  function rmadl_load_admin_page() {
    $rmadl_settings_page_obj = new RMADL_Settings_Page();
  }
  
  //新規登録時制限
  function rmadl_new_user_regist_limiter ($errors, $sanitized_user_login, $user_email) {
    return RMADL_Limitter_Execute::new_user_regist_limiter($errors, $sanitized_user_login, $user_email);
    
  }
  
  //プロフィール上書き時制限処理
  function rmadl_profile_mailupdate_limiter ($user_id, $old_user_data) {
    RMADL_Limitter_Execute::profile_mailupdate_limiter($user_id, $old_user_data);
  }
  
  public static function plugin_basename() {
    return plugin_basename(__FILE__);
  }
  
}// end class RMADL
endif; // end class exists

$rmadl_obj = new RMADL();
