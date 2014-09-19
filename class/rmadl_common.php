<?php 
/* メイン */
if (!class_exists('RMADL_COMMON')) :
class RMADL_COMMON {

  public static $setting_data_default = array(
    'target_domains' => '',
    'exclude_address' => '',
    'update_limiter' => 'disable',
    'limiter_mode' => 'blacklist'
 );
  
  //初期設定データをAutoload noで保存
  public static function rmadl_init() {
    if($settings = self::get_settings_data())
      update_option('rmadl_settings', serialize($settings));
  }
  
  public static function get_settings_data() {
    $settings = get_option('rmadl_settings');
    if($settings) {
      $settings = unserialize($settings);
      foreach(self::$setting_data_default as $key => $setting) {
        if(!isset($settings[$key])) {
          $settings[$key] = $setting;
        }
      }
    } else {
      add_option('rmadl_settings', serialize(self::$setting_data_default), '', 'no');
      $settings = false;
    }
    
    return $settings;
  }
  
  public static function get_limiter_domains() {
    $settings = self::get_settings_data();
    
    if($settings) {
      $settings = unserialize($settings);
    }
    
  }
  
  public static function plugin_settings_page_links($links, $file) {
  
    $this_plugin = RMADL::plugin_basename();
    if ($file === $this_plugin) {
    
      $link_url = get_admin_url(null, 'options-general.php?page=rmadl');;
      $link_text = '<a href="' . $link_url . '" title="' . __('Settings') . '">' . __('Settings') . '</a>';
      
      array_unshift($links, $link_text);
      
    }
    
    return $links;
  }
  
}// end class RMADL
endif; // end class exists
