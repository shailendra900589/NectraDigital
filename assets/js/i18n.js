(function () {
    'use strict';

    var cfg = window.NectraI18n || {};
    var COOKIE = cfg.cookieName || 'nectra_lang';
    var DEFAULT = cfg.defaultLang || 'en';
    var languages = cfg.languages || {};
    var cookieDomain = cfg.cookieDomain || '';

    function googleCodeFor(langCode) {
        var meta = languages[langCode];
        return (meta && meta.google) ? meta.google : langCode;
    }

    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : '';
    }

    function setRawCookie(name, value, days, domain) {
        var maxAge = days ? '; max-age=' + (days * 86400) : '';
        var dom = domain ? '; domain=' + domain : '';
        document.cookie = name + '=' + encodeURIComponent(value) + '; path=/' + maxAge + dom + '; SameSite=Lax';
    }

    function clearAllTranslationCookies() {
        var root = location.hostname.replace(/^www\./, '');
        var domains = ['', '.' + root, location.hostname, cookieDomain].filter(Boolean);
        var uniqueDomains = domains.filter(function (d, i) { return domains.indexOf(d) === i; });

        ['googtrans', COOKIE].forEach(function (name) {
            uniqueDomains.forEach(function (dom) {
                var domPart = dom ? '; domain=' + dom : '';
                document.cookie = name + '=; path=/' + domPart + '; max-age=0';
            });
        });
    }

    function setGoogtransCookie(langCode) {
        if (langCode === DEFAULT) {
            clearAllTranslationCookies();
            return;
        }
        var gCode = googleCodeFor(langCode);
        var val = '/en/' + gCode;
        setRawCookie('googtrans', val, 365, '');
        if (cookieDomain) {
            setRawCookie('googtrans', val, 365, cookieDomain);
        }
        var root = location.hostname.replace(/^www\./, '');
        setRawCookie('googtrans', val, 365, '.' + root);
    }

    function saveLang(code) {
        setRawCookie(COOKIE, code, 365, '');
        if (cookieDomain) {
            setRawCookie(COOKIE, code, 365, cookieDomain);
        }
        try { localStorage.setItem(COOKIE, code); } catch (e) {}
    }

    function getSavedLang() {
        var params = new URLSearchParams(location.search);
        var fromUrl = params.get('lang');
        if (fromUrl && languages[fromUrl]) return fromUrl;

        try {
            var stored = localStorage.getItem(COOKIE);
            if (stored && languages[stored]) return stored;
        } catch (e) {}

        var fromCookie = getCookie(COOKIE);
        if (fromCookie && languages[fromCookie]) return fromCookie;

        var goog = getCookie('googtrans');
        if (goog) {
            var parts = goog.split('/').filter(Boolean);
            var gCode = parts[parts.length - 1];
            for (var key in languages) {
                if (languages.hasOwnProperty(key) && googleCodeFor(key) === gCode) {
                    return key;
                }
            }
        }

        return cfg.currentLang || DEFAULT;
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

    function buildLangUrl(code) {
        var url = new URL(location.href);
        if (code === DEFAULT) {
            url.searchParams.delete('lang');
        } else {
            url.searchParams.set('lang', code);
        }
        return url.pathname + url.search + url.hash;
    }

    function navigateToLanguage(code, fromUser) {
        if (!languages[code]) code = DEFAULT;

        clearAllTranslationCookies();
        saveLang(code);

        if (code === DEFAULT) {
            if (fromUser) showToast('English');
            location.assign(buildLangUrl(DEFAULT));
            return;
        }

        setGoogtransCookie(code);

        if (fromUser) {
            var native = (languages[code] && languages[code].native) || code;
            showToast(native);
        }

        location.assign(buildLangUrl(code));
    }

    function getTranslateSelect() {
        return document.querySelector('select.goog-te-combo');
    }

    function triggerWidget(langCode) {
        var gCode = googleCodeFor(langCode);
        var select = getTranslateSelect();
        if (!select) return false;

        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].value === gCode) {
                select.selectedIndex = i;
                select.dispatchEvent(new Event('change'));
                document.documentElement.classList.add('nectra-translated');
                document.body.classList.add('nectra-translated', 'nectra-lang-' + langCode);
                return true;
            }
        }
        return false;
    }

    function ensureFullPageTranslation(langCode, attempt) {
        if (langCode === DEFAULT) return;

        if (triggerWidget(langCode)) {
            observeDynamicContent(langCode);
            return;
        }

        if (attempt < 40) {
            setTimeout(function () { ensureFullPageTranslation(langCode, attempt + 1); }, 150);
        }
    }

    var observerStarted = false;
    function observeDynamicContent(langCode) {
        if (observerStarted || langCode === DEFAULT) return;
        observerStarted = true;

        var debounce;
        var observer = new MutationObserver(function () {
            clearTimeout(debounce);
            debounce = setTimeout(function () { triggerWidget(langCode); }, 500);
        });
        observer.observe(document.body, { childList: true, subtree: true });
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
            search.addEventListener('input', function () { filterLanguages(search.value); });
            search.addEventListener('click', function (e) { e.stopPropagation(); });
        }

        root.querySelectorAll('.nectra-lang-option').forEach(function (btn) {
            btn.addEventListener('click', function () {
                closeMenus();
                navigateToLanguage(btn.getAttribute('data-lang'), true);
            });
        });
    }

    function initSwitchers() {
        document.querySelectorAll('.nectra-lang-switcher').forEach(bindSwitcher);
        document.addEventListener('click', closeMenus);

        var floatBtn = document.getElementById('nectraLangFloatBtn');
        if (floatBtn) {
            floatBtn.addEventListener('click', function (e) {
                e.stopPropagation();
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
            includedLanguages: cfg.includedCodes,
            autoDisplay: false,
            multilanguagePage: true,
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE
        }, 'google_translate_element');

        var saved = getSavedLang();
        if (saved && saved !== DEFAULT) {
            ensureFullPageTranslation(saved, 0);
        }
        updateActiveUI(saved);
    };

    window.NectraTranslate = {
        setLanguage: function (code) { navigateToLanguage(code, true); },
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

    if (cfg.googtrans && cfg.currentLang && cfg.currentLang !== DEFAULT) {
        setGoogtransCookie(cfg.currentLang);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSwitchers);
    } else {
        initSwitchers();
    }
})();
