/**
 * Fullscreen search overlay: toggled by the magnifying-glass icon in the
 * header nav ([ren_search_toggle]). Opens/closes [ren_search_overlay].
 */
(function () {
	"use strict";

	document.addEventListener("DOMContentLoaded", function () {
		var toggle = document.getElementById("ren-search-toggle");
		var overlay = document.getElementById("ren-search-overlay");
		if (!toggle || !overlay) return;

		var closeBtn = overlay.querySelector(".ren-search-overlay__close");
		var input = overlay.querySelector(".ren-search-overlay__input");

		function openOverlay() {
			overlay.classList.add("is-open");
			overlay.setAttribute("aria-hidden", "false");
			toggle.setAttribute("aria-expanded", "true");
			document.body.classList.add("ren-search-open");
			// Wait for the opening transition so mobile keyboards don't fight it.
			window.setTimeout(function () {
				if (input) input.focus();
			}, 150);
		}

		function closeOverlay() {
			overlay.classList.remove("is-open");
			overlay.setAttribute("aria-hidden", "true");
			toggle.setAttribute("aria-expanded", "false");
			document.body.classList.remove("ren-search-open");
			toggle.focus();
		}

		toggle.addEventListener("click", function () {
			if (overlay.classList.contains("is-open")) {
				closeOverlay();
			} else {
				openOverlay();
			}
		});

		if (closeBtn) closeBtn.addEventListener("click", closeOverlay);

		// Click on the dark backdrop (outside the form) also closes it.
		overlay.addEventListener("click", function (e) {
			if (e.target === overlay) closeOverlay();
		});

		document.addEventListener("keydown", function (e) {
			if (e.key === "Escape" && overlay.classList.contains("is-open")) {
				closeOverlay();
			}
		});
	});
})();
