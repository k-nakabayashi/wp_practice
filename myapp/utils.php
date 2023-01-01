<?php

// function functionExpanded(){
//     return function ($f){
//         return $f;
//     };
// }
// $_fe = functionExpanded();

function swithcNavItems($sorted_menu_items) {
	
	//追記 : ログイン状態に応じて、ナビバーを切り分け
	foreach ($sorted_menu_items as $key => $tmp_item) {
	
		if (is_user_logged_in()) {
			//ログイン時に、除外する項目
			switch ($tmp_item->title) {
				case "患者一覧":
					global $spMember;
					if ($spMember->getMyLevel() <= 2) {
						unset($sorted_menu_items[$key]);
					}
					break;

				case "診察診断":
					global $spMember;
					if ($spMember->getMyLevel() <= 2) {
						unset($sorted_menu_items[$key]);
					}
					break;

				case "ログイン":
					unset($sorted_menu_items[$key]);
					break;
				case "パスワードのリセット":
					unset($sorted_menu_items[$key]);
					break;
				case "登録":
					unset($sorted_menu_items[$key]);
					break;
				case "ログアウト":
					$tmp_item->url = wp_logout_url( home_url() );
					break;

			}
		} else {

			//非ログイン時に、除外する項目
			switch ($tmp_item->title) {
				case "患者一覧":
					unset($sorted_menu_items[$key]);
					break;
				case "診察診断":
					unset($sorted_menu_items[$key]);
					break;
				case "プロフィール":
					unset($sorted_menu_items[$key]);
					break;
				case "ログアウト":
					unset($sorted_menu_items[$key]);
					break;


			}
		}
	}
	return $sorted_menu_items;
}

function setBoxList($user_detail, $target_name) {
                
	if ($user_detail[$target_name] != null) {
		
		$temp_birth_list = explode(",",  $user_detail[$target_name]);

		$user_detail[$target_name] = [];
		
		for ($i = 0; $i < count($temp_birth_list); ++ $i) {
			if ($temp_birth_list[$i] != "") {
				$user_detail[$target_name][$i] = true;
			}
		}
	}
	return $user_detail;
}