/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referencing this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'retro\'">' + entity + '</span>' + html;
	}
	var icons = {
		'icon-reprezentuj': '&#xe90e;',
		'icon-retro-to-left': '&#xe900;',
		'icon-retro-to-right': '&#xe901;',
		'icon-retro-no-smoke': '&#xe902;',
		'icon-retro-coffe': '&#xe903;',
		'icon-retro-shower': '&#xe904;',
		'icon-retro-tv': '&#xe905;',
		'icon-retro-dryer': '&#xe906;',
		'icon-retro-air-cond': '&#xe907;',
		'icon-retro-elevator': '&#xe908;',
		'icon-retro-pool': '&#xe909;',
		'icon-retro-temp-control': '&#xe90a;',
		'icon-retro-alarm-clock': '&#xe90b;',
		'icon-retro-locker': '&#xe90c;',
		'icon-retro-wifi': '&#xe90d;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
