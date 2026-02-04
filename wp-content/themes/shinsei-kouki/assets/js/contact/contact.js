/**
 * お問い合わせフォーム：個人情報チェックが入るまで送信ボタンを非活性にする
 */
(function($) {
	'use strict';

	function initPrivacySubmitState() {
		var $section = $('#contactFormSection');
		if (!$section.length) return;

		// 個人情報同意チェックボックス（value で特定）
		var $checkbox = $section.find('input[type="checkbox"][value="個人情報保護方針に同意する"]');
		var $submit = $section.find('.submitArea input[name="submitConfirm"]');

		if (!$checkbox.length || !$submit.length) return;

		function updateSubmitState() {
			$submit.prop('disabled', !$checkbox.prop('checked'));
		}

		updateSubmitState(); // 初期状態
		$checkbox.on('change', updateSubmitState);
	}

	$(function() {
		initPrivacySubmitState();
	});
})(jQuery);
