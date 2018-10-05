<?php //子テーマ用関数

//子テーマ用のビジュアルエディタースタイルを適用
add_editor_style();

//以下に子テーマ用の関数を書く
add_filter( 'wp_image_editors', 'change_graphic_lib' );
function change_graphic_lib($array) {
return array( 'WP_Image_Editor_GD', 'WP_Image_Editor_Imagick' );
}

add_action( 'edit_form_top', 'before_form_content' );
function before_form_content() {
  echo '<input name="save" type="submit" class="check-button" id="publish" value="検収する">';
}

function my_enter_title_here($title){
	return 'タイトルを入力してほしいねん';
}
add_filter('enter_title_here', 'my_enter_title_here', 10, 1);

//必須入力項目
add_action( 'publish_post', 'post_edit_required' );
add_action( 'publish_post', 'post_edit_required' );
function post_edit_required() {
?>
<script type="text/javascript">
jQuery(function($) {
  if( 'post' == $('#post_type').val() ) {
    $('#post').submit(function(e) {
      // タイトル
      if ( '' == $('#title').val() ) {
        alert('タイトルを入力してください');
        $('.spinner').css('visibility', 'hidden');
        $('#publish').removeClass('button-primary-disabled');
        $('#title').focus();
        return false;
      }
	    // コンテンツ（エディタ）
      if ( $('.wp-editor-area').val().length < 1 ) {
        alert('コンテンツを入力してください');
        $('.spinner').css('visibility', 'hidden');
        $('#publish').removeClass('button-primary-disabled');
        return false;
      }
	    //h2
      if ( $("h2").length / the_content().length < 1/900 ) {
      alert('見出し2は900文字に一回使用してください')
      $('.spinner').css('visibility', 'hidden');
      $('#publish').removeClass('button-primary-disabled');
        return false;
	    }
      //h3
      if ( $("h3").length / the_content().length < 1/900 ) {
      alert('見出し3は450文字に一回使用してください')
      $('.spinner').css('visibility', 'hidden');
      $('#publish').removeClass('button-primary-disabled');
        return false;
	    }
      // アイキャッチ　＊足りない      
      if ( $('.wp-post-image').size < 1 ) {
      alert('アイキャッチ画像を設定してください');
      $('.spinner').css('visibility', 'hidden');
      $('#publish').removeClass('button-primary-disabled');
        return false;
      }
      //メタディスクリプション
      var metaDiscre = document.head.children;
      var metaLength = metaDiscre.length;
      for(var i = 0;i < metaLength;i++){
        var proper = metaDiscre[i].getAttribute('name');
        if(proper === 'description'){
          var dis = metaDiscre[i];
        }
      }
      if (dis == '') {
      alert('メタディスクリプションを設定してください');
      $('.spinner').css('visibility', 'hidden');
      $('#publish').removeClass('button-primary-disabled');
        return false;
      }
      // カテゴリー
      if ( $('#taxonomy-category input:checked').length < 1 ) {
      alert('カテゴリーを選択してください');
      $('.spinner').css('visibility', 'hidden');
      $('#publish').removeClass('button-primary-disabled');
      $('#taxonomy-category a[href="#category-all"]').focus();
        return false;
      }
      // タグ
      if ( $('#tagsdiv-post_tag .tagchecklist span').length < 1 ) {
      alert('タグを選択してください');
      $('.spinner').css('visibility', 'hidden');
      $('#publish').removeClass('button-primary-disabled');
      $('#new-tag-post_tag').focus();
        return false;
      }
      // アイキャッチ
      if ( $('#set-post-thumbnail img').length < 1 ) {
      alert('アイキャッチ画像を設定してください');
      $('.spinner').css('visibility', 'hidden');
      $('#publish').removeClass('button-primary-disabled');
      $('#set-post-thumbnail').focus();
        return false;
      }
    });
  }
});
</script>
<?php
}

  // 固定カスタムフィールドボックス
  function add_kw_fields() {
    //add_meta_box(表示される入力ボックスのHTMLのID, ラベル, 表示する内容を作成する関数名, 投稿タイプ, 表示方法)
    //第4引数のpostをpageに変更すれば固定ページにオリジナルカスタムフィールドが表示されます(custom_post_typeのslugを指定することも可能)。
    //第5引数はnormalの他にsideとadvancedがあります。
    add_meta_box( 'kw_checker', 'キーワード設定', 'insert_kw_fields', 'post', 'normal');
  }
  add_action('admin_menu', 'add_kw_fields');
  
  
  // カスタムフィールドの入力エリア
  
  /*検収基準はこちら
  A
  ①第1KWと第2KWを本文中の見出し2の1/2以上に入れ込まなければならない
  ②見出し2は900文字に一回使用しなければならない（5000文字なら、5回）
  ③第1KWと第1KWを見出し3の1/4以上で使用しなければならない。
  ④見出し3は450文字に一回は使用しなければならない。
  ⑤第3KWと第7KWを見出し3の1/8以上で使用しなければならない。
  ⑥第1KWと第2KWは300文字（6000文字なら20回）に1回使用しなければならない。（見出しは別）
  ⑦第3KW〜第7KWは600文字（6000文字なら10回）に1回使用しなければならない。（見出しは別）
  ⑧第8KW〜第11KWを見出し3で一回使用しなければならない
  ⑨第8KW〜第11KWを本文中で3回使用しなければならない
  ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
  B
  ①アイキャッチ画像をつけなければならない
  ②導入文の前半に第1KWと第2KWを使用しなければならない
  ③導入文には第1KW〜第7KWを一回以上使用しなければならない
  ④見出し2の下は必ず見出し3を使用しなければならない
  ⑤見出し3の下は画像（通常画像・インスタグラム・アマゾン）のどれかを挿入しなければならない*/

  function insert_kw_fields() {
    global $post;
  
    //下記に管理画面に表示される入力エリアを作ります。「get_post_meta()」は現在入力されている値を表示するための記述です。
    echo '第1KW ： <input type="text" name="kw_1" value="'.get_post_meta($post->ID, 'kw_1', true).'" size="50" /><br>';
    echo '第2KW ： <input type="text" name="kw_2" value="'.get_post_meta($post->ID, 'kw_2', true).'" size="50" /><br>';
    echo '第3KW ： <input type="text" name="kw_3" value="'.get_post_meta($post->ID, 'kw_3', true).'" size="50" /><br>';
    echo '第4KW ： <input type="text" name="kw_4" value="'.get_post_meta($post->ID, 'kw_4', true).'" size="50" /><br>';
    echo '第5KW ： <input type="text" name="kw_5" value="'.get_post_meta($post->ID, 'kw_5', true).'" size="50" /><br>';
    echo '第6KW ： <input type="text" name="kw_6" value="'.get_post_meta($post->ID, 'kw_6', true).'" size="50" /><br>';
    echo '第7KW ： <input type="text" name="kw_7" value="'.get_post_meta($post->ID, 'kw_7', true).'" size="50" /><br>';
    echo '第8KW ： <input type="text" name="kw_8" value="'.get_post_meta($post->ID, 'kw_8', true).'" size="50" /><br>';
    echo '第9KW ： <input type="text" name="kw_9" value="'.get_post_meta($post->ID, 'kw_9', true).'" size="50" /><br>';
    echo '第10KW： <input type="text" name="kw_10" value="'.get_post_meta($post->ID, 'kw_10', true).'" size="50" /><br>';
    echo '第11KW： <input type="text" name="kw_11" value="'.get_post_meta($post->ID, 'kw_11', true).'" size="50" /><br>';
    echo '第12KW： <input type="text" name="kw_12" value="'.get_post_meta($post->ID, 'kw_12', true).'" size="50" /><br>';
    echo '第13KW： <input type="text" name="kw_13" value="'.get_post_meta($post->ID, 'kw_13', true).'" size="50" /><br>';
    echo '第14KW： <input type="text" name="kw_14" value="'.get_post_meta($post->ID, 'kw_14', true).'" size="50" /><br>';
    echo '第15KW： <input type="text" name="kw_15" value="'.get_post_meta($post->ID, 'kw_15', true).'" size="50" /><br>';
    echo '第16KW： <input type="text" name="kw_16" value="'.get_post_meta($post->ID, 'kw_16', true).'" size="50" /><br>';
    echo '第17KW： <input type="text" name="kw_17" value="'.get_post_meta($post->ID, 'kw_17', true).'" size="50" /><br>';
  }
  
    //カスタムフィールドを追加するボタンJSで実装。
  
  // カスタムフィールドの値を保存
  function save_kw_fields( $post_id ) {
    if(!empty($_POST['kw_1'])){ //題名が入力されている場合
      update_post_meta($post_id, 'kw_1', $_POST['kw_1'] ); //値を保存
    }else{ //題名未入力の場合
      delete_post_meta($post_id, 'kw_1'); //値を削除
    }
    if(!empty($_POST['kw_2'])){
      update_post_meta($post_id, 'kw_2', $_POST['kw_2'] );
    }else{
      delete_post_meta($post_id, 'kw_2');
    }
    if(!empty($_POST['kw_3'])){
      update_post_meta($post_id, 'kw_3', $_POST['kw_3'] );
    }else{
      delete_post_meta($post_id, 'kw_3');
    }
    if(!empty($_POST['kw_4'])){
      update_post_meta($post_id, 'kw_4', $_POST['kw_4'] );
    }else{
      delete_post_meta($post_id, 'kw_4');
    }
    if(!empty($_POST['kw_5'])){
      update_post_meta($post_id, 'kw_5', $_POST['kw_5'] );
    }else{
      delete_post_meta($post_id, 'kw_5');
    }
    if(!empty($_POST['kw_6'])){
      update_post_meta($post_id, 'kw_6', $_POST['kw_6'] );
    }else{
      delete_post_meta($post_id, 'kw_6');
    }
    if(!empty($_POST['kw_7'])){
      update_post_meta($post_id, 'kw_7', $_POST['kw_7'] );
    }else{
      delete_post_meta($post_id, 'kw_7');
    }
    if(!empty($_POST['kw_8'])){
      update_post_meta($post_id, 'kw_8', $_POST['kw_8'] );
    }else{
      delete_post_meta($post_id, 'kw_8');
    }
    if(!empty($_POST['kw_9'])){
      update_post_meta($post_id, 'kw_9', $_POST['kw_9'] );
    }else{
      delete_post_meta($post_id, 'kw_9');
    }
    if(!empty($_POST['kw_10'])){
      update_post_meta($post_id, 'kw_10', $_POST['kw_10'] );
    }else{
      delete_post_meta($post_id, 'kw_10');
    }
    if(!empty($_POST['kw_11'])){
      update_post_meta($post_id, 'kw_11', $_POST['kw_11'] );
    }else{
      delete_post_meta($post_id, 'kw_11');
    }
  }
  add_action('save_post', 'save_kw_fields');


  // カスタムフィールドの値でValidationする
  function validate_kw( $post_id ){
    // ①第1KWと第2KWを本文中の見出し2の1/2以上に入れ込まなければならない
    ?>
    <script type="text/javascript">
    jQuery(function($) {
    var key_1 = "<?php echo $_POST['kw_1'] ?>";
    var key_2 = "<?php echo $_POST['kw_2'] ?>";
    if (key_1 !== null && key_2 !== null){
      if ( $("h2").length / (key_1.length + key_2.length) < 2 || $("h2").match(key_1) == null || $("h2").match(key_2) == null ) {
        alert('第1KWと第2KWを本文中の見出し2の1/2以上に入れ込んでください')
        $('.spinner').css('visibility', 'hidden');
        $('#publish').removeClass('button-primary-disabled');
          return false;
      }
    }
    })
    // ③第1KWと第2KWを見出し3の1/4以上で使用しなければならない。
    jQuery(function($) {
    var key_1 = "<?php echo $_POST['kw_1'] ?>";
    var key_2 = "<?php echo $_POST['kw_2'] ?>";
    if (key_1 !== null && key_2 !== null){
      if ( (key_1.length + key_2.length) / $("h2").length !=> 0.25 || $("h3").match(key_1) == null || $("h3").match(key_2) == null ) {
        alert('第1KWと第2KWを本文中の見出し3の1/4以上に入れ込んでください')
        $('.spinner').css('visibility', 'hidden');
        $('#publish').removeClass('button-primary-disabled');
          return false;
      }
    }
    })
    // ⑤第3KWと第7KWを見出し3の1/8以上で使用しなければならない。
    jQuery(function($) {
    var key_3 = "<?php echo $_POST['kw_3'] ?>";
    var key_7 = "<?php echo $_POST['kw_7'] ?>";
    if (key_3 !== null && key_7 !== null){
      if ( (key_3.length + key_7.length) / $("h3").length !=> 0.125 || $("h3").match(key_3) == null || $("h3").match(key_2) == null ) {
        alert('第1KWと第2KWを本文中の見出し3の1/4以上に入れ込んでください')
        $('.spinner').css('visibility', 'hidden');
        $('#publish').removeClass('button-primary-disabled');
          return false;
      }
    }
    })
    // ⑥第1KWと第2KWは300文字（6000文字なら20回）に1回使用しなければならない。（見出しは別）
    jQuery(function($) {
    var key_1 = "<?php echo $_POST['kw_1'] ?>";
    var key_2 = "<?php echo $_POST['kw_2'] ?>";
    if (key_1 !== null && key_2 !== null){
      var count = 0;
      ex_key_1 = new RegExp(key_1);
      ex_key_1 = new RegExp(key_2);
      var arr = thecontent().match(ex_key_1) + thecontent().match(ex_key_2);
      if (arr = null){
        count = 0;
      }
      else{
        count = arr.length;
      }
      if ( count / thecontent().length !=> (1/300) ) {
        alert('第1KWと第2KWは300文字に1回使用しなければならない')
        $('.spinner').css('visibility', 'hidden');
        $('#publish').removeClass('button-primary-disabled');
          return false;
      }
    }
    })
    // ⑦第3KW〜第7KWは600文字（6000文字なら10回）に1回使用しなければならない。（見出しは別）
    jQuery(function($) {
    var key_3 = "<?php echo $_POST['kw_3'] ?>";
    var key_4 = "<?php echo $_POST['kw_4'] ?>";
    var key_5 = "<?php echo $_POST['kw_5'] ?>";
    var key_6 = "<?php echo $_POST['kw_6'] ?>";
    var key_7 = "<?php echo $_POST['kw_7'] ?>";
    if (key_3 !== null && key_4 !== null && key_5 !== null && key_6 !== null && key_7 !== null){
      var count = 0;
      ex_key_3 = new RegExp(key_3);
      ex_key_4 = new RegExp(key_4);
      ex_key_5 = new RegExp(key_5);
      ex_key_6 = new RegExp(key_6);
      ex_key_7 = new RegExp(key_7);
      var arr = thecontent().match(ex_key_3) + thecontent().match(ex_key_4) + thecontent().match(ex_key_5) + thecontent().match(ex_key_6) + thecontent().match(ex_key_7);
      if (arr = null){
        count = 0;
      }
      else{
        count = arr.length;
      }
      if ( count / thecontent().length !=> (1/600) ) {
        alert('第3KW〜第7KWは600文字に1回使用しなければならない')
        $('.spinner').css('visibility', 'hidden');
        $('#publish').removeClass('button-primary-disabled');
          return false;
      }
    }
    })
    // 第8KW〜第11KWを見出し3で一回使用しなければならない
    jQuery(function($) {
    var key_8 = "<?php echo $_POST['kw_8'] ?>";
    var key_9 = "<?php echo $_POST['kw_9'] ?>";
    var key_10 = "<?php echo $_POST['kw_10'] ?>";
    var key_11 = "<?php echo $_POST['kw_11'] ?>";
    if (key_8 !== null && key_9 !== null && key_10 !== null && key_11 !== null){
      if ( $("h3").match(key_8) == null || $("h3").match(key_9) == null || $("h3").match(key_10) == null || $("h3").match(key_11) == null) {
        alert('第8KW〜第11KWを見出し3で一回使用しなければならない')
        $('.spinner').css('visibility', 'hidden');
        $('#publish').removeClass('button-primary-disabled');
          return false;
      }
    }
    })
    // ⑨第8KW〜第11KWを本文中で3回使用しなければならない
    jQuery(function($) {
    var key_8 = "<?php echo $_POST['kw_8'] ?>";
    var key_9 = "<?php echo $_POST['kw_9'] ?>";
    var key_10 = "<?php echo $_POST['kw_10'] ?>";
    var key_11 = "<?php echo $_POST['kw_11'] ?>";
    if (key_8 !== null && key_9 !== null && key_10 !== null && key_11 !== null){
      var count = 0;
      ex_key_8 = new RegExp(key_8);
      ex_key_9 = new RegExp(key_9);
      ex_key_10 = new RegExp(key_10);
      ex_key_11 = new RegExp(key_11);
      var arr = thecontent().match(ex_key_8) + thecontent().match(ex_key_9) + thecontent().match(ex_key_10) + thecontent().match(ex_key_11)
      if (arr = null){
        count = 0;
      }
      else{
        count = arr.length;
      }
      if ( count < 3 ) {
        alert('第8KW〜第11KWを本文中で3回使用しなければならない')
        $('.spinner').css('visibility', 'hidden');
        $('#publish').removeClass('button-primary-disabled');
          return false;
      }
    }
    })
    // ②導入文の前半に第1KWと第2KWを使用しなければならない
    jQuery(function($) {
    var key_1 = "<?php echo $_POST['kw_1'] ?>";
    var key_2 = "<?php echo $_POST['kw_2'] ?>";
    if (key_1 !== null && key_2 !== null){
      if ( $("div.theContentWrap-ccc + p").match(key_1) == null || $("div.theContentWrap-ccc + p").match(key_2) == null) {
        alert('導入文の前半に第1KWと第2KWを使用しなければならない')
        $('.spinner').css('visibility', 'hidden');
        $('#publish').removeClass('button-primary-disabled');
          return false;
      }
    }
    })
    // ③導入文には第1KW〜第7KWを一回以上使用しなければならない
    jQuery(function($) {
    var key_1 = "<?php echo $_POST['kw_1'] ?>";
    var key_2 = "<?php echo $_POST['kw_2'] ?>";
    var key_3 = "<?php echo $_POST['kw_3'] ?>";
    var key_4 = "<?php echo $_POST['kw_4'] ?>";
    var key_5 = "<?php echo $_POST['kw_5'] ?>";
    var key_6 = "<?php echo $_POST['kw_6'] ?>";
    var key_7 = "<?php echo $_POST['kw_7'] ?>";
    if (key_1 !== empty && key_2 !== empty && key_3 !== empty && key_4 !== empty && key_5 !== empty && key_6 !== empty && key_7 !== empty){
      if ( $("div.theContentWrap-ccc + p").match(key_1) == null || $("div.theContentWrap-ccc + p").match(key_2) == null $("div.theContentWrap-ccc + p").match(key_3) == null || $("div.theContentWrap-ccc + p").match(key_4) == null || $("div.theContentWrap-ccc + p").match(key_5) == null || $("div.theContentWrap-ccc + p").match(key_6) == null || $("div.theContentWrap-ccc + p").match(key_7) == null || ) {
        alert('③導入文には第1KW〜第7KWを一回以上使用しなければならない')
        $('.spinner').css('visibility', 'hidden');
        $('#publish').removeClass('button-primary-disabled');
          return false;
      }
    }
    })
    /*
    ④見出し2の下は必ず見出し3を使用しなければならない
    ⑤見出し3の下は画像（通常画像・インスタグラム・アマゾン）のどれかを挿入しなければならない
    */
    </script>
    <?php
  }
  add_action( 'publish_post', 'validate_kw' );

  // Instagramの入力を簡易化したい
  /*
  <div class="instagram">
  <img class="instagram_image" src="https://www.instagram.com/p/＊「投稿へ移動」画面のURLの/p/のあとの文字列＊（BhOSOqel33Q）/media/?size=l">
  <a class="instagram_logo" href="https://www.instagram.com/p/＊「投稿へ移動」画面のURLの/p/のあとの文字列＊（BhOSOqel33Q）"><span>[icon name="instagram" class="" unprefixed_class=""]&nbsp;&nbsp;Instagram</span></a>
  </div>
  */

  # shortcode_attsを利用する
  function insta_func( $atts ) {
    $atts = shortcode_atts(
            array(
              'url' => '',
            ), $atts, 'instagram' );

    return '<div class="instagram"><br /><img class="instagram_image" src="'.$atts['url'].'/media/?size=l"><br /><a class="instagram_logo" href="'.$atts['url'].'"><span>Instagram</span></a></div>';
  }
  add_shortcode( 'instagram', 'insta_func' );

  // 【入力方法】
  // [instagram url=*投稿のURL（/まで）*]


?>