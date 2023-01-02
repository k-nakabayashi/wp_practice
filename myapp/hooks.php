<?php

use MyApp\Http\Ctrl\ProfileCtrl;

// //追記 固定ページに「カテゴリー」を適応する
// //https://lightgauge.net/build-site/wp/add-fixedpage-category#toc2
// add_action( 'init', 'my_add_pages_categories' ); 
// function my_add_pages_categories()
// {
//     register_taxonomy_for_object_type( 'category', 'page' );
// }
// add_action( 'pre_get_posts', 'my_set_page_categories' );

// function my_set_page_categories( $query )
// {
//     if ( $query->is_category== true && $query->is_main_query()){
//         $query->set( 'post_type', array( 'post', 'page', 'nav_menu_item' ));
//     }
// }
//登録された時に、発動

// function registerUsertDetail ($user_id) {
// 	global $latest_member_id;
//     $result = (new ProfileCtrl())->create(
//         [
//             'user_id' => $user_id,
//             'member_id'=> $latest_member_id
//         ]
//     );

//     $latest_member_id = null;
    
//     return $result;
// }
// add_action('user_register', 'registerUsertDetail');

function my_app_enqueue_scripts() {
    // // WordPressのデフォルトの「jQuery」スクリプトを削除
	// wp_deregister_script('jquery');
    // // プラグイン「contact-form-7」のスタイルシートを削除
	// wp_deregister_style('contact-form-7');

	// // CDNを利用し「jquery.min.js」スクリプトを追加
	// wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
	// // テンプレートディレクトリ直下にある「js」フォルダ内の「script-common.js」を、ハンドル名jqueryのファイルの後に追加
    // wp_enqueue_script('script-common', get_template_directory_uri() . '/js/script-common.js', array('jquery'));
    
    // テンプレートディレクトリ直下にある「style.css」を追加
    // var_dump(get_template_directory_uri()  . '/style.css');
    // wp_enqueue_script( "tailwind", get_template_directory_uri()."/tailwind.js", [], false, false);
	
    # TODO:　後ほどキャッシュクリアを有効にする

    // wp_enqueue_script( "my_script", get_template_directory_uri()."/my_index.js", [], false, false);
    wp_enqueue_style( 'tailwindcss-style', 'https://cdn.tailwindcss.com');
    wp_enqueue_style( 'my-style', get_template_directory_uri().'/css/my_style.css?');
    
    // // CDNを利用し「font-awesome.min.css」を追加
	// wp_enqueue_style( 'font-awesome.min', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

	// // 条件分岐を使用し、シングルページの場合のみ「prism.js」を、ハンドル名jqueryのファイルの後に追加
	// if(is_single()) {
	// 	wp_enqueue_script('prism', get_template_directory_uri() . '/js/min/prism.js', array('jquery'));
	// }
}
add_action( 'wp_enqueue_scripts', 'my_app_enqueue_scripts' );