/**
 * A Base Layout
 * @constructor
 */
function Layout(zones) {

	/**
	 *
	 * @type {Boolean}
	 */
	this.rendered = false;

	/**
	 *
	 * @type {Array, <Zone>}
	 */
	this.zones = zones;

	/**
	 * @private
	 * @type {String}
	 */
	this.id = 'layout_' + Core.UUID();

	/**
	 * @public
	 * @return {String}
	 */
	this.getId = function () {
		return this.id;
	};

	/**
	 *
	 * @return {Boolean}
	 */
	this.isRendered = function () {
		return this.rendered;
	};

	/**
	 * @public
	 */
	this.destroy = function () {
		if (this.container) {
			$(this.container).remove();
		}
	};

	this.show = function () {
		$(this.container).css('left', '0px');
	};

	this.hide = function () {
		$(this.container).css('left', '-2000px');
	};

}

Layout.prototype.render = function () {};
Layout.prototype.destroy = function () {};
Layout.prototype.show = function () {};
Layout.prototype.hide = function () {};

/**
 * A full screen Layout
 * @constructor
 * @param zone {Zone}
 * @extends {Layout}
 */
function FullscreenLayout (zone) {

	this.zone = zone;

	Layout.apply( this, arguments );

	/**
	 * @public
	 */
	this.render = function () {
		if (!this.isRendered()) {
			this.container = $('<div/>').attr('id', this.getId()).css({'position':'relative', 'top':'0px', 'left':'-2000px', 'height':Canvas.getHeight(), 'width':Canvas.getWidth()});
			$('#' + Canvas.getId()).append(this.container);
			this.rendered = true;
		}
		if (!this.zone.isRendered()) {
			this.zone.setPosition(0,0);
			this.zone.setSize(Canvas.getWidth(), Canvas.getHeight());
			this.zone.setLayout(this);
			this.zone.render();
		}
	};

	this.destroy = function () {
		this.zone.destroy();
		if (this.isRendered()) {
			$(this.container).html('');
			$(this.container).remove();
			this.container = undefined;
			this.rendered = false;
		}

	};

	this.play = function () {
		this.render();
		this.zone.play();
		this.show();
	};

}

FullscreenLayout.prototype = new Layout;

