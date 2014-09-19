<?php 
/* 管理ページHTML出力部 */
if (!class_exists('RMADL_Settings_Page_HTML')) :
class RMADL_Settings_Page_HTML {

  public function output_settings_html($source_data) {
  ?>
  
  <div id="rmadl_settings" class="wrap">
  
    <div id="icon-options-general" class="icon32"><br /></div>
    <h2>Registration Mail Address Domain Limitter</h2>
    <?php if(!empty($source_data->messages)) { ?>
    
    <div id="setting-error-settings_updated" class="updated settings-error">
    
      <?php foreach($source_data->messages as $message) echo $message; ?>
    
    </div>
    
    <?php } ?>
    
    <form id="rmadl_settings_update" action="options-general.php?page=rmadl" method="post" enctype="multipart/form-data">
      <input type="hidden" name="submit_mode" value="setting_update">
      
      <dl>
        <dt><label for="target_domains"><?php _e('Target Domain', 'rains-rmadl') ?></label></dt>
        <dd><textarea id="target_domains" name="target_domains"><?php if($source_data->setting_data !== false && $source_data->setting_data['target_domains'] != '') echo implode("\n", $source_data->setting_data['target_domains']) ?></textarea></dd>
        <dd class="text"><?php _e('The recognition as one domain per line.<br />Also, if there is an error in the format of the domain you entered, and then remove the string invalid automatically.', 'rains-rmadl') ?></dd>
        
        <dt><label for="exclude_address"><?php _e('Exclude Address', 'rains-rmadl') ?></label></dt>
        <dd><textarea id="exclude_address" name="exclude_address"><?php if($source_data->setting_data !== false && $source_data->setting_data['exclude_address'] != '') echo implode("\n", $source_data->setting_data['exclude_address']) ?></textarea></dd>
        <dd class="text"><?php _e('Enter one address per line.<br />Even if it is a domain which is the target of processing, the specific address the outside of the object of processing can be specified.', 'rains-rmadl') ?></dd>
        
        <dt><?php _e('E-mail address domain restrictions Profile update', 'rains-rmadl') ?></dt>
        <dd>
          <span<?php if($source_data->setting_data !== false && $source_data->setting_data['update_limiter'] === 'enable') echo ' class="active"'; ?>><input id="update_limiter_enable" name="update_limiter" type="radio" value="enable" <?php if($source_data->setting_data !== false && $source_data->setting_data['update_limiter'] === 'enable') echo 'checked'; ?>><label for="update_limiter_enable"><?php _e('Enable', 'rains-rmadl') ?></label></span>
          <span<?php if($source_data->setting_data !== false && $source_data->setting_data['update_limiter'] === 'disable') echo ' class="active"'; ?>><input id="update_limiter_disable" name="update_limiter" type="radio" value="disable" <?php if($source_data->setting_data !== false && $source_data->setting_data['update_limiter'] === 'disable') echo 'checked'; ?>><label for="update_limiter_disable"><?php _e('Disable', 'rains-rmadl') ?></label></span>
        </dd>
        <dd class="text"><?php _e('This setting is whether to limit even if you update e-mail address from the edit screen of the user profile.', 'rains-rmadl') ?></dd>
        
        <dt><?php _e('Operation mode', 'rains-rmadl') ?></dt>
        <dd>
          <span<?php if($source_data->setting_data !== false && $source_data->setting_data['limiter_mode'] === 'whitelist') echo ' class="active"'; ?>><input id="whitelist" name="limiter_mode" type="radio" value="whitelist" <?php if($source_data->setting_data !== false && $source_data->setting_data['limiter_mode'] === 'whitelist') echo 'checked'; ?>><label for="whitelist"><?php _e('White List', 'rains-rmadl') ?></label></span>
          <span<?php if($source_data->setting_data !== false && $source_data->setting_data['limiter_mode'] === 'blacklist') echo ' class="active"'; ?>><input id="blacklist" name="limiter_mode" type="radio" value="blacklist" <?php if($source_data->setting_data !== false && $source_data->setting_data['limiter_mode'] === 'blacklist') echo 'checked'; ?>><label for="blacklist"><?php _e('Black List', 'rains-rmadl') ?></label></span>
        </dd>
        <dd class="text"><?php _e('"White List" - Allow only domain in the list.', 'rains-rmadl') ?><br /><?php _e('"Black List" - Deny domain in the list.', 'rains-rmadl') ?></dd>
        
      </dl>
      
      <?php wp_nonce_field($source_data->user_ID, $source_data->user_login) ?>
      
      <p class="submit"><input class="button-primary" type="submit" value="<?php _e('Settings Update', 'rains-rmadl') ?>" name="update_settings_submit" /></p>
      
    </form>
    
    <?php if($source_data->setting_data) { //削除処理用項目 ?>
    
    <form id="rmadl_settings_delete" action="options-general.php?page=rmadl" method="post" enctype="multipart/form-data">
      <input type="hidden" name="submit_mode" value="uninstall">
      
      <dl>
        <dt><?php _e('Delete Settings', 'rains-rmadl') ?></dt>
        <dd class="notes"><?php _e('Configuration data of this plug-in is not deleted automatically when stop the plug-in.', 'rains-rmadl') ?><br /><?php _e('If want to stop using the plug-in, delete the configuration data using this feature.', 'rains-rmadl') ?></dd>
        <dd><span><input id="uninstall" name="uninstall" type="checkbox" value="uninstall"><label for="uninstall"><?php _e('Delete') ?></label></span></dd>
      </dl>
      
      <?php wp_nonce_field($source_data->user_ID, $source_data->user_login) ?>
      
      <p class="submit"><input class="button-primary" type="submit" value="<?php _e('Delete Settings Data', 'rains-rmadl') ?>" name="delete_settings_submit" /></p>      
      
    </form>
    
    <?php } ?>
    
  </div>
  
  <?php
  }// end function
  
}// end class RMADL_Settings_Page_HTML
endif; // end class exists
