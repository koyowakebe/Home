<?php //子テーマ用関数

//子テーマ用のビジュアルエディタースタイルを適用
add_editor_style();

//以下に子テーマ用の関数を書く
add_filter( 'wp_image_editors', 'change_graphic_lib' );
function change_graphic_lib($array) {
return array( 'WP_Image_Editor_GD', 'WP_Image_Editor_Imagick' );
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
    echo '第1KW： <input type="text" name="cd_name" value="'.get_post_meta($post->ID, 'cd_name', true).'" size="50" /><br>';
    echo '第2KW： <input type="text" name="cd_author" value="'.get_post_meta($post->ID, 'cd_author', true).'" size="50" /><br>';
    echo '第3KW： <input type="text" name="cd_price" value="'.get_post_meta($post->ID, 'cd_price', true).'" size="50" />　<br>';
  }
  
  
  // カスタムフィールドの値を保存
  function save_kw_fields( $post_id ) {
    if(!empty($_POST['cd_name'])){ //題名が入力されている場合
      update_post_meta($post_id, 'cd_name', $_POST['cd_name'] ); //値を保存
    }else{ //題名未入力の場合
      delete_post_meta($post_id, 'cd_name'); //値を削除
    }
    
    if(!empty($_POST['cd_author'])){
      update_post_meta($post_id, 'cd_author', $_POST['cd_author'] );
    }else{
      delete_post_meta($post_id, 'cd_author');
    }
    
    if(!empty($_POST['cd_price'])){
      update_post_meta($post_id, 'cd_price', $_POST['cd_price'] );
    }else{
      delete_post_meta($post_id, 'cd_price');
    }
  }
  add_action('save_post', 'save_kw_fields');


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
                    'link' => '100',
            ), $atts, 'insta' );

    return '<div class="instagram"><br /><img class="instagram_image" src="'.$atts['link'].'/media/?size=l"><br /><a class="instagram_logo" href="'.$atts['link'].'"><span>Instagram</span></a></div>';
  }
  add_shortcode( 'insta', 'insta_func' );

  // 【入力方法】
  // [insta link=*投稿のURL（/含めず・文字列まで）*]

?>