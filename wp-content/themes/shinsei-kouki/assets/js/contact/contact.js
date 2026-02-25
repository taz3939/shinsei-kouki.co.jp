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

	/**
	 * 個人情報保護方針リンクが label 内にあるため、タップがチェックボックスに取られてしまうのを防ぐ。
	 * リンクのクリックで stopPropagation し、リンクの遷移だけが行われるようにする。
	 */
	function initPrivacyPolicyLink() {
		var $section = $('#contactFormSection');
		if (!$section.length) return;

		var $link = $section.find('a[href*="privacy-policy"]');
		if (!$link.length) return;

		$link.on('click', function(e) {
			e.stopPropagation();
		});
	}

	$(function() {
		initPrivacySubmitState();
		initPrivacyPolicyLink();
	});
})(jQuery);
