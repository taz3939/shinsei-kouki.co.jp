(function ($) {
    'use strict';

    $(function () {
        var $dropzone = $('#topics-eyecatch-dropzone');
        var $message = $('#topics-eyecatch-message');
        var $statusWrap = $('#topics-eyecatch-status-wrap');
        var $preview = $('#topics-eyecatch-preview');

        if (!$dropzone.length || typeof shinseiKoukiTopicsEyecatch === 'undefined') {
            return;
        }
        if (parseInt($dropzone.data('post-id'), 10) === 0) {
            return;
        }

        function showMessage(text, isError) {
            $message.css('color', isError ? '#b32d2e' : '#00a32a').text(text || '').show();
        }

        function setEyecatchSet(isSet, previewUrl) {
            if (!$statusWrap.length) return;
            if (isSet) {
                $statusWrap.removeClass('topics-eyecatch-empty').addClass('topics-eyecatch-set');
                $statusWrap.find('.topics-eyecatch-status').text('✓ 設定済み');
                if ($preview.length && previewUrl) {
                    $preview.attr('src', previewUrl).show();
                }
            } else {
                $statusWrap.removeClass('topics-eyecatch-set').addClass('topics-eyecatch-empty');
                $statusWrap.find('.topics-eyecatch-status').text('未設定');
                if ($preview.length) {
                    $preview.attr('src', '').hide();
                }
            }
        }

        function doUpload(file) {
            showMessage('アップロード中…', false);
            var formData = new FormData();
            formData.append('action', 'shinsei_kouki_topics_upload_eyecatch');
            formData.append('nonce', shinseiKoukiTopicsEyecatch.nonce);
            formData.append('post_id', shinseiKoukiTopicsEyecatch.postId);
            formData.append('file', file);
            $.ajax({
                url: shinseiKoukiTopicsEyecatch.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res.success && res.data) {
                        setEyecatchSet(true, res.data.previewUrl);
                        showMessage(res.data.message || '設定しました。', false);
                    } else {
                        showMessage(res.data && res.data.message ? res.data.message : 'アップロードに失敗しました。', true);
                    }
                },
                error: function (xhr) {
                    var msg = 'アップロードに失敗しました。';
                    if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                        msg = xhr.responseJSON.data.message;
                    }
                    showMessage(msg, true);
                }
            });
        }

        $dropzone.on('dragover dragenter', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).css('border-color', '#2271b1').css('background', '#f0f6fc');
        }).on('dragleave dragend drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).css('border-color', '').css('background', '');
        }).on('drop', function (e) {
            var files = e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files;
            if (!files || !files.length) return;
            var file = files[0];
            if (file.type.indexOf('image/') !== 0) {
                showMessage('画像ファイル（jpg / png / gif / webp）をドロップしてください。', true);
                return;
            }
            doUpload(file);
        });
    });

})(jQuery);
