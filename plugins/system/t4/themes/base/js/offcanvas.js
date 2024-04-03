/* rechange name offcanvas before load bs5 offvancas */
(function (w, $) {
	"use strict";

	const pluginName = "T4Offcanvas",
		initSelector = ".js-" + pluginName;

	$.fn[pluginName] = function (options) {
		return this.each(function () {
			new w.componentNamespace.Offcanvas(this, options).init();
		});
	};
	// auto-init on enhance (which is called on domready)
	$(w.document).on("enhance", function (e) {
		$($(e.target).is(initSelector) && e.target).add(initSelector, e.target).filter(initSelector)[pluginName]();
	});

})(this, jQuery);

jQuery(document).ready($ => {
	const $offcanvas = $('.t4-offcanvas');

	$('.t4-wrapper').addClass('c-offcanvas-content-wrap');

	if ($('#triggerButton').length) {
		$offcanvas.T4Offcanvas({
			triggerButton: '#triggerButton',
			onOpen: function () {
				$('#triggerButton').addClass('active');
				bodyScrollLock.disableBodyScroll('.t4-off-canvas-body');
			},
			onClose: function () {
				$('#triggerButton').removeClass('active');
				setTimeout(function () {
					bodyScrollLock.enableBodyScroll('.t4-off-canvas-body');
				}, 300);
			}
		});
		setTimeout(() => {
			$offcanvas.show();
		}, 300);
	} else {
		$offcanvas.hide();
	}

	$offcanvas.find('[data-bs-toggle]').removeAttr('data-bs-toggle', false);
	$offcanvas.find('[data-toggle]').removeAttr('data-toggle', false);

	$offcanvas.find('[data-effect="drill"] ul.dropdown-menu, [data-effect="def"] ul.dropdown-menu').each((i, el) => {
		const $el = $(el);
		const label = $el.prev().text();

		$el.prepend("<span class='sub-menu-back'><i class='fas fa-chevron-left'></i>" + label + "</span>");
		$el.before('<span class="sub-menu-toggle btn-toggle"></span>');
	});

	$offcanvas.find('[data-effect="drill"] nav.navbar').each((i, el) => {
		initDrilldownMenu($(el));
	});

	$offcanvas.find('[data-effect="def"] nav.navbar').each((i, el) => {
		initAccordionMenu($(el));
	});

	function initAccordionMenu($container) {
		const toggleSelector = '.sub-menu-toggle, .separator.dropdown-toggle, .nav-header.dropdown-toggle';

		$container.find(toggleSelector).on('click', e => {
			const $el = $(e.currentTarget);
			const $dropdown = $el.siblings('.dropdown-menu');

			$dropdown.slideToggle(300);
		})
	}

	function initDrilldownMenu($container) {
		$container.addClass('drilldown-effect');
		$container.css({
			overflow: 'unset',
			transition: 'transform 300ms, height 300ms',
		});

		const openerSelector = '.sub-menu-toggle, .separator.dropdown-toggle, .nav-header.dropdown-toggle';

		$container.find(openerSelector).on('click', e => {
			const $el = $(e.currentTarget);
			const $dropdown = $el.siblings('.dropdown-menu');
			const $navItem = $el.parent();
			const $navbar = $navItem.parent();
			const level = +$navItem.data('level') || 0;

			$dropdown.css('display', 'block');

			if (!$container[0].style.height) {
				$container.css('height', $navbar.height() + 'px');
			}

			setTimeout(() => {
				$container.css('height', $dropdown.height() + 'px');
				$container.css('transform', `translateX(-${level * 100}%)`);
			});
		});

		$container.find('.sub-menu-back').on('click', e => {
			const $el = $(e.currentTarget);
			const $dropdown = $el.parent();
			const currentlevel = $el.closest('li.nav-item').data('level') || 0;
			const prevLevel = currentlevel ? currentlevel - 1 : 0;

			$container.one('transitionend', () => {
				$dropdown.css('display', '');
			})

			setTimeout(() => {
				const $navItem = $el.closest('.nav-item');
				const $prevNav = $navItem.parent();

				$container.css('height', $prevNav.height() + 'px');
				$container.css('transform', `translateX(-${prevLevel * 100}%)`);
			});
		});
	}
});