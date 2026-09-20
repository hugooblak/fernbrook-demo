/* Fernbrook Dog Academy Leads, on every page (about 1 KB):
 * 1. Remembers where the visitor came from, so a lead that starts on a blog post still shows its ad campaign.
 * 2. Click events for phone links and booking buttons (dataLayer only).
 * 3. Tucks the sticky phone bar away while an enquiry form is on screen. */

/* 1. Attribution: the first landing page and campaign tags of this visit. A new campaign click starts over. */
(function () {
	var KEY = 'fbl_attribution', saved = null, params = new URLSearchParams(window.location.search);
	try { saved = JSON.parse(sessionStorage.getItem(KEY) || 'null'); } catch (e) {}
	var campaign = ['utm_source', 'gclid', 'fbclid'].some(function (k) { return params.get(k); });
	if (!saved || campaign) {
		saved = { landing_page: window.location.href.split('#')[0], referrer: document.referrer || '' };
		['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'].forEach(function (k) {
			if (params.get(k)) saved[k] = params.get(k).slice(0, 200);
		});
		try { sessionStorage.setItem(KEY, JSON.stringify(saved)); } catch (e) {}
	}
	window.fblAttribution = saved;
})();

/* 2. Clicks. */
(function () {
	document.addEventListener('click', function (e) {
		var a = e.target.closest && e.target.closest('a');
		if (!a) return;
		var where = a.closest('.fb-mobile-cta') ? 'mobile_bar' : a.closest('header') ? 'header' : a.closest('footer') ? 'footer' : 'content';
		var push = function (o) { (window.dataLayer = window.dataLayer || []).push(o); };
		if (a.protocol === 'tel:') push({ event: 'fbl_click_call', location: where });
		else if (a.closest('.fb-track-cta')) push({ event: 'fbl_click_cta', location: where, text: a.textContent.trim() });
	});
})();

/* 3. While an enquiry form is on screen, tuck away the sticky phone bar so it doesn't cover or compete with the form. */
(function () {
	if (!('IntersectionObserver' in window)) return;
	var forms = document.querySelectorAll('.fbl-form');
	if (!forms.length) return;
	var visible = new Set();
	var io = new IntersectionObserver(function (entries) {
		entries.forEach(function (e) { if (e.isIntersecting) visible.add(e.target); else visible.delete(e.target); });
		document.documentElement.classList.toggle('fbl-form-in-view', visible.size > 0);
	}, { threshold: 0.25 });
	forms.forEach(function (f) { io.observe(f); });
})();
