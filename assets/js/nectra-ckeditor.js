/**
 * Nectra Digital — CKEditor 5 (free Classic build) auto-init
 * Usage: <textarea class="ckeditor-full"> or <textarea class="ckeditor-basic">
 */
(function () {
    'use strict';

    var TOOLBAR_FULL = [
        'heading', '|',
        'bold', 'italic', '|',
        'link', 'bulletedList', 'numberedList', '|',
        'outdent', 'indent', '|',
        'blockQuote', 'insertTable', '|',
        'undo', 'redo'
    ];

    var TOOLBAR_BASIC = [
        'bold', 'italic', '|',
        'link', 'bulletedList', 'numberedList', '|',
        'undo', 'redo'
    ];

    function createEditor(el, toolbar) {
        if (el.dataset.ckeditorInitialized === '1') {
            return Promise.resolve();
        }
        return ClassicEditor.create(el, {
            toolbar: toolbar,
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                ]
            }
        }).then(function (editor) {
            el.dataset.ckeditorInitialized = '1';
            window.nectraCkEditors = window.nectraCkEditors || [];
            window.nectraCkEditors.push(editor);
            return editor;
        }).catch(function (err) {
            console.error('CKEditor init failed:', err);
        });
    }

    function initAll() {
        if (typeof ClassicEditor === 'undefined') {
            return;
        }
        document.querySelectorAll('textarea.ckeditor-full').forEach(function (el) {
            createEditor(el, TOOLBAR_FULL);
        });
        document.querySelectorAll('textarea.ckeditor-basic').forEach(function (el) {
            createEditor(el, TOOLBAR_BASIC);
        });
    }

    window.nectraCkeditorInit = initAll;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
