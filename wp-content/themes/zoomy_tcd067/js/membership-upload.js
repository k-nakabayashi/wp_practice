jQuery(function($){

	/**
	 * リサイズ画像アップロードがある場合のフォームサブミット
	 */
	$('form.p-membership-form[enctype]').on('submit.ajaxImageUpload', function(){
		if ($(this).hasClass('is-ajaxing')) return false;

		var $this = $(this);
		var $previews = $(this).find('.has-upload-preview .p-membership-form__image-upload__drop-inner2');

		// リサイズ画像アップロードがあればAjaxアップロード
		if ($previews.length) {
			// base64からblob変換
			function dataURLtoBlob(dataurl) {
				var bin = atob(dataurl.split('base64,')[1]);
				var len = bin.length;
				var barr = new Uint8Array(len);
				for (var i = 0; i < len; i++) {
					barr[i] = bin.charCodeAt(i);
				}
				return new Blob([barr], {
					type: 'image/jpeg',
				});
			}

			var images = 0;
			var fd = new FormData();
			fd.append('action', 'tcd_membership_image_upload');
			if ($(this).is('#js-edit-profile-form')) {
				fd.append('type', 'edit_profile');
				fd.append('nonce', $(this).find('[name="nonce"]').val());
			} else {
				fd.append('type', TCD_MEMBERSHIP_UPLOAD.memberpage_type);
				fd.append('post_id', $(this).find('[name="post_id"]').val());
				fd.append('nonce', $(this).find('[name="nonce"]').val());
			}

			$previews.each(function(){
				var $inputFileName = $(this).closest('.p-membership-form__image-upload__drop').find(':file').first().attr('name');
				if (!$inputFileName) return;

				var base64, blob, ext;

				// base64形式の画像データ取得
				base64 = $(this).css('background-image');

				// base64データ前後についているurl()を取り除く 「"」はEdgeだとなくなるので注意
				base64 = base64.replace(/^url\(\"?/, '').replace(/\"?\)$/, '')

				// blobに変換
				blob = dataURLtoBlob(base64);

				// ファイル名指定してフォームデータに追加
				if (blob.type == 'image/png') {
					ext = '.png';
				} else if (blob.type == 'image/gif') {
					ext = '.gif';
				} else {
					ext = '.jpg';
				}
				fd.append($inputFileName, blob, $inputFileName+ext);

				images++;
			});

			if (images) {
				$(this).addClass('is-ajaxing').addClass('is-processing');

				$.ajax({
					url: TCD_MEMBERSHIP.ajax_url,
					type: 'POST',
					data: fd,
					processData: false,
					contentType: false,
					success: function(data, textStatus, XMLHttpRequest) {
						$this.removeClass('is-ajaxing').removeClass('is-processing');
						// アップロードデータ代入
						if (data.uploaded) {
							$.each(data.uploaded, function(k, v) {
								$this.find('input[name="'+k+'"]').val(v);
							});
						}

						// 成功
						if (data.success) {
							var $toConfirm = $this.find('[name="to_confirm"]');
							$this.off('submit.ajaxImageUpload');

							// 確認ボタンがあれば確認ボタンクリック、なければサブミット
							if ($toConfirm.length) {
								$toConfirm.trigger('click');
							} else {
								$this.trigger('submit');
							}
						} else if (data.message) {
							showModalAlert(data.message);
						} else {
							showModalAlert(TCD_MEMBERSHIP.ajax_error_message);
						}
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						$this.removeClass('is-ajaxing').removeClass('is-processing');
						showModalAlert(TCD_MEMBERSHIP.ajax_error_message);
					}
				});

				return false;
			}
		}

	});

	/**
	 * 画像削除ボタン
	 */
	$('.p-membership-form__image-upload__delete-hidden').val(0);
	$(document).on('click', '.p-membership-form__image-upload__drop .p-membership-form__image-upload__delete-button', function(){
		var $drop = $(this).closest('.p-membership-form__image-upload__drop');
		if ($drop.hasClass('has-upload-preview')) {
			$drop.removeClass('has-upload-preview');
			$drop.find('.p-membership-form__image-upload__drop-inner2').removeAttr('style');
		} else if ($drop.hasClass('has-image')) {
			$drop.removeClass('has-image');
			$drop.find('.p-membership-form__image-upload__drop-inner').removeAttr('style');
			$drop.find('.p-membership-form__image-upload__delete-hidden').val(1);
		}
		$drop.find(':file').val('');
		return false;
	});

	/**
	 * 画像回転ボタン
	 */
	$(document).on('click', '.p-membership-form__image-upload__drop .p-membership-form__image-upload__rotate-button1', function(){
		var $drop = $(this).closest('.p-membership-form__image-upload__drop');
		var drop = $drop.get(0);

		// 回転・縮小済みのプレビュー表示
		setImagePreview({
			drop: drop,
			addRotate: 270,
			fileToEmpty: false
		});

		return false;
	});
	$(document).on('click', '.p-membership-form__image-upload__drop .p-membership-form__image-upload__rotate-button2', function(){
		var $drop = $(this).closest('.p-membership-form__image-upload__drop');
		var drop = $drop.get(0);

		// 回転・縮小済みのプレビュー表示
		setImagePreview({
			drop: drop,
			addRotate: 90,
			fileToEmpty: false
		});

		return false;
	});

	/**
	 * Canvasに画像を読み込んだ時にJPEGのEXIFからOrientationのみが自動反映されるか
	 * 参考 https://github.com/blueimp/JavaScript-Load-Image/blob/master/js/load-image-orientation.js
	 */
	var isCanvasAutoExifOrientation = false;
	function detectCanvasAutoExifOrientation() {
		// black+white 3x2 JPEG, with the following meta information set:
		// - EXIF Orientation: 6 (Rotated 90° CCW)
		var testImageURL =
			'data:image/jpeg;base64,/9j/4QAiRXhpZgAATU0AKgAAAAgAAQESAAMAAAABAAYAAAA' +
			'AAAD/2wCEAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBA' +
			'QEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQE' +
			'BAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAf/AABEIAAIAAwMBEQACEQEDEQH/x' +
			'ABRAAEAAAAAAAAAAAAAAAAAAAAKEAEBAQADAQEAAAAAAAAAAAAGBQQDCAkCBwEBAAAAAAA' +
			'AAAAAAAAAAAAAABEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8AG8T9NfSMEVMhQ' +
			'voP3fFiRZ+MTHDifa/95OFSZU5OzRzxkyejv8ciEfhSceSXGjS8eSdLnZc2HDm4M3BxcXw' +
			'H/9k=';
		var img = new Image();
		var img2 = new Image();
		img.style.imageOrientation = 'none';
		img.onload = function(){
			var canvas = document.createElement('canvas');
			var ctx = canvas.getContext('2d');
			canvas.width = this.naturalWidth;
			canvas.height =  this.naturalHeight;
			canvas.style.imageOrientation = 'none';
			ctx.drawImage(this, 0, 0, this.naturalWidth, this.naturalHeight);
			ctx.save();
			img2.src = ctx.canvas.toDataURL('image/jpeg', 0.9);
			canvas = ctx = null;
		}
		img2.onload = function(){
			if (this.naturalWidth === 2 && this.naturalHeight === 3) {
				isCanvasAutoExifOrientation = true;
			}
		}
		img.src = testImageURL;
	}
	detectCanvasAutoExifOrientation();

	/* 回転・縮小済みのプレビュー表示
	 * 参考 https://egashira.jp/image-resize-before-upload
	 */
	var setImagePreview = function(args) {
		if (typeof args !== 'object') return false;
		if (!args.drop) return false;

		var drop = args.drop,
			previewVars = args.drop.previewVars;

		if (!previewVars.reader || !previewVars.img) return false;

		var iw = previewVars.img.naturalWidth,
			ih = previewVars.img.naturalHeight,
			width = iw,
			height = ih;
			maxWidth = previewVars.maxWidth,
			maxHeight = previewVars.maxHeight,
			maxCrop = previewVars.maxCrop,
			img = previewVars.img,
			rotate = drop.previewVars.rotate,
			exifOrientation = previewVars.exifOrientation;	// ChromeがWindow.orientaion持っている関係でorientaionだと上手くいかない

		// 縮小する画像のサイズ差し替え
		var updatedWidthAndHeight = false;
		if (TCD_MEMBERSHIP_UPLOAD.image_size_photo1) {
			// 正方形
			if (iw == ih && TCD_MEMBERSHIP_UPLOAD.image_size_photo3) {
				maxWidth = TCD_MEMBERSHIP_UPLOAD.image_size_photo3.width;
				maxHeight = TCD_MEMBERSHIP_UPLOAD.image_size_photo3.height;

				if (iw < maxWidth && ih < maxHeight) {
					width = iw;
					height = ih;
				} else {
					width = maxWidth;
					height = maxHeight;
				}
				updatedWidthAndHeight = true;

			// 縦長
			} else if (iw < ih && TCD_MEMBERSHIP_UPLOAD.image_size_photo2) {
				maxWidth = TCD_MEMBERSHIP_UPLOAD.image_size_photo2.width;
				maxHeight = TCD_MEMBERSHIP_UPLOAD.image_size_photo2.height;

				if (iw <= maxWidth && ih <= maxHeight) {

				} else if (maxHeight && maxHeight / maxWidth < ih / iw) {
					height = maxHeight;
					width = Math.round(iw * height / ih);
				} else {
					width = maxWidth;
					height = Math.round(ih * width / iw);
				}
				updatedWidthAndHeight = true;

			// 横長
			} else if (TCD_MEMBERSHIP_UPLOAD.image_size_photo1) {
				maxWidth = TCD_MEMBERSHIP_UPLOAD.image_size_photo1.width;
				maxHeight = TCD_MEMBERSHIP_UPLOAD.image_size_photo1.height;

				if (iw <= maxWidth && ih <= maxHeight) {

				} else if (maxWidth && maxHeight / maxWidth > ih / iw) {
					width = maxWidth;
					height = Math.round(ih * width / iw);
				} else {
					height = maxHeight;
					width = Math.round(iw * height / ih);
				}
				updatedWidthAndHeight = true;
			}

		} else if (TCD_MEMBERSHIP_UPLOAD.image_size) {
			maxWidth = TCD_MEMBERSHIP_UPLOAD.image_size.width;
			maxHeight = TCD_MEMBERSHIP_UPLOAD.image_size.height;
		}

		if (0 >= maxWidth) {
			maxWidth = 99999;
		}
		if (0 >= maxHeight) {
			maxHeight = 99999;
		}

		// 追加回転あればrotateに加算
		if (args.addRotate === 90 || args.addRotate == 270) {
			drop.previewVars.rotate = (drop.previewVars.rotate + args.addRotate) % 360;
			rotate = drop.previewVars.rotate;
		}

		// rotate追加回転を反映したexifOrientationを算出
		if (rotate) {
			exifOrientation = getAddRotatedExifOrientation(exifOrientation, rotate);
		}

		// 90度回転など、縦横が入れ替わる場合には事前に最大幅、高さを入れ替えておく
		if (exifOrientation > 4) {
			var tmpMaxWidth = maxWidth;
			maxWidth = maxHeight;
			maxHeight = tmpMaxWidth;
		}

		// 縮小画像サイズ計算
		var cropWidth = 0, cropHeight = 0;
		if (!updatedWidthAndHeight) {
			if (width > maxWidth || height > maxHeight) {
				if (maxCrop) {
					if (width >= maxWidth && height >= maxHeight) {
						if (height / width < maxHeight / maxWidth) {
							width = Math.round(width * maxHeight / height );
							height = maxHeight;
							cropSx = Math.floor((width - maxWidth) / 2);
							cropWidth = maxWidth;
							cropHeight = height;
						} else {
							height = Math.round(height * maxWidth / width );
							width = maxWidth;
							cropSy = Math.floor((height - maxHeight) / 2);
							cropWidth = width;
							cropHeight = maxHeight;
						}
					} else if (width > maxWidth) {
						cropWidth = maxWidth
						cropHeight = height;
					} else if (height > maxHeight) {
						cropWidth = width;
						cropHeight = maxHeight;
					}
				} else {
					var ratio = width/maxWidth;
					if (ratio <= height/maxHeight) {
						ratio = height/maxHeight;
					}

					width = Math.round(iw/ratio);
					height = Math.round(ih/ratio);
				}
			}
		}

		var base64ResizedImageSrc;
		var canvas = document.createElement('canvas');
		var ctx = canvas.getContext('2d');
		canvas.style.imageOrientation = 'none';
		ctx.save();

		// exifOrientationからcanvasの回転・反転
		transformCoordinate(canvas, width, height, exifOrientation);

		// iPhoneのサブサンプリング問題の回避
		// see http://d.hatena.ne.jp/shinichitomita/20120927/1348726674
		var subsampled = detectSubsampling(img);
		if (subsampled) {
			iw /= 2;
			ih /= 2;
		}

		// exifOrientation利かせながらタイルレンダリング
		var d = 1024; // size of tiling canvas
		var tmpCanvas = document.createElement('canvas');
		tmpCanvas.width = tmpCanvas.height = d;
		tmpCanvas.style.imageOrientation = 'none';
		var tmpCtx = tmpCanvas.getContext('2d');
		var vertSquashRatio = detectVerticalSquash(img, iw, ih);
		var dw = Math.ceil(d * width / iw);
		var dh = Math.ceil(d * height / ih / vertSquashRatio);
		var sy = 0;
		var dy = 0;
		while (sy < ih) {
			var sx = 0;
			var dx = 0;
			while (sx < iw) {
				tmpCtx.clearRect(0, 0, d, d);
				tmpCtx.drawImage(img, -sx, -sy);
				// 何度もImageDataオブジェクトとCanvasの変換を行ってるけど、Orientation関連で仕方ない。本当はputImageDataであれば良いけどOrientation効かない
				var imageData = tmpCtx.getImageData(0, 0, d, d);
				var resampled = resample_hermite(imageData, d, d, dw, dh);
				ctx.drawImage(resampled, 0, 0, dw, dh, dx, dy, dw, dh);
				sx += d;
				dx += dw;
			}
			sy += d;
			dy += dh;
		}
		ctx.restore();

		// 切り抜き
		if (cropWidth && cropHeight) {
			var offsetX = Math.floor(canvas.width - cropWidth) / 2;
			var offsetY = Math.floor(canvas.height - cropHeight) / 2;

			// リサイズ後のcanvasから切り抜きしてtmpCanvasに張り付け
			var imageData = ctx.getImageData(offsetX, offsetY, cropWidth, cropHeight);
			tmpCanvas.width = cropWidth;
			tmpCanvas.height = cropHeight;
			tmpCtx.putImageData(imageData, 0, 0);

			canvas = ctx = null;

			// 切り抜き後のbase64形式の画像データ取得
			base64ResizedImageSrc = tmpCtx.canvas.toDataURL('image/jpeg', 0.9);

			tmpCanvas = tmpCtx = null;
		} else {
			tmpCanvas = tmpCtx = null;

			// リサイズ後のbase64形式の画像データ取得
			base64ResizedImageSrc = ctx.canvas.toDataURL('image/jpeg', 0.9);

			canvas = ctx = null;
		}

		// リサイズ後の画像プレビュー表示
		// inner2の背景画像にセット
		var $drop = $(drop);
		$drop.addClass('has-upload-preview');
		$drop.find('.p-membership-form__image-upload__drop-inner2').css('background-image', 'url("' + base64ResizedImageSrc + '")');

		// input[type="file"]を空に
		if (args.fileToEmpty) {
			if ($drop.is('.p-membership-form__image-upload-tiny__drop')) {
				$drop.parent().find(':file').val('');
			} else {
				$drop.find(':file').val('');
			}
		}

		// hermite filterかけてジャギーを削除する
		function resample_hermite(img, W, H, W2, H2){
			var canvas = document.createElement('canvas');
			canvas.width = W2;
			canvas.height = H2;
			canvas.style.imageOrientation = 'none';
			var ctx = canvas.getContext('2d');
			var img2 = ctx.createImageData(W2, H2);
			var data = img.data;
			var data2 = img2.data;
			var ratio_w = W / W2;
			var ratio_h = H / H2;
			var ratio_w_half = Math.ceil(ratio_w/2);
			var ratio_h_half = Math.ceil(ratio_h/2);
			for(var j = 0; j < H2; j++){
				for(var i = 0; i < W2; i++){
					var x2 = (i + j*W2) * 4;
					var weight = 0;
					var weights = 0;
					var gx_r = 0, gx_g = 0, gx_b = 0, gx_a = 0;
					var center_y = (j + 0.5) * ratio_h;
					for(var yy = Math.floor(j * ratio_h); yy < (j + 1) * ratio_h; yy++){
						var dy = Math.abs(center_y - (yy + 0.5)) / ratio_h_half;
						var center_x = (i + 0.5) * ratio_w;
						var w0 = dy*dy;
						for(var xx = Math.floor(i * ratio_w); xx < (i + 1) * ratio_w; xx++){
							var dx = Math.abs(center_x - (xx + 0.5)) / ratio_w_half;
							var w = Math.sqrt(w0 + dx*dx);
							if(w >= -1 && w <= 1){
								weight = 2 * w*w*w - 3*w*w + 1;
								if(weight > 0){
									dx = 4*(xx + yy*W);
									gx_r += weight * data[dx];
									gx_g += weight * data[dx + 1];
									gx_b += weight * data[dx + 2];
									gx_a += weight * data[dx + 3];
									weights += weight;
								}
							}
						}
					}
					data2[x2]		 = gx_r / weights;
					data2[x2 + 1] = gx_g / weights;
					data2[x2 + 2] = gx_b / weights;
					data2[x2 + 3] = gx_a / weights;
				}
			}
			ctx.putImageData(img2, 0, 0);
			return canvas;
		}

		// iPhoneのサブサンプリングを検出
		function detectSubsampling(img) {
			var iw = img.naturalWidth, ih = img.naturalHeight;
			if (iw * ih > 1024 * 1024) {
				var canvas = document.createElement('canvas');
				canvas.width = canvas.height = 1;
				var ctx = canvas.getContext('2d');
				ctx.drawImage(img, -iw + 1, 0);
				return ctx.getImageData(0, 0, 1, 1).data[3] === 0;
			} else {
				return false;
			}
		}

		// iPhoneの縦画像でひしゃげて表示される問題の回避
		function detectVerticalSquash(img, iw, ih) {
			var canvas = document.createElement('canvas');
			canvas.width = 1;
			canvas.height = ih;
			var ctx = canvas.getContext('2d');
			ctx.drawImage(img, 0, 0);
			var data = ctx.getImageData(0, 0, 1, ih).data;
			var sy = 0;
			var ey = ih;
			var py = ih;
			while (py > sy) {
				var alpha = data[(py - 1) * 4 + 3];
				if (alpha === 0) {
					ey = py;
				} else {
					sy = py;
				}
				py = (ey + sy) >> 1;
			}
			var ratio = (py / ih);
			return (ratio===0) ? 1 : ratio;
		}

		// exifOrientationからcanvasの回転・反転
		function transformCoordinate(canvas, width, height, exifOrientation) {
			if (exifOrientation > 4 && exifOrientation < 9 ) {
				canvas.width = height;
				canvas.height = width;
			} else {
				canvas.width = width;
				canvas.height = height;
			}
			var ctx = canvas.getContext('2d');
			switch (exifOrientation) {
				case 2:
					// horizontal flip
					ctx.translate(width, 0);
					ctx.scale(-1, 1);
					break;
				case 3:
					// 180 rotate left
					ctx.translate(width, height);
					ctx.rotate(Math.PI);
					break;
				case 4:
					// vertical flip
					ctx.translate(0, height);
					ctx.scale(1, -1);
					break;
				case 5:
					// vertical flip + 90 rotate right
					ctx.rotate(0.5 * Math.PI);
					ctx.scale(1, -1);
					break;
				case 6:
					// 90 rotate right
					ctx.rotate(0.5 * Math.PI);
					ctx.translate(0, -height);
					break;
				case 7:
					// horizontal flip + 90 rotate right
					ctx.rotate(0.5 * Math.PI);
					ctx.translate(width, -height);
					ctx.scale(-1, 1);
					break;
				case 8:
					// 90 rotate left
					ctx.rotate(-0.5 * Math.PI);
					ctx.translate(-width, 0);
					break;
				default:
					break;
			}
		}

		// rotateの追加回転を反映したexifOrientationを計算
		function getAddRotatedExifOrientation(exifOrientation, rotate) {
			if (rotate) {
				rotate = (rotate + 360) % 360;

				switch (exifOrientation) {
					// horizontal flip
					case 2:
						if (rotate === 90) {
							exifOrientation = 7;
						} else if (rotate === 180) {
							exifOrientation = 4;
						} else if (rotate === 270) {
							exifOrientation = 5;
						}
						break;

					// 180 rotate left
					case 3:
						if (rotate === 90) {
							exifOrientation = 8;
						} else if (rotate === 180) {
							exifOrientation = 1;
						} else if (rotate === 270) {
							exifOrientation = 6;
						}
						break;

					// vertical flip
					case 4:
						if (rotate === 90) {
							exifOrientation = 5;
						} else if (rotate === 180) {
							exifOrientation = 2;
						} else if (rotate === 270) {
							exifOrientation = 7;
						}
						break;

					// vertical flip + 90 rotate right
					case 5:
						if (rotate === 90) {
							exifOrientation = 2;
						} else if (rotate === 180) {
							exifOrientation = 7;
						} else if (rotate === 270) {
							exifOrientation = 4;
						}
						break;

					// 90 rotate right
					case 6:
						if (rotate === 90) {
							exifOrientation = 3;
						} else if (rotate === 180) {
							exifOrientation = 8;
						} else if (rotate === 270) {
							exifOrientation = 1;
						}
						break;

					// horizontal flip + 90 rotate right
					case 7:
						if (rotate === 90) {
							exifOrientation = 4;
						} else if (rotate === 180) {
							exifOrientation = 5;
						} else if (rotate === 270) {
							exifOrientation = 2;
						}
						break;

					// 90 rotate left
					case 8:
						if (rotate === 90) {
							exifOrientation = 1;
						} else if (rotate === 180) {
							exifOrientation = 6;
						} else if (rotate === 270) {
							exifOrientation = 3;
						}
						break;

					default:
						if (rotate === 90) {
							exifOrientation = 6;
						} else if (rotate === 180) {
							exifOrientation = 3;
						} else if (rotate === 270) {
							exifOrientation = 8;
						}
						break;
				}
			}

			return exifOrientation;
		}
	};

	/**
	 * 画像プレビュー
	 * 参考 https://egashira.jp/image-resize-before-upload
	 */
	var uploadImagePreview = function($drop, file) {
		$drop = $($drop);

		if (!$drop.length || !file || !file.type.match(/^image\/(png|jpeg|gif)$/)) return;

		// 削除フラグを0に
		$drop.find('.p-membership-form__image-upload__delete-hidden').val(0);

		// 変数初期化
		var drop = $drop.get(0);
		drop.previewVars = {};
		drop.previewVars.reader = new FileReader();
		drop.previewVars.img = new Image();
		drop.previewVars.img.classList.add('img1');
		drop.previewVars.img.style.imageOrientation = 'none';
		drop.previewVars.exifOrientation = 0;
		drop.previewVars.rotate = 0;

		// 縮小する画像のサイズ ブログ・写真の投稿・編集場合は後で差し替わります
		drop.previewVars.maxWidth = parseInt($drop.attr('data-max-width'), 10) || 850;
		drop.previewVars.maxHeight = parseInt($drop.attr('data-max-height'), 10) || 0;
		drop.previewVars.maxCrop = $drop.attr('data-max-crop') || 0;

		drop.previewVars.reader.onload = function(e) {
			var data = this.result;

			drop.previewVars.img.onload = function() {
				// 回転・縮小済みのプレビュー表示
				setImagePreview({
					drop: drop,
					addRotate: false,
					fileToEmpty: true
				});
			}

			// JPEGの場合には、EXIFからexifOrientation（回転）情報を取得
			// コールバックを使うためsrcセットの開始位置に注意
			// Canvasに画像を読み込んだ時にJPEGのEXIFからOrientationのみが自動反映される場合はOrientation無し扱いで問題なし
			if (data.split(',')[0].match('jpeg') && !isCanvasAutoExifOrientation) {
				getExifOrientation(file, function(ret) {
					if (ret > 0) {
						drop.previewVars.exifOrientation = ret;
					}
					drop.previewVars.img.src = data;
				});
			} else {
				drop.previewVars.img.src = data;
			}
		}
		drop.previewVars.reader.readAsDataURL(file);

		// JPEGのEXIFからOrientationのみを取得する
		// 参考 http://stackoverflow.com/a/32490603
		function getExifOrientation(file, callback) {
			var reader2 = new FileReader();

			reader2.onload = function(event) {
				var view = new DataView(event.target.result);

				if (view.getUint16(0, false) != 0xFFD8) return callback(-2);

				var length = view.byteLength,
					offset = 2;

				while (offset < length) {
					var marker = view.getUint16(offset, false);
					offset += 2;

					if (marker == 0xFFE1) {
						if (view.getUint32(offset += 2, false) != 0x45786966) {
							return callback(-1);
						}
						var little = view.getUint16(offset += 6, false) == 0x4949;
						offset += view.getUint32(offset + 4, little);
						var tags = view.getUint16(offset, little);
						offset += 2;

						for (var i = 0; i < tags; i++) {
							if (view.getUint16(offset + (i * 12), little) == 0x0112) {
								return callback(view.getUint16(offset + (i * 12) + 8, little));
							}
						}
					} else if ((marker & 0xFF00) != 0xFF00) {
						break;
					} else {
						offset += view.getUint16(offset, false);
					}
				}

				return callback(-1);
			};

			reader2.readAsArrayBuffer(file.slice(0, 64 * 1024));
		};
	};

	/**
	 * 画像選択時にプレビュー
	 */
	$(document).on('change', '.p-membership-form__image-upload__drop :file', function(){
		var $drop = $(this).closest('.p-membership-form__image-upload__drop');
		var $inner = $drop.find('.p-membership-form__image-upload__drop-inner');

		if (this.files.length > 0) {
			if(!this.files[0].type.match(/^image\/(png|jpeg|gif)$/)) {
				showModalAlert(TCD_MEMBERSHIP_UPLOAD.not_image_file);
				this.value = '';
				return false;
			}

			uploadImagePreview($drop, this.files[0]);
		}
	});
	// リロード対策
	$('.p-membership-form__image-upload__drop :file').trigger('change');

	/**
	 * ドラッグアンドドロップアップロード
	 * 子要素がある場合の不具合対策参考 https://qiita.com/sounisi5011/items/dc4878d3e8b38101cf0b
	 */
	var dragEnterFlag = false;
	$(document).on('dragenter', 'body', function(event) {
		event.preventDefault();
		event.stopPropagation();
		dragEnterFlag = true;
	});
	$(document).on('dragover', 'body', function(event) {
		event.preventDefault();
		event.stopPropagation();
		dragEnterFlag = false;
		$('body').addClass('is-drag-over');
	});
	$(document).on('dragleave', 'body', function(event) {
		event.preventDefault();
		event.stopPropagation();
		if (dragEnterFlag) {
			dragEnterFlag = false;
		} else {
			$('body').removeClass('is-drag-over');
		}
	});
	$(document).on('drop', 'body', function(event) {
		event.preventDefault();
		event.stopPropagation();
		dragEnterFlag = false;
		$('body').removeClass('is-drag-over');
	});
	$(document).on('dragover', '.p-membership-form__image-upload__drop, .p-membership-form__image-upload-tiny__drop', function(event) {
		event.preventDefault();
		event.stopPropagation();
		$(this).addClass('is-drag-over');
	});
	$(document).on('dragleave', '.p-membership-form__image-upload__drop, .p-membership-form__image-upload-tiny__drop', function(event) {
		event.preventDefault();
		event.stopPropagation();
		$(this).removeClass('is-drag-over');
	});
	$(document).on('drop', '.p-membership-form__image-upload__drop, .p-membership-form__image-upload-tiny__drop', function(event) {
		event.preventDefault();
		event.stopPropagation();
		dragEnterFlag = false;
		$('body').removeClass('is-drag-over');
		$('.p-membership-form__image-upload__drop, .p-membership-form__image-upload-tiny__drop').removeClass('is-drag-over');

		var files = event.originalEvent.dataTransfer.files[0];
		var file = event.originalEvent.dataTransfer.files[0];

		var $drop;
		if ($(this).is('.p-membership-form__image-upload-tiny__drop')) {
			$drop = $(this).parent().find('.p-membership-form__image-upload__drop');
		} else {
			$drop = $(this);
		}

		if (files.length > 1) {
			showModalAlert(TCD_MEMBERSHIP_UPLOAD.drop_one_file)
			return false;
		}

		if (!file.type.match(/^image\/(png|jpeg|gif)$/)) {
			showModalAlert(TCD_MEMBERSHIP_UPLOAD.drop_not_image_file)
			return false;
		}

		uploadImagePreview($drop, event.originalEvent.dataTransfer.files[0]);
	});

	/**
	 * ブログ投稿・編集のリピーター
	 */
	if ($('.p-membership-form__repeaters .p-membership-form__repeater').length) {
		// 行表示
		$('.p-membership-form__repeater-add-button').click(function(){
			$('.p-membership-form__repeaters .p-membership-form__repeater:hidden:not(.is-repeater-hidden)').first().slideDown(300);
			if (!$('.p-membership-form__repeaters .p-membership-form__repeater:hidden').length) {
				$('.p-membership-form__repeater-add').hide();
			}
			return false;
		});
		// 行削除
		$('.p-membership-form__repeater-delete-button').click(function(){
			var $cl = $(this).closest('.p-membership-form__repeater');
			$cl.slideUp(300, function(){
				$cl.remove();
			});
			return false;
		});
	}

	/**
	 * モーダルアラート表示
	 */
	var showModalAlert = function(msg) {
		if (!msg) return false;

		var $modalAlert = $('#js-modal-alert');
		if (!$modalAlert.length) {
			var html = '<div id="js-modal-alert" class="p-modal p-modal--alert">';
			html += '<div class="p-modal__contents">';
			html += '<div class="p-modal__contents__inner">';
			html += '<div class="p-modal__body p-body"></div>';
			html += '<button class="p-modal__close">&#xe91a;</button></div>';
			html += '</div>';
			html += '</div>';
			html += '</div>';
			$modalAlert = $(html).appendTo('body');
		}

		$('.p-modal.is-active').removeClass('is-active');
		$modalAlert.find('.p-modal__body').html(msg);
		$modalAlert.addClass('is-active');
	};

});
