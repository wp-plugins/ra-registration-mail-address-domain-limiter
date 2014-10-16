<?php 
/* 制限処理 */
if (!class_exists('RMADL_Limitter_Execute')) :
class RMADL_Limitter_Execute {

  /* 新規登録メールアドレスドメイン制限 */
  public static function new_user_regist_limiter ($errors, $sanitized_user_login, $user_email) {
  
      $add_error = self::mail_addr_domain_check($user_email);
    
    //エラー処理追加
    if ($add_error === true)
        $errors->add('disallow_domain_or_address', __('<strong>ERROR</strong>: The domain of the inputted mail address or mail address is not permitted.','rains-rmadl'));
    
    //エラーオブジェクトを戻し
    return $errors;
    
  }
  
  /* プロフィール更新時制限 */
  public static function profile_mailupdate_limiter($user_id, $old_user_data) {
  
      $user_email = get_user_option('user_email', $user_id);
      
      $add_error = self::mail_addr_domain_check($user_email, 'profile_update');
      
    //エラー処理
    if ($add_error === true) {
      global $wpdb;
      //変更前のアドレスで書き戻し
      $text = $wpdb->update($wpdb->users, array('user_email' => $old_user_data->user_email), array('ID' => $user_id));
    }
  
  }
  
  
  //判定処理
  public static function mail_addr_domain_check($user_email, $case = '') {
  
    $settings = RMADL_COMMON::get_settings_data();
  
    // whitelist & blacklist 分岐
    if($settings !== false) {
    
      if(preg_match('/@(.*)$/', $user_email, $matches)) {
        $domain = $matches[1];
      } else {
        $domain = '';
      }
      
      $add_error = false;
      switch($settings['limiter_mode']) {
      
        case 'whitelist' : 
          if (!in_array($domain, $settings['target_domains']) && !in_array($user_email, $settings['exclude_address'])) $add_error = true;
          break;
        
        case 'blacklist' : 
          if (in_array($domain, $settings['target_domains']) && !in_array($user_email, $settings['exclude_address'])) $add_error = true;
          break;
        
        default : 
          break;
        
      }
      
      //処理対象外パターン除外
      if($case === 'profile_update' && $settings['update_limiter'] !== 'enable')
        $add_error = false;
    }
    
    
    return $add_error;
  }

}// end class RMADL_Limitter
endif; // end class exists
