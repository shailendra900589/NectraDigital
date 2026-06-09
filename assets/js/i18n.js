(function () {
    'use strict';

    var cfg = window.NectraI18n || {};
    var COOKIE = cfg.cookieName || 'nectra_lang';
    var DEFAULT = cfg.defaultLang || 'en';
    var languages = cfg.languages || {};
    var widgetReady = false;
    var pendingLang = null;

    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : '';
    }

    function setCookie(name, value, days) {
        var maxAge = days ? '; max-age=' + (days * 86400) : '';
        document.cookie = name + '=' + encodeURIComponent(value) + '; path=/' + maxAge + '; SameSite=Lax';
    }

    function clearCookie(name) {
        var host = location.hostname.replace(/^www\./, '');
        document.cookie = name + '=; path=/; max-age=0';
        document.cookie = name + '=; path=/; domain=.' + host + '; max-age=0';
        document.cookie = name + '=; path=/; domain=.' + location.hostname + '; max-age=0';
    }

    function getSavedLang() {
        try {
            var stored = localStorage.getItem(COOKIE);
            if (stored && languages[stored]) return stored;
        } catch (e) {}
        var fromCookie = getCookie(COOKIE);
        if (fromCookie && languages[fromCookie]) return fromCookie;
        var goog = getCookie('googtrans');
        if (goog) {
            var parts = goog.split('/').filter(Boolean);
            var code = parts[parts.length - 1];
            if (languages[code]) return code;
        }
        return DEFAULT;
    }

    function saveLang(code) {
        setCookie(COOKIE, code, 365);
        try { localStorage.setItem(COOKIE, code); } catch (e) {}
    }

    function showToast(msg) {
        var el = document.getElementById('nectraLangToast');
        if (!el) {
            el = document.createElement('div');
            el.id = 'nectraLangToast';
            el.className = 'nectra-lang-toast notranslate';
            document.body.appendChild(el);
        }
        el.textContent = msg;
        el.classList.add('is-visible');
        clearTimeout(el._timer);
        el._timer = setTimeout(function () { el.classList.remove('is-visible'); }, 2600);
    }

    function getTranslateSelect() {
        return document.querySelector('select.goog-te-combo');
    }

    function triggerWidget(langCode) {
        var select = getTranslateSelect();
        if (!select) return false;
        if (langCode === DEFAULT) {
            clearCookie('googtrans');
            saveLang(DEFAULT);
            location.reload();
            return true;
        }
        select.value = langCode;
        select.dispatchEvent(new Event('change'));
        setCookie('googtrans', '/en/' + langCode, 365);
        saveLang(langCode);
        return true;
    }

    function applyLanguage(langCode, fromUser) {
        if (!languages[langCode]) langCode = DEFAULT;

        if (langCode === DEFAULT) {
            if (getCookie('googtrans')) {
                clearCookie('googtrans');
                saveLang(DEFAULT);
                location.reload();
            }
            updateActiveUI(langCode);
            return;
        }

        if (!widgetReady) {
            pendingLang = langCode;
            return;
        }

        if (triggerWidget(langCode)) {
            updateActiveUI(langCode);
            if (fromUser) {
                var native = (languages[langCode] && languages[langCode].native) || langCode;
                showToast('Language: ' + native);
            }
        }
    }

    function updateActiveUI(code) {
        var meta = languages[code] || languages[DEFAULT];
        document.querySelectorAll('.nectra-lang-current').forEach(function (el) {
            el.textContent = meta.native || meta.label || code;
        });
        document.querySelectorAll('.nectra-lang-option').forEach(function (btn) {
            var active = btn.getAttribute('data-lang') === code;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-selected', active ? 'true' : 'false');
            var check = btn.querySelector('.nectra-lang-check');
            if (active && !check) {
                check = document.createElement('i');
                check.className = 'fas fa-check nectra-lang-check';
                check.setAttribute('aria-hidden', 'true');
                btn.appendChild(check);
            } else if (!active && check) {
                check.remove();
            }
        });
        document.querySelectorAll('.nectra-lang-switcher').forEach(function (wrap) {
            wrap.setAttribute('data-current', code);
        });
    }

    function closeMenus() {
        document.querySelectorAll('.nectra-lang-switcher.is-open').forEach(function (wrap) {
            wrap.classList.remove('is-open');
            var menu = wrap.querySelector('.nectra-lang-menu');
            var toggle = wrap.querySelector('.nectra-lang-toggle');
            if (menu) menu.hidden = true;
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
        });
    }

    function toggleMenu(wrap) {
        var menu = wrap.querySelector('.nectra-lang-menu');
        var toggle = wrap.querySelector('.nectra-lang-toggle');
        var open = !wrap.classList.contains('is-open');
        closeMenus();
        if (open) {
            wrap.classList.add('is-open');
            if (menu) menu.hidden = false;
            if (toggle) toggle.setAttribute('aria-expanded', 'true');
            var search = wrap.querySelector('.nectra-lang-search');
            if (search) setTimeout(function () { search.focus(); }, 80);
        }
    }

    function filterLanguages(query) {
        query = (query || '').toLowerCase().trim();
        document.querySelectorAll('.nectra-lang-list li').forEach(function (li) {
            var btn = li.querySelector('.nectra-lang-option');
            if (!btn) return;
            var text = (btn.textContent || '').toLowerCase();
            li.classList.toggle('is-hidden', query !== '' && text.indexOf(query) === -1);
        });
    }

    function bindSwitcher(root) {
        var toggle = root.querySelector('.nectra-lang-toggle');
        var search = root.querySelector('.nectra-lang-search');

        if (toggle) {
            toggle.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleMenu(root);
            });
        }

        if (search) {
            search.addEventListener('input', function () {
                filterLanguages(search.value);
            });
        }

        root.querySelectorAll('.nectra-lang-option').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var code = btn.getAttribute('data-lang');
                closeMenus();
                applyLanguage(code, true);
            });
        });
    }

    function initSwitchers() {
        document.querySelectorAll('.nectra-lang-switcher').forEach(bindSwitcher);

        document.addEventListener('click', function () {
            closeMenus();
        });

        var floatBtn = document.getElementById('nectraLangFloatBtn');
        if (floatBtn) {
            floatBtn.addEventListener('click', function () {
                var navSwitcher = document.getElementById('nectraLangSwitcher');
                if (navSwitcher) toggleMenu(navSwitcher);
            });
        }

        updateActiveUI(getSavedLang());
    }

    window.googleTranslateElementInit = function () {
        if (!window.google || !google.translate || !google.translate.TranslateElement) {
            return;
        }
        new google.translate.TranslateElement({
            pageLanguage: 'en',
            includedLanguages: cfg.includedCodes || 'en,hi',
            autoDisplay: false,
            multilanguagePage: true,
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE
        }, 'google_translate_element');

        widgetReady = true;

        var saved = getSavedLang();
        if (pendingLang) {
            applyLanguage(pendingLang, false);
            pendingLang = null;
        } else if (saved && saved !== DEFAULT && !getCookie('googtrans')) {
            applyLanguage(saved, false);
        } else {
            updateActiveUI(saved);
        }
    };

    window.NectraTranslate = {
        setLanguage: function (code) { applyLanguage(code, true); },
        getLanguage: getSavedLang,
        translateViaApi: function (text, target) {
            if (!cfg.apiEnabled || !cfg.apiUrl) {
                return Promise.reject(new Error('API not configured'));
            }
            return fetch(cfg.apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ text: text, target: target || getSavedLang(), source: 'en' })
            }).then(function (r) { return r.json(); });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSwitchers);
    } else {
        initSwitchers();
    }

    var params = new URLSearchParams(location.search);
    var langParam = params.get('lang');
    if (langParam && languages[langParam]) {
        saveLang(langParam);
        if (langParam !== DEFAULT) {
            pendingLang = langParam;
        }
        params.delete('lang');
        var clean = location.pathname + (params.toString() ? '?' + params.toString() : '');
        history.replaceState(null, '', clean);
    }
})();
