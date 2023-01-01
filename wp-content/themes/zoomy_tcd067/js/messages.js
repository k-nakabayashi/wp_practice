jQuery(function($){

	if (typeof TCD_MEMBERSHIP_MESSAGES !== 'object') {
		return;
	}

	var $window = $(window);
	var $document = $(document);
	var $body = $(document.body);
	var winWidth = window.innerWidth || $body.width();

	$window.on('resize', function(){
		winWidth = window.innerWidth || $body.width();
	});

	/**
	 * モーダルスクロールバー有無チェック
	 */
	var checkModalScrollbar = function() {
		if ($('body').hasClass('is-wp-mobile-device')) return false;
		var $modal = $('.p-modal.is-active');
		var $modalContents = $modal.find('.p-modal__contents:visible');
		var modalInner = $modal.find('.p-modal__contents__inner:visible').get(0);
		if (!modalInner || !$modalContents.length) return false;
		if (modalInner.scrollHeight > $modalContents.height()) {
			$modal.addClass('has-scrollbar');
		} else {
			$modal.removeClass('has-scrollbar');
		}
	};

	/**
	 * 送信モーダル
	 */
	var $modalSend, $modalSendForm;
	var createModalSend = function() {
		$modalSend = $('#js-modal-create-message');
		if ($modalSend.length) return;

		var html = '<div id="js-modal-create-message" class="p-modal p-modal--messages p-modal--create-message">';
		html += '<div class="p-modal__contents">';
		html += '<div class="p-modal__contents__inner">';
		html += '<div class="p-messages__create-message__headline">' + TCD_MEMBERSHIP_MESSAGES.create_message_headline + '</div>';
		html += '<form class="p-messages__create-message__form">';
		html += '<textarea class="p-messages__create-message__input p-messages-scrollbar"></textarea>';
		html += '<div class="p-messages__create-message__button">';
		html += '<button class="p-button p-rounded-button p-messages__create-message__submit" type="submit">' + TCD_MEMBERSHIP_MESSAGES.send_button + '</button>';
		html += '</div>';
		html += '</form>'
		html += '<button class="p-modal__close">&#xe91a;</button>';
		html += '</div>';
		html += '</div>';
		html += '</div>';

		$modalSend = $(html).appendTo('body');
		$modalSendForm = $('form.p-messages__create-message__form');
	};
	var showModalSend = function() {
		if (!$modalSend || !$modalSend.length) {
			createModalSend();
		}
		$('.p-modal.is-active').removeClass('is-active');
		clearModalSendNotice();
		$modalSend.addClass('is-active');
		checkModalScrollbar();
	};

	/**
	 * 送信モーダル ノーティス
	 */
	var addModalSendNotice = function(msg, isError, isReplace) {
		var notice;

		if (isError) {
			notice = '<div class="p-membership-form__error">' + msg + '</div>';
		} else {
			notice = '<div class="p-membership-form__message">' + msg + '</div>';
		}

		if (isReplace) {
			clearModalSendNotice();
		}

		$modalSendForm.append(notice);
	};
	var clearModalSendNotice = function() {
		$modalSendForm.find('.p-membership-form__error, .p-membership-form__message').remove();
	};

	/**
	 * 確認モーダル
	 */
	var $modalConfirm;
	var showModalConfirm = function(msg, okFunction) {
		if (!msg) return false;

		$modalConfirm = $('#js-modal-messages-confirm');

		if (!$modalConfirm.length) {
			var html = '<div id="js-modal-messages-confirm" class="p-modal p-modal--messages p-modal--messages-confirm">';
			html += '<div class="p-modal__contents">';
			html += '<div class="p-modal__contents__inner">';
			html += '<h3 class="p-messages-confirm"></h3>';
			html += '<div class="p-membership-form__button">';
			html += '<button class="p-button p-rounded-button js-ok-button" type="button">' + TCD_MEMBERSHIP_MESSAGES.confirm_button_ok + '</button>';
			html += '<button class="p-button p-button-gray p-rounded-button js-cancel-button" type="button">' + TCD_MEMBERSHIP_MESSAGES.confirm_button_cancel + '</button>';
			html += '</div>';
			html += '</div>';
			html += '</div>';
			html += '</div>';
			$modalConfirm = $(html).appendTo('body');

			// cancel click event
			$modalConfirm.on('click.modalConfirmCancel', '.js-cancel-button', function(){
				$modalConfirm.removeClass('is-active');
				return false;
			});
		}

		$('.p-modal.is-active').removeClass('is-active');
		$modalConfirm.find('.p-messages-confirm').html(msg.replace(/\n/g, '<br>'));
		$modalConfirm.addClass('is-active');
		checkModalScrollbar();

		// ok click event
		$modalConfirm.off('click.modalConfirmOk');
		if (typeof okFunction === 'function') {
			$modalConfirm.on('click.modalConfirmOk', '.js-ok-button', okFunction);
		}
	};

	/**
	 * アラートモーダル
	 */
	var showModalAlert = function(msg) {
		if (!msg) return false;

		var $modalAlert = $('#js-modal-alert');
		if (!$modalAlert.length) {
			var html = '<div id="js-modal-alert" class="p-modal p-modal--messages p-modal--alert">';
			html += '<div class="p-modal__contents">';
			html += '<div class="p-modal__contents__inner">';
			html += '<div class="p-modal__body p-body"></div>';
			html += '<button class="p-modal__close">&#xe91a;</button>';
			html += '</div>';
			html += '</div>';
			html += '</div>';
			$modalAlert = $(html).appendTo('body');
		}

		$('.p-modal.is-active').removeClass('is-active');
		$modalAlert.find('.p-modal__body').html(msg.replace(/\n/g, '<br>'));
		$modalAlert.addClass('is-active');
		checkModalScrollbar();
	};

	/**
	 * メッセージinbox以外からのメッセージ送信
	 */
	if (TCD_MEMBERSHIP_MESSAGES.modal_send) {
		var recipient_user_id, nonce;

		// 送信モーダル生成
		createModalSend();

		// 送信モーダル表示
		$document.on('click', '.js-create-message', function(){
			var $this = $(this);
			recipient_user_id = $this.attr('data-user-id');
			nonce = $this.attr('data-nonce');

			if (recipient_user_id && nonce) {
				showModalSend();
			}

			return false;
		});

		// 送信モーダルからのサブミット
		$modalSendForm.on('submit', function(){
			var $message = $modalSendForm.find('.p-messages__create-message__input');
			var message = $message.val() || '';
			if (!message.trim() || $modalSendForm.hasClass('is-ajaxing')) return false;

			clearModalSendNotice();
			$modalSendForm.addClass('is-ajaxing');

			$.ajax({
				url: TCD_MEMBERSHIP_MESSAGES.ajax_url,
				type: 'POST',
				data: {
					action: 'tcd_messages_send_message',
					recipient_user_id: recipient_user_id,
					nonce: nonce,
					message: message
				}
			}).success(function(data, textStatus, XMLHttpRequest) {
				$modalSendForm.removeClass('is-ajaxing');

				if (data.success) {
					$message.val('');
					if (data.message) {
						addModalSendNotice(data.message, false, true);
					}
				} else if (data.message) {
					addModalSendNotice(data.message, false, true);
				} else if (data.error) {
					addModalSendNotice(data.error, true, true);
				} else {
					addModalSendNotice(TCD_MEMBERSHIP_MESSAGES.ajax_error_message, 'error', true);
				}
			}).error(function(XMLHttpRequest, textStatus, errorThrown) {
				$modalSendForm.removeClass('is-ajaxing');
				addModalSendNotice(TCD_MEMBERSHIP_MESSAGES.ajax_error_message, 'error', true);
			});

			return false;
		});
	}

	/**
	 * メッセージinbox・メッセージ作成・ブロック一覧共通
	 */
	if ($body.hasClass('membership-messages')) {
		/**
		 * モバイルでの会員検索フォーム表示
		 */
		$('.p-member-page-header .js-messages-search').click(function(){
			var $cl = $(this).closest('.p-member-page-header');
			if ($cl.hasClass('is-messages-search-open')) {
				$cl.removeClass('is-messages-search-open');
			} else {
				if (winWidth < 992) {
					$body.removeClass('show-sidemenu');
					$('.p-member-menu').removeClass('is-active');
				}
				$cl.addClass('is-messages-search-open');
			}
			return false;
		});
	}

	/**
	 * メッセージinbox
	 */
	if ($body.hasClass('membership-messages-messages')) {
		var $messagesUsers = $('.p-messages-users');
		var $messagesDetail = $('.p-messages-detail');
		var $messagesDetailMain;
		var messagesAjaxing = false;
		var ajaxUsersInfiniteScrolling = false;
		var usersDisplayedMinMessageId = 0;
		var hasUsersNextPage = false;
		var showDetailUserId = 0;
		var ajaxDetailInfiniteScrolling = false;
		var detailDisplayedMaxMessageId = 0;
		var detailDisplayedMinMessageId = 0;
		var hasDetailPrevPage = false;
		var detailNextTimer = null;

		/**
		 * 高さ調整
		 */
		$window.on('resize', function(){
			var h, mh, $el, pt, pb;

			if (winWidth < 992) {
				$el = $('.p-messages--messages');
				pt = parseInt($el.css('paddingTop'), 10);
				pb = parseInt($el.css('paddingBottom'), 10);
				h = window.innerHeight - Math.floor($el.offset().top) - pt - pb;
				mh = 320;
			} else {
				h = window.innerHeight - Math.floor($messagesUsers.offset().top) - 30;
				mh = 600;
			}

			if (h < mh) {
				h = mh;
			} else if (h < window.innerHeight / 2) {
				h = window.innerHeight / 2;
			}

			if (winWidth < 992) {
				$messagesUsers.height(h);
				$messagesDetail.height(h + pt + pb);
			} else {
				$messagesUsers.height(h);
				$messagesDetail.height(h);
			}
		});

		/**
		 * メッセージ作成 フォームトグル
		 */
		$messagesDetail.on('click', '.p-messages-detail__footer-create', function(){
			var $form = $('.p-messages-detail__footer .p-messages__create-message__form');
			var $detailMain = $('.p-messages-detail__main');
			var fh = Math.ceil($form.outerHeight());
			var st = $detailMain.scrollTop();

			if ($form.is(':visible')) {
				$form.slideUp(300);
				$detailMain.animate({scrollTop: st - fh}, 300);
			} else {
				$form.slideDown(300);
				$detailMain.animate({scrollTop: st + fh}, 300);
			}

			return false;
		});

		/**
		 * ユーザー一覧読み込み
		 */
		var getMessagesUsers = function(nextpage) {
			var getdata = {
				action: 'tcd_messages_get_list',
				search: $('.p-messages-search-members__input').val()
			};

			if (nextpage) {
				if (usersDisplayedMinMessageId) {
					getdata.less_message_id = usersDisplayedMinMessageId;
				} else {
					setUsersInfiniteScroll(true);
					return;
				}
			} else {
				usersDisplayedMinMessageId = 0;
				$messagesUsers.html('');
				clearDetail();
			}

			messagesAjaxing = true;
			$messagesUsers.addClass('is-ajaxing');

			$.ajax({
				url: TCD_MEMBERSHIP_MESSAGES.ajax_url,
				type: 'GET',
				data: getdata
			}).success(function(data, textStatus, XMLHttpRequest) {
				messagesAjaxing = false;
				$messagesUsers.removeClass('is-ajaxing');

				if (data.html) {
					$messagesUsers.append(data.html);
				}

				if (data.min_message_id) {
					if (usersDisplayedMinMessageId) {
						usersDisplayedMinMessageId = Math.min(usersDisplayedMinMessageId, data.min_message_id);
					} else {
						usersDisplayedMinMessageId = data.min_message_id;
					}
				}

				if (data.has_next_page) {
					hasUsersNextPage = true;
				} else {
					hasUsersNextPage = false;
				}

				if (ajaxUsersInfiniteScrolling) {
					if (hasUsersNextPage) {
						ajaxUsersInfiniteScrolling = false;
						$messagesUsers.trigger('scroll');
					} else {
						setUsersInfiniteScroll(true);
					}
				} else if (hasUsersNextPage) {
					setUsersInfiniteScroll();
				}

				if (typeof data.total_unread === 'number') {
					if (data.total_unread) {
						$('.p-messages-total-unread').text(data.total_unread);
					} else {
						$('.p-messages-total-unread').text('');
					}
				}
			}).error(function(XMLHttpRequest, textStatus, errorThrown) {
				messagesAjaxing = false;
				$messagesUsers.removeClass('is-ajaxing');
				setUsersInfiniteScroll(true);
				showModalAlert(TCD_MEMBERSHIP_MESSAGES.ajax_error_message);
			});
		};

		/**
		 * ユーザー一覧 無限スクロール
		 */
		var setUsersInfiniteScroll = function(off){
			if (off) {
				ajaxUsersInfiniteScrolling = false;
				$messagesUsers.off('scroll.messagesUsersInfiniteScroll');
				return;
			}

			$messagesUsers.off('scroll.messagesUsersInfiniteScroll').on('scroll.messagesUsersInfiniteScroll', function(){
				if (ajaxUsersInfiniteScrolling) return;

				var st = $messagesUsers.scrollTop();
				var sh = $messagesUsers.prop('scrollHeight');
				var h = $messagesUsers.innerHeight();
				var itemH = $messagesUsers.find('.p-messages-users__item:last').innerHeight();

				if (st + h >= sh - itemH * 0.5) {
					ajaxUsersInfiniteScrolling = true;
					getMessagesUsers(true);
				}
			}).trigger('scroll');
		};

		/**
		 * 詳細表示
		 */
		var getUserMessages = function(prevpage) {
			if (!showDetailUserId) return;

			var $messagesUsersUser = $messagesUsers.find('.p-messages-users__item[data-user-id="' + showDetailUserId + '"]');

			var getdata = {
				action: 'tcd_messages_get_user_messages',
				user_id: showDetailUserId
			};

			if (prevpage) {
				if (detailDisplayedMinMessageId) {
					getdata.less_message_id = detailDisplayedMinMessageId;
					$messagesDetail.addClass('is-prev-ajaxing');
				} else {
					setUsersInfiniteScroll(true);
					return;
				}
			} else {
				$messagesUsers.find('.p-messages-users__item.is-active').removeClass('is-active');
				$messagesUsersUser.addClass('is-active');
				$messagesDetail.addClass('is-ajaxing');
			}

			messagesAjaxing = true;

			$.ajax({
				url: TCD_MEMBERSHIP_MESSAGES.ajax_url,
				type: 'GET',
				data: getdata
			}).success(function(data, textStatus, XMLHttpRequest) {
				messagesAjaxing = false;
				$messagesDetail.removeClass('is-ajaxing').removeClass('is-prev-ajaxing');

				if (data.error) {
					$messagesUsersUser.removeClass('is-active');
					setDetailInfiniteScroll(true);
					showModalAlert(data.error);
					return;
				}

				if (data.html) {
					if (prevpage) {
						// 追加する前の位置にスクロールする
						var st = $messagesDetailMain.scrollTop();
						var beforeSh = $messagesDetailMain.prop('scrollHeight');

						setDetailInfiniteScroll(true);
						$messagesDetailMain.prepend(data.html);

						var afterSh = $messagesDetailMain.prop('scrollHeight');
						$messagesDetailMain.scrollTop(st + afterSh - beforeSh);
					} else {
						$messagesDetail.html(data.html);
						$messagesDetailMain = $messagesDetail.find('.p-messages-detail__main');
						$body.addClass('show_messages_detail');

						// 最初の未読にスクロ－ル、未読が無ければ末尾にスクロ－ル
						var st = $messagesDetailMain.prop('scrollHeight');
						var $recipientUnreadFirst = $messagesDetailMain.find('.p-messages-detail__message.recipient-unread:first');
						if ($recipientUnreadFirst.length) {
							if ($recipientUnreadFirst.index() === 0) {
								st = 0;
							} else {
								st = $recipientUnreadFirst.position().top + $messagesDetailMain.scrollTop();
							}
						}
						setTimeout(function(){
							$messagesDetailMain.scrollTop(st);
						}, 16);

						// 992未満の場合
						if (winWidth < 992) {
							// スクロール
							$window.scrollTop(0);

							// 戻る対策でhistory.pushState
							history.pushState(null, null, null);
						}
					}
				}

				if (data.max_message_id) {
					if (detailDisplayedMaxMessageId) {
						detailDisplayedMaxMessageId = Math.max(detailDisplayedMaxMessageId, data.max_message_id);
					} else {
						detailDisplayedMaxMessageId = data.max_message_id;
					}
				}

				if (data.min_message_id) {
					if (detailDisplayedMinMessageId) {
						detailDisplayedMinMessageId = Math.min(detailDisplayedMinMessageId, data.min_message_id);
					} else {
						detailDisplayedMinMessageId = data.min_message_id;
					}
				}

				if (data.has_prev_page) {
					hasDetailPrevPage = true;
					$messagesDetail.addClass('has-prev-page');
				} else {
					hasDetailPrevPage = false;
					$messagesDetail.removeClass('has-prev-page');
				}

				if (ajaxDetailInfiniteScrolling) {
					if (hasDetailPrevPage) {
						ajaxDetailInfiniteScrolling = false;
						$messagesUsers.trigger('scroll');
					} else {
						setDetailInfiniteScroll(true);
					}
				} else if (hasDetailPrevPage) {
					setDetailInfiniteScroll();
				}

				if (typeof data.total_unread === 'number') {
					if (data.total_unread) {
						$('.p-messages-total-unread').text(data.total_unread);
					} else {
						$('.p-messages-total-unread').text('');
					}
				}

				if (typeof data.user_unread === 'number') {
					if (data.user_unread) {
						$messagesUsersUser.find('.p-messages-user__unread').text(data.user_unread);
					} else {
						$messagesUsersUser.find('.p-messages-user__unread').text('');
					}
				}

				setTimeoutDetailNext();
			}).error(function(XMLHttpRequest, textStatus, errorThrown) {
				messagesAjaxing = false;
				$messagesDetail.removeClass('is-ajaxing').removeClass('is-prev-ajaxing');
				$messagesUsersUser.removeClass('is-active');
				showModalAlert(TCD_MEMBERSHIP_MESSAGES.ajax_error_message);
			});
		};

		/**
		 * ユーザー一覧からユーザークリックで詳細表示
		 */
		$messagesUsers.on('click', '.p-messages-users__item', function(){
			var $this = $(this);
			var user_id = $this.attr('data-user-id');
			if (messagesAjaxing || !user_id) return false;

			clearDetail();
			showDetailUserId = user_id;
			getUserMessages();

			return false;
		});

		/**
		 * 詳細表示クリア
		 */
		var clearDetail = function(){
			showDetailUserId = 0;
			detailDisplayedMaxMessageId = 0;
			detailDisplayedMinMessageId = 0;
			hasDetailPrevPage = false;
			$body.removeClass('show_messages_detail');
			$messagesDetail.html('').removeClass('is-ajaxing').removeClass('is-prev-ajaxing').removeClass('is-next-ajaxing').removeClass('has-prev-page');
			$messagesDetailMain = null;
			setDetailInfiniteScroll(true);
			clearTimeoutDetailNext();
		};

		/**
		 * 詳細main 無限スクロール
		 */
		var setDetailInfiniteScroll = function(off){
			if (!$messagesDetailMain) {
				$messagesDetailMain = $messagesDetail.find('.p-messages-detail__main');
			}

			if (off) {
				ajaxDetailInfiniteScrolling = false;
				$messagesDetailMain.off('scroll.messagesDetailInfiniteScroll');
				return;
			}

			$messagesDetailMain.off('scroll.messagesDetailInfiniteScroll').on('scroll.messagesDetailInfiniteScroll', function(){
				if (ajaxDetailInfiniteScrolling) return;

				var st = $messagesDetailMain.scrollTop();
				var pt = parseInt($messagesDetailMain.css('paddingTop'), 10);
				var itemH = $messagesDetailMain.find('.p-messages-detail__message:first').height();

				if (st <= itemH * 0.5 + pt) {
					ajaxDetailInfiniteScrolling = true;
					getUserMessages(true);
				}
			}).trigger('scroll');
		};

		/**
		 * 詳細 新着メッセージ取得
		 */
		var getDetailNext = function(){
			clearTimeoutDetailNext();

			if (showDetailUserId && detailDisplayedMaxMessageId) {
				messagesAjaxing = true;
				$messagesDetail.removeClass('is-next-ajaxing');

				$.ajax({
					url: TCD_MEMBERSHIP_MESSAGES.ajax_url,
					type: 'GET',
					data: {
						action: 'tcd_messages_get_user_messages_latest',
						user_id: showDetailUserId,
						greater_message_id: detailDisplayedMaxMessageId
					}
				}).success(function(data, textStatus, XMLHttpRequest) {
					messagesAjaxing = false;
					$messagesDetail.removeClass('is-next-ajaxing');

					if (data.html) {
						$messagesDetailMain.append(data.html);
					}

					if (data.max_message_id) {
						if (detailDisplayedMaxMessageId) {
							detailDisplayedMaxMessageId = Math.max(detailDisplayedMaxMessageId, data.max_message_id);
						} else {
							detailDisplayedMaxMessageId = data.max_message_id;
						}
					}

					if (typeof data.total_unread === 'number') {
						if (data.total_unread) {
							$('.p-messages-total-unread').text(data.total_unread);
						} else {
							$('.p-messages-total-unread').text('');
						}
					}

					if (typeof data.user_unread === 'number') {
						var $messagesUsersUser = $messagesUsers.find('.p-messages-users__item[data-user-id="' + showDetailUserId + '"]');
						if (data.user_unread) {
							$messagesUsersUser.find('.p-messages-user__unread').text(data.user_unread);
						} else {
							$messagesUsersUser.find('.p-messages-user__unread').text('');
						}
					}

					setTimeoutDetailNext();
				}).error(function(XMLHttpRequest, textStatus, errorThrown) {
					messagesAjaxing = false;
					$messagesDetail.removeClass('is-next-ajaxing');
				});
			}
		};
		var setTimeoutDetailNext = function(){
			clearTimeoutDetailNext();
			detailNextTimer = setTimeout(getDetailNext, 60000);
		};
		var clearTimeoutDetailNext = function(){
			if (detailNextTimer) {
				clearTimeout(detailNextTimer);
				detailNextTimer = null;
			}
		};

		/**
		 * ユーザーブロック
		 */
		var ajaxBlock = function(elem, user_id, nonce) {
			if (!elem || !user_id || !nonce) return;
			var $elem = $(elem);

			$elem.addClass('is-ajaxing');

			$.ajax({
				url: TCD_MEMBERSHIP_MESSAGES.ajax_url,
				type: 'POST',
				data: {
					action: 'tcd_messages_add_block',
					user_id: user_id,
					nonce: nonce
				}
			}).success(function(data, textStatus, XMLHttpRequest) {
				$elem.removeClass('is-ajaxing');

				if (data.success) {
					clearDetail();

					var $MessagesUsersUser = $messagesUsers.find('.p-messages-users__item[data-user-id="' + user_id + '"]');
					if ($MessagesUsersUser.length) {
						$MessagesUsersUser.fadeOut(300, function(){
							$(this).remove();
						});
					} else {
						$messagesUsers.find('.p-messages-users__item.is-active').removeClass('is-active');
					}
				} else if (data.error) {
					showModalAlert(data.error);
				} else {
					showModalAlert(TCD_MEMBERSHIP_MESSAGES.ajax_error_message);
				}
			}).error(function(XMLHttpRequest, textStatus, errorThrown) {
				$elem.removeClass('is-ajaxing');
				showModalAlert(TCD_MEMBERSHIP_MESSAGES.ajax_error_message);
			});
		};
		$messagesDetail.on('click', '.p-messages-user__nav-block a', function(){
			var $this = $(this);
			if (messagesAjaxing || $this.hasClass('is-ajaxing')) return false;

			var user_id = $this.attr('data-user-id');
			var nonce = $this.attr('data-nonce');
			if (!user_id || !nonce) return false;

			if (TCD_MEMBERSHIP_MESSAGES.confirm_block) {
				showModalConfirm(TCD_MEMBERSHIP_MESSAGES.confirm_block, function(){
					$modalConfirm.removeClass('is-active');
					ajaxBlock($this, user_id, nonce);
					return false;
				});
			} else {
				ajaxBlock($this, user_id, nonce);
			}

			return false;
		});

		/**
		 * メッセージ全削除
		 */
		var ajaxDeleteAll = function(elem, user_id, nonce) {
			if (!elem || !user_id || !nonce) return;
			var $elem = $(elem);

			$elem.addClass('is-ajaxing');

			$.ajax({
				url: TCD_MEMBERSHIP_MESSAGES.ajax_url,
				type: 'POST',
				data: {
					action: 'tcd_messages_delete_all',
					user_id: user_id,
					nonce: nonce
				}
			}).success(function(data, textStatus, XMLHttpRequest) {
				$elem.removeClass('is-ajaxing');

				if (data.success) {
					clearDetail();

					var $MessagesUsersUser = $messagesUsers.find('.p-messages-users__item[data-user-id="' + user_id + '"]');
					if ($MessagesUsersUser.length) {
						$MessagesUsersUser.slideUp(300, function(){
							$(this).remove();
						});
					} else {
						$messagesUsers.find('.p-messages-users__item.is-active').removeClass('is-active');
					}
				} else if (data.error) {
					showModalAlert(data.error);
				} else {
					showModalAlert(TCD_MEMBERSHIP_MESSAGES.ajax_error_message);
				}
			}).error(function(XMLHttpRequest, textStatus, errorThrown) {
				$elem.removeClass('is-ajaxing');
				showModalAlert(TCD_MEMBERSHIP_MESSAGES.ajax_error_message);
			});
		};
		$messagesDetail.on('click', '.p-messages-user__nav-delete-all a', function(){
			var $this = $(this);
			if (messagesAjaxing || $this.hasClass('is-ajaxing')) return false;

			var user_id = $this.attr('data-user-id');
			var nonce = $this.attr('data-nonce');
			if (!user_id || !nonce) return false;

			if (TCD_MEMBERSHIP_MESSAGES.confirm_delete_all) {
				showModalConfirm(TCD_MEMBERSHIP_MESSAGES.confirm_delete_all, function(){
					$modalConfirm.removeClass('is-active');
					ajaxDeleteAll($this, user_id, nonce);
					return false;
				});
			} else {
				ajaxDeleteAll($this, user_id, nonce);
			}

			return false;
		});

		/**
		 * 単一メッセージ削除
		 */
		var ajaxDelete = function(elem, message_id, nonce) {
			if (!elem || !message_id || !nonce) return;
			var $elem = $(elem);

			$elem.addClass('is-ajaxing');

			$.ajax({
				url: TCD_MEMBERSHIP_MESSAGES.ajax_url,
				type: 'POST',
				data: {
					action: 'tcd_messages_delete',
					message_id: message_id,
					nonce: nonce
				}
			}).success(function(data, textStatus, XMLHttpRequest) {
				$elem.removeClass('is-ajaxing');

				if (data.success) {
					$elem.closest('.p-messages-detail__message').slideUp(300, function(){
						$(this).remove();

						// 表示メッセージ無しになった場合
						if (!$messagesDetailMain.find('.p-messages-detail__message').length) {
							clearDetail();
							var $MessagesUsersUser = $messagesUsers.find('.p-messages-users__item.is-active');
							if ($MessagesUsersUser.length) {
								$MessagesUsersUser.slideUp(300, function(){
									$(this).remove();
								});
							}
						}
					});
				} else if (data.error) {
					showModalAlert(data.error);
				} else {
					showModalAlert(TCD_MEMBERSHIP_MESSAGES.ajax_error_message);
				}
			}).error(function(XMLHttpRequest, textStatus, errorThrown) {
				$elem.removeClass('is-ajaxing');
				showModalAlert(TCD_MEMBERSHIP_MESSAGES.ajax_error_message);
			});
		};
		$messagesDetail.on('click', '.p-messages-detail__message a.p-messages-detail__message-delete', function(){
			var $this = $(this);
			if (messagesAjaxing || $this.hasClass('is-ajaxing')) return false;

			var $cl = $this.closest('.p-messages-detail__message');
			var message_id = $cl.attr('data-message-id');
			var nonce = $cl.attr('data-delete-nonce');
			if (!message_id || !nonce) return false;

			if (TCD_MEMBERSHIP_MESSAGES.confirm_delete) {
				showModalConfirm(TCD_MEMBERSHIP_MESSAGES.confirm_delete, function(){
					$modalConfirm.removeClass('is-active');
					ajaxDelete($this, message_id, nonce);
					return false;
				});
			} else {
				ajaxDelete($this, message_id, nonce);
			}

			return false;
		});

		/**
		 * メッセージ送信
		 */
		$messagesDetail.on('submit', '.p-messages-detail__footer .p-messages__create-message__form', function(){
			var $form = $(this);
			var $message = $form.find('.p-messages__create-message__input');
			var message = $message.val() || '';
			if (messagesAjaxing || $form.hasClass('is-ajaxing') || !message.trim()) return false;

			var recipient_user_id = $form.attr('data-user-id');
			var nonce = $form.attr('data-nonce');
			if (!recipient_user_id || !nonce) return false;

			messagesAjaxing = true;
			$form.addClass('is-ajaxing');

			$.ajax({
				url: TCD_MEMBERSHIP_MESSAGES.ajax_url,
				type: 'POST',
				data: {
					action: 'tcd_messages_send_message',
					recipient_user_id: recipient_user_id,
					nonce: nonce,
					message: message
				}
			}).success(function(data, textStatus, XMLHttpRequest) {
				messagesAjaxing = false;
				$form.removeClass('is-ajaxing');

				if (data.success) {
					$message.val('');
					getDetailNext();
				} else if (data.error) {
					showModalAlert(data.error);
				} else {
					showModalAlert(TCD_MEMBERSHIP_MESSAGES.ajax_error_message);
				}
			}).error(function(XMLHttpRequest, textStatus, errorThrown) {
				messagesAjaxing = false;
				$form.removeClass('is-ajaxing');
				showModalAlert(TCD_MEMBERSHIP_MESSAGES.ajax_error_message);
			});

			return false;
		});

		/**
		 * 会員検索
		 */
		$('form.p-messages-search-members').submit(function(){
			if (messagesAjaxing) return false;
			getMessagesUsers();
			return false;
		});

		/**
		 * ブラウザ戻る対策
		 */
		$window.on('popstate', function(event) {
			// 詳細表示からの戻るでユーザー一覧表示
			if (showDetailUserId) {
				clearDetail();
				$messagesUsers.find('.p-messages-users__item.is-active').removeClass('is-active');
			}
		});

		/**
		 * 初期処理 ユーザー一覧読み込み
		 */
		getMessagesUsers();

	/**
	 * メッセージ作成 宛先リスト表示の場合
	 */
	} else if ($body.hasClass('membership-messages_create')) {
		var $userList = $('#js-messages-recipients');
		var currentPage = 1;
		var userListCache = {};
		var tabsInitialOffsetTop = 0;
		var tabCount = $('.p-author__list-tab').length;
		var ajaxloading = false;
		var ajaxInfiniteScrolling = false;

		/**
		 * 宛先リスト取得
		 */
		var getUserList = function(listType, nextpage, scrollTo) {
			if (!listType) return false;

			var page = 1;
			if (nextpage) {
				page = currentPage + 1;
			}

			var searchVal = $('.p-messages-search-members__input').val();

			if (scrollTo) {
				var toScrollTop = tabsInitialOffsetTop - 90;
				if ($window.scrollTop() > toScrollTop) {
					$window.scrollTop(toScrollTop);
				}
			}

			if (page == 1) {
				$('.p-author__list-tab.max-paged').removeClass('max-paged');
			}

			if (!userListCache[listType]) {
				userListCache[listType] = {};
				userListCache[listType][page] = {};
				if (searchVal) {
					userListCache[listType][searchVal] = {};
					userListCache[listType][searchVal][page] = {};
				}
			} else {
				if (searchVal) {
					if (userListCache[listType][searchVal]) {
						if (userListCache[listType][searchVal][page]) {
							render_user_list(userListCache[listType][searchVal][page], page);
							currentPage = page;
							return;
						} else {
							userListCache[listType][searchVal][page] = {};
						}
					} else {
						userListCache[listType][searchVal] = {};
						userListCache[listType][searchVal][page] = {};
					}
				} else if (userListCache[listType][page]) {
					render_user_list(userListCache[listType][page], page);
					currentPage = page;
					return;
				}
			}

			if (page == 1) {
				$userList.html('').addClass('ajax-loading');
			} else {
				$userList.addClass('ajax-infinite-loading');
			}

			ajaxloading = true;
			$.ajax({
				url: TCD_MEMBERSHIP_MESSAGES.ajax_url,
				type: 'GET',
				data: {
					action: 'tcd_messages_get_recipients',
					list_type: listType,
					paged: page,
					search: searchVal
				}
			}).success(function(data, textStatus, XMLHttpRequest) {
				render_user_list(data, page);

				if (searchVal) {
					userListCache[listType][searchVal][page] = data;
				} else {
					userListCache[listType][page] = data;
				}

				currentPage = page;
				ajaxloading = false;
				ajaxInfiniteScrolling = false;
			}).error(function(XMLHttpRequest, textStatus, errorThrown) {
				$userList.removeClass('ajax-loading').removeClass('ajax-infinite-loading').append('<p class="p-author__list-error">' + TCD_MEMBERSHIP_MESSAGES.ajax_error_message + '</p>');
				ajaxloading = false;
				ajaxInfiniteScrolling = false;
			});
		};

		/**
		 * 宛先リストフェードイン表示
		 */
		var render_user_list = function(data, page){
			var $data = $($.parseHTML(data));
			if (page == 1) {
				$data.find('.p-author__list-item').addClass('fadein').css('opacity', 0);
				$userList.removeClass('ajax-loading').html($data);
			} else {
				$data.filter('.p-author__list-item').addClass('fadein').css('opacity', 0);
				$userList.removeClass('ajax-infinite-loading').find('.p-author__list__inner').append($data);
				$window.trigger('scroll');
			}

			// object-fit: cover未対応ブラウザ対策
			$window.trigger('resize.object_fit_cover');

			$userList.find('.fadein').each(function(i){
				$(this).removeClass('fadein').delay((i+1) * 300).animate({opacity: 1}, 500);
			});
		};

		/**
		 * ロード時処理
		 */
		$document.on('js-initialized-after', function(){
			getUserList($('.p-author__list-tab.is-active').attr('data-list-type'), false, false);

			/**
			 * タブfixed表示
			 */
			if (tabCount > 1) {
				tabsInitialOffsetTop = $userList.offset().top - $('.p-author__list-tabs').height();
				$window.on('resize', function(){
					tabsInitialOffsetTop = $userList.offset().top;
				}).on('scroll', function(){
					var st = $window.scrollTop();
					if (st > 300 && st > tabsInitialOffsetTop - 150) {
						var t = $('.p-author__list-tabs').height();
						var h = $userList.innerHeight();
						if (winWidth > 600) {
							t += $('#wpadminbar').height();
						}
						if ($('.l-header__bar').css('position') === 'fixed') {
							t += $('.l-header__bar').height();
						}
						if (st > tabsInitialOffsetTop - t && st < tabsInitialOffsetTop - t + h - 150) {
							$body.addClass('is-author__list-tabs-fixed');
						} else if ($body.hasClass('is-author__list-tabs-fixed')) {
							$body.removeClass('is-author__list-tabs-fixed');
						}
					} else if ($body.hasClass('is-author__list-tabs-fixed')) {
						$body.removeClass('is-author__list-tabs-fixed');
					}
				});
			}

			/**
			 * 無限スクロール
			 */
			$window.on('scroll', function(){
				if (ajaxloading || ajaxInfiniteScrolling) return;

				var $activeTab = $('.p-author__list-tab.is-active');
				if ($activeTab.hasClass('max-paged')) return;

				var st = $window.scrollTop();
				var wh = window.innerHeight || $window.innerHeight();
				var alt = $userList.offset().top;
				var alh = $userList.height();
				if (st + wh * 0.8 >= alt + alh && st + wh * 0.2 <= alt + alh ) {
					var maxPage = $activeTab.attr('data-max-page');
					if (currentPage + 1 > maxPage) {
						$activeTab.addClass('max-paged');
					} else {
						ajaxInfiniteScrolling = true;
						getUserList($activeTab.attr('data-list-type'), true, false);
						if (currentPage + 1 >= maxPage) {
							$activeTab.addClass('max-paged');
						}
					}
				}
			});

			/**
			 * タブクリック
			 */
			$('.p-author__list-tab').click(function(){
				if ($(this).hasClass('is-active') || ajaxloading) return false;
				$(this).siblings('.is-active').removeClass('is-active');
				$(this).addClass('is-active');
				getUserList($(this).attr('data-list-type'), false, true);
				return false;
			});

			/**
			 * 会員検索
			 */
			$('form.p-messages-search-members').submit(function(){
				getUserList($('.p-author__list-tab.is-active').attr('data-list-type'), false, true);
				return false;
			});
		});

	/**
	 * ブロック一覧
	 */
	} else if ($body.hasClass('membership-messages_blocked_members')) {
		var $userList = $('#js-messages-blocked-members');
		var currentPage = 1;
		var maxPaged = false;
		var ajaxloading = false;
		var ajaxInfiniteScrolling = false;

		/**
		 * ブロックユーザーリスト取得
		 */
		var getUserList = function(nextpage, scrollTo) {
			var page = 1;
			if (nextpage) {
				page = currentPage + 1;
			}

			var searchVal = $('.p-messages-search-members__input').val();

			if (scrollTo) {
				var toScrollTop = $userList.offset().top;
				if ($window.scrollTop() > toScrollTop) {
					$window.scrollTop(toScrollTop);
				}
			}

			if (page == 1) {
				maxPaged = false;
				$userList.html('').addClass('ajax-loading');
			} else {
				$userList.addClass('ajax-infinite-loading');
			}

			ajaxloading = true;
			$.ajax({
				url: TCD_MEMBERSHIP_MESSAGES.ajax_url,
				type: 'GET',
				data: {
					action: 'tcd_messages_get_blocked_members',
					paged: page,
					search: searchVal
				}
			}).success(function(data, textStatus, XMLHttpRequest) {
				render_user_list(data, page);
				currentPage = page;
				ajaxloading = false;
				ajaxInfiniteScrolling = false;
			}).error(function(XMLHttpRequest, textStatus, errorThrown) {
				$userList.removeClass('ajax-loading').removeClass('ajax-infinite-loading').append('<p class="p-author__list-error">' + TCD_MEMBERSHIP_MESSAGES.ajax_error_message + '</p>');
				ajaxloading = false;
				ajaxInfiniteScrolling = false;
			});
		};

		/**
		 * ブロックユーザーリストフェードイン表示
		 */
		var render_user_list = function(data, page){
			if (!data.trim()) {
				maxPaged = true;
			}

			var $data = $($.parseHTML(data));
			if (page == 1) {
				$data.find('.p-user-list__item').addClass('fadein').css('opacity', 0);
				$userList.removeClass('ajax-loading').html($data);
			} else {
				$data.filter('.p-user-list__item').addClass('fadein').css('opacity', 0);
				$userList.removeClass('ajax-infinite-loading').append($data);
				$window.trigger('scroll');
			}

			// object-fit: cover未対応ブラウザ対策
			$window.trigger('resize.object_fit_cover');

			$userList.find('.fadein').each(function(i){
				$(this).removeClass('fadein').delay((i+1) * 300).animate({opacity: 1}, 500);
			});
		};

		/**
		 * ブロック解除
		 */
		var ajaxUnblock = function(elem, user_id, nonce) {
			if (!elem || !user_id || !nonce) return;
			var $elem = $(elem);

			$elem.addClass('is-ajaxing');

			$.ajax({
				url: TCD_MEMBERSHIP_MESSAGES.ajax_url,
				type: 'POST',
				data: {
					action: 'tcd_messages_remove_block',
					user_id: user_id,
					nonce: nonce
				}
			}).success(function(data, textStatus, XMLHttpRequest) {
				$elem.removeClass('is-ajaxing');

				if (data.success) {
					$elem.closest('.p-user-list__item').fadeOut(300, function(){
						$(this).remove();
					});
				} else if (data.error) {
					showModalAlert(data.error);
				}
			}).error(function(XMLHttpRequest, textStatus, errorThrown) {
				$elem.removeClass('is-ajaxing');
				showModalAlert(TCD_MEMBERSHIP_MESSAGES.ajax_error_message);
			});
		};
		$userList.on('click', '.p-button-unblock', function(){
			var $this = $(this);
			var user_id = $this.attr('data-user-id');
			var nonce = $this.attr('data-nonce');
			if (!user_id || !nonce || $this.hasClass('is-ajaxing')) return false;

			if (TCD_MEMBERSHIP_MESSAGES.confirm_unblock) {
				showModalConfirm(TCD_MEMBERSHIP_MESSAGES.confirm_unblock, function(){
					$modalConfirm.removeClass('is-active');
					ajaxUnblock($this, user_id, nonce);
					return false;
				});
			} else {
				ajaxUnblock($this, user_id, nonce);
			}

			return false;
		});

		/**
		 * ロード時処理
		 */
		$document.on('js-initialized-after', function(){
			getUserList(false, false);

			/**
			 * 無限スクロール
			 */
			$window.on('scroll', function(){
				if (ajaxloading || ajaxInfiniteScrolling || maxPaged) return;

				var st = $window.scrollTop();
				var wh = window.innerHeight || $window.innerHeight();
				var alt = $userList.offset().top;
				var alh = $userList.height();
				if (st + wh * 0.8 >= alt + alh && st + wh * 0.2 <= alt + alh ) {
					ajaxInfiniteScrolling = true;
					getUserList(true, false);
				}
			});

			/**
			 * 会員検索
			 */
			$('form.p-messages-search-members').submit(function(){
				getUserList(false, true);
				return false;
			});
		});
	}

});
