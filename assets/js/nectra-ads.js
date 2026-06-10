/**
 * Nectra blog ads — single AdSense loader, visible slots, hide only when unfilled.
 */
(function () {
    'use strict';

    function revealSlot(slot) {
        slot.classList.remove('nectra-ad-slot--hidden', 'nectra-ad-slot--pending');
        slot.classList.add('nectra-ad-slot--filled');
    }

    function hideSlot(slot) {
        slot.classList.remove('nectra-ad-slot--filled');
        slot.classList.add('nectra-ad-slot--hidden');
    }

    function slotHasImage(slot) {
        var img = slot.querySelector('img.nectra-ad-img, img.sidebar-ad-img');
        if (!img) return false;
        return (img.complete && img.naturalHeight > 10) || img.offsetHeight > 10;
    }

    function pushAdsense(ins) {
        if (!ins || ins.getAttribute('data-nectra-pushed') === '1') return false;
        if (typeof window.adsbygoogle === 'undefined') return false;

        var slot = ins.closest('.nectra-ad-slot');
        if (slot) {
            slot.classList.remove('nectra-ad-slot--hidden', 'nectra-ad-slot--pending');
            slot.classList.add('nectra-ad-slot--adsense');
        }

        try {
            ins.setAttribute('data-nectra-pushed', '1');
            (window.adsbygoogle = window.adsbygoogle || []).push({});
            return true;
        } catch (e) {
            ins.removeAttribute('data-nectra-pushed');
            return false;
        }
    }

    function initAdsenseSlots() {
        document.querySelectorAll('ins.adsbygoogle[data-nectra-adsense]').forEach(pushAdsense);
    }

    function checkAdSlots() {
        document.querySelectorAll('.nectra-ad-slot').forEach(function (slot) {
            var ins = slot.querySelector('ins.adsbygoogle');

            if (ins) {
                var status = ins.getAttribute('data-ad-status');
                var h = ins.offsetHeight || 0;
                var iframe = ins.querySelector('iframe');
                if (iframe && iframe.offsetHeight > 20) h = Math.max(h, iframe.offsetHeight);

                if (status === 'filled' || h > 40) {
                    revealSlot(slot);
                } else if (status === 'unfilled') {
                    hideSlot(slot);
                }
                return;
            }

            if (slotHasImage(slot)) {
                revealSlot(slot);
            }
        });

        document.querySelectorAll('.sidebar-ad-column').forEach(function (col) {
            var hasVisible = col.querySelector('.nectra-ad-slot--filled, .nectra-ad-slot--adsense:not(.nectra-ad-slot--hidden)');
            col.style.display = hasVisible ? '' : 'none';
        });
    }

    function whenAdsenseReady(fn) {
        if (typeof window.adsbygoogle !== 'undefined') {
            fn();
            return;
        }
        var loader = document.getElementById('nectra-adsense-loader');
        if (loader) {
            loader.addEventListener('load', fn, { once: true });
        }
        setTimeout(fn, 2500);
    }

    function boot() {
        whenAdsenseReady(function () {
            initAdsenseSlots();
            checkAdSlots();
        });
    }

    document.querySelectorAll('img.nectra-ad-img, img.sidebar-ad-img').forEach(function (img) {
        img.addEventListener('load', checkAdSlots);
        img.addEventListener('error', function () {
            var slot = img.closest('.nectra-ad-slot');
            if (slot) hideSlot(slot);
        });
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    [800, 2000, 4000, 8000].forEach(function (ms) {
        setTimeout(function () {
            initAdsenseSlots();
            checkAdSlots();
        }, ms);
    });

    setTimeout(function () {
        document.querySelectorAll('.nectra-ad-slot--adsense').forEach(function (slot) {
            var ins = slot.querySelector('ins.adsbygoogle');
            if (!ins) return;
            var status = ins.getAttribute('data-ad-status');
            var h = ins.offsetHeight || 0;
            if (status === 'unfilled' || (status !== 'filled' && h < 30)) {
                hideSlot(slot);
            }
        });
        checkAdSlots();
    }, 10000);

    if (window.MutationObserver) {
        var debounce;
        var obs = new MutationObserver(function () {
            clearTimeout(debounce);
            debounce = setTimeout(checkAdSlots, 250);
        });
        document.querySelectorAll('.nectra-ad-slot').forEach(function (slot) {
            obs.observe(slot, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['data-ad-status', 'style', 'class']
            });
        });
    }
})();
