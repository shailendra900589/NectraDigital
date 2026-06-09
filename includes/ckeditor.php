<?php
/**
 * CKEditor 5 loader — free Classic build (CDN)
 * @param string $assetsBase e.g. '../assets' or '../../assets'
 */
function nectra_ckeditor_styles(): void
{
    ?>
<style>
.ck-editor__editable { min-height: 220px; }
.ck-editor__editable.ck-editor__editable_inline { min-height: 120px; }
.ge-admin .ck.ck-editor,
.ge-admin .ck.ck-editor__main > .ck-editor__editable,
body.admin-editor .ck.ck-editor__main > .ck-editor__editable {
    background: #1a1a22 !important;
    color: #e8e8e8 !important;
    border-color: #333 !important;
}
.ge-admin .ck.ck-toolbar,
body.admin-editor .ck.ck-toolbar {
    background: #121218 !important;
    border-color: #333 !important;
}
.ge-admin .ck.ck-button,
.ge-admin .ck.ck-dropdown__button,
body.admin-editor .ck.ck-button {
    color: #ccc !important;
}
.ge-admin .ck.ck-button:hover,
body.admin-editor .ck.ck-button:hover {
    background: #222 !important;
}
</style>
    <?php
}

function nectra_ckeditor_scripts(string $assetsBase = '../assets'): void
{
    $base = rtrim($assetsBase, '/');
    ?>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script src="<?php echo htmlspecialchars($base); ?>/js/nectra-ckeditor.js"></script>
    <?php
}
