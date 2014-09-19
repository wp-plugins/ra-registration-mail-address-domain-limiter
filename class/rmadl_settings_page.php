<?php 
/* 管理ページ処理部 */
if (!class_exists('RMADL_Settings_Page')) :
class RMADL_Settings_Page {
  
  public $setting_data;
  public $setting_data_db;
  public $user_ID;
  public $user_login;
  public $messages = array();
  
  
  // 管理者用メニューに追加
  public function __construct() {
    add_action('admin_menu', array(&$this, 'rmadl_add_menu'));
    add_action('admin_print_styles', array(&$this, 'rmadl_add_admin_css'));
  }
  
  public function rmadl_add_menu() { 
    add_submenu_page('options-general.php', 'Registration Mail Address Domain Limitter - Settings', 'RMADL', 'manage_options', 'rmadl', array(&$this, 'rmadl_display_settins_page'));
  }
  
  public function rmadl_add_admin_css() {
    wp_register_style('rmadl_admin_css', plugins_url('ra-mail-domain-limiter.css', RMADL::plugin_basename()));
    wp_enqueue_style('rmadl_admin_css');
  }
  
  
  /* 設定画面 */
  public function rmadl_display_settins_page() {
  
    //管理者以外は表示拒否
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.', 'rains-rmadl'));
    }
  
    global $wpdb, $user_ID;
    
    $this->user_ID = $user_ID;
    
    //nonce用にユーザー名取得
    $this->user_login = get_user_option('user_login', $this->user_ID);
    
    
    //DB記憶データ削除処理分岐
    if (isset($_POST['submit_mode']) && $_POST['submit_mode'] == "uninstall" && !empty($_POST['uninstall'])) {
      if(delete_option('rmadl_settings')) {
        $this->add_message(__('removed the configuration data.', 'rains-rmadl'));
        $this->setting_data = array();
      } else {
        $this->add_message(__('failed to delete the configuration data.', 'rains-rmadl'));
      }
    }
    
    
    //保存済みデータを取得（無ければfalse）
    $this->setting_data = RMADL_COMMON::get_settings_data();
    
      //設定更新処理分岐
    if (isset($_POST['submit_mode']) && $_POST['submit_mode'] == "setting_update") {
    
      //$_POSTから設定内容をコピー
      //ドメイン設定
      if(isset($_POST['target_domains']) && $_POST['target_domains'] != '') {
        $this->setting_data['target_domains'] = $this->domains_textarea_fix($_POST['target_domains']);
      } else {
        $this->setting_data['target_domains'] = array();
      }
      
      //例外アドレス設定
      if(isset($_POST['exclude_address']) && $_POST['exclude_address'] != '') {
        $this->setting_data['exclude_address'] = $this->exclude_textarea_fix($_POST['exclude_address']);
      } else {
        $this->setting_data['exclude_address'] = array();
      }
      
      //プロフィール上書き制限
      if(isset($_POST['update_limiter']) && ($_POST['update_limiter'] == 'enable' || $_POST['update_limiter'] == 'disable')) {
        $this->setting_data["update_limiter"] = $_POST['update_limiter'];
      }
      
      //制限モード
      if(isset($_POST['limiter_mode']) && ($_POST['limiter_mode'] == 'whitelist' || $_POST['limiter_mode'] == 'blacklist')) {
        $this->setting_data["limiter_mode"] = $_POST['limiter_mode'];
      }
      
      
      //DBに設定が無い状態で設定更新を押された時、ラジオボタンにチェックが入っていない場合デフォルト値を代入
      if(!isset($this->setting_data["update_limiter"]) || !$this->setting_data["update_limiter"]) { 
        if(!isset($_POST['update_limiter']) || ($_POST['update_limiter'] != 'enable' && $_POST['update_limiter'] != 'disable')) {
          $this->setting_data["update_limiter"] =  RMADL_COMMON::$setting_data_default['update_limiter'];
        }
      }
      if(!isset($this->setting_data["limiter_mode"]) || !$this->setting_data["limiter_mode"]) {
        if(!isset($_POST['limiter_mode']) || ($_POST['limiter_mode'] != 'whitelist' && $_POST['limiter_mode'] != 'blacklist')) {
          $this->setting_data["limiter_mode"] =  RMADL_COMMON::$setting_data_default['limiter_mode'];
        }
      }
      
      
      //nonceとrefererチェック
      check_admin_referer($this->user_ID, $this->user_login);
      
      //DBに設定保存
      if(update_option('rmadl_settings', serialize($this->setting_data))) {
        $this->add_message(__('Settings have been saved.', 'rains-rmadl'));
      } else {
        $this->add_message(__('Failed to save the setting data. Current Settings and the input settings that are saved are the same.', 'rains-rmadl'));
      }
      
    } // end if isset($_POST['submit_mode']) && $_POST['submit_mode'] == "setting_update"
    
    //フォームHTML出力
    RMADL_Settings_Page_HTML::output_settings_html($this);
  
  } // end function rmadl_display_settins_page()
  
  
  /* textareaからのドメイン登録データで、改行の連続やデータ先頭・末尾の改行を除去など */
  protected function domains_textarea_fix($text) {
  
    mb_regex_encoding("utf-8");
    
    //英数小文字に統一
    $text = mb_convert_kana($text, "as", "utf-8");
    $text = mb_convert_case($text, MB_CASE_LOWER, "utf-8");
    
    //改行コードの揺れを統一
    $text = preg_replace("/^(\r\n){1,}|\r{1,}|\n{1,}$/", '', $text);
    $text = preg_replace("/(\r\n){2,}|\r{2,}|\n{2,}/", "\n", $text);
    
    //複数の連続改行、改行のみの行を削除
    $text = preg_replace("/\r\n |\r |\n  */", "\n", $text);
    $text = preg_replace("/\r\n |\r |\n /", '', $text);
    
    //ドメインに使用できる有効な文字以外を削除
    $text = mb_ereg_replace("[^0-9a-z\-\n\.]", '', $text);
    
    //ハイフンの連続を１つに
    $text = preg_replace("/\-{1,}/", '-', $text);
    
    //ドットと隣接したハイフンを削除
    $text = preg_replace("/\.\-/", '.', $text);
    $text = preg_replace("/\-\./", '.', $text);
    
    //ドットの連続を１つに
    $text = preg_replace("/\.{1,}/", '.', $text);
    
    //行頭行末のドット、ハイフンのみを削除
    $text = preg_replace("/^[\.\-]{1,}/m", '', $text);
    $text = preg_replace("/[\.\-]{1,}$/m", '', $text);
    
    //行で分割
    $target_domain_array = explode("\n", $text);
    //各要素をtrim()にかける
    $target_domain_array = array_map('trim', $target_domain_array);
    //空行を削除
    $target_domain_array = array_filter($target_domain_array, 'strlen');
    //再採番
    $target_domain_array = array_values($target_domain_array);
    
    
    return $target_domain_array;
    
  }
  
  /* textareaからのアドレス登録データ処理 */
  protected function exclude_textarea_fix($text) {
  
    mb_regex_encoding("utf-8");
    
    //英数小文字に統一
    $text = mb_convert_kana($text, "as", "utf-8");
    $text = mb_convert_case($text, MB_CASE_LOWER, "utf-8");
    
    //改行コードの揺れを統一
    $text = preg_replace("/^(\r\n){1,}|\r{1,}|\n{1,}$/", '', $text);
    $text = preg_replace("/(\r\n){2,}|\r{2,}|\n{2,}/", "\n", $text);
    
    //複数の連続改行、改行のみの行を削除
    $text = preg_replace("/\r\n |\r |\n  */", "\n", $text);
    $text = preg_replace("/\r\n |\r |\n /", '', $text);
    
    //メールアドレスに使用できる有効な文字以外を削除
    $text = mb_ereg_replace("[^0-9a-z\-\n\.@]", '', $text);
    
    //行で分割
    $exclude_address_array = explode("\n", $text);
    //各要素をtrim()にかける
    $exclude_address_array = array_map('trim', $exclude_address_array);
    //メールアドレス形式以外を除去
    foreach($exclude_address_array as $key => $address) {
      if(!is_email($address)) {
        unset($exclude_address_array[$key]);
      }
    }
    //空行を削除
    $exclude_address_array = array_filter($exclude_address_array, 'strlen');
    //再採番
    $exclude_address_array = array_values($exclude_address_array);
    
    
    return $exclude_address_array;
    
  }
  
  /* メッセージ追加 */
  protected function add_message($massage, $tag_before = '<p><strong>', $tag_after = '</strong></p>') {
    $this->messages[] = $tag_before . htmlentities($massage, ENT_QUOTES, 'utf-8') . $tag_after . "\n";
  }
  
  
}// end class RMADL_Settings_Page
endif; // end class exists
