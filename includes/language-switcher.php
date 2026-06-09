<?php
if (!function_exists('nectra_supported_languages')) {
    require_once __DIR__ . '/i18n.php';
}
$nectra_langs = nectra_supported_languages();
$nectra_current = nectra_get_user_lang();
$nectra_current_meta = $nectra_langs[$nectra_current] ?? $nectra_langs['en'];
?>
<div class="nectra-lang-switcher notranslate" id="nectraLangSwitcher" data-current="<?php echo htmlspecialchars($nectra_current); ?>">
    <button type="button"
            class="nectra-lang-toggle"
            id="nectraLangToggle"
            aria-haspopup="listbox"
            aria-expanded="false"
            aria-label="Choose language">
        <i class="fas fa-globe" aria-hidden="true"></i>
        <span class="nectra-lang-current"><?php echo htmlspecialchars($nectra_current_meta['native']); ?></span>
        <i class="fas fa-chevron-down nectra-lang-chevron" aria-hidden="true"></i>
    </button>
    <div class="nectra-lang-menu" id="nectraLangMenu" role="listbox" aria-label="Languages" hidden>
        <div class="nectra-lang-search-wrap">
            <input type="search"
                   class="nectra-lang-search"
                   id="nectraLangSearch"
                   placeholder="Search language…"
                   autocomplete="off"
                   aria-label="Search languages">
        </div>
        <ul class="nectra-lang-list">
            <?php foreach ($nectra_langs as $code => $meta): ?>
            <li>
                <button type="button"
                        class="nectra-lang-option<?php echo $code === $nectra_current ? ' is-active' : ''; ?>"
                        role="option"
                        data-lang="<?php echo htmlspecialchars($code); ?>"
                        aria-selected="<?php echo $code === $nectra_current ? 'true' : 'false'; ?>">
                    <span class="nectra-lang-flag" aria-hidden="true"><?php echo $meta['flag']; ?></span>
                    <span class="nectra-lang-names">
                        <span class="nectra-lang-native"><?php echo htmlspecialchars($meta['native']); ?></span>
                        <span class="nectra-lang-label"><?php echo htmlspecialchars($meta['label']); ?></span>
                    </span>
                    <?php if ($code === $nectra_current): ?>
                    <i class="fas fa-check nectra-lang-check" aria-hidden="true"></i>
                    <?php endif; ?>
                </button>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<div id="google_translate_element" class="nectra-gtranslate-host" aria-hidden="true"></div>
