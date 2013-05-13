/**
 *
 * @oaram asset {VideoAsset}
 * @constructor
 */
function Zone(asset) {

	/**
	 * @private
	 * @type {VideoAsset}
	 */
	this.asset = asset;

	/**
	 * @private
	 * @type {Boolean}
	 */
	this.rendered = false;

	/**
	 * @private
	 * @define {String}
	 */
	this.id = 'zone_' + Core.UUID();

	/**
	 * @private
	 * @type {Layout}
	 */
	this.layout = undefined;

	/**
	 * @private
	 * @type {Object}
	 */
	this.container = undefined;

	/**
	 * @private
	 * @define {number}
	 */
	this.top = undefined;

	/**
	 * @private
	 * @define {number}
	 */
	this.left = undefined;

	/**
	 * @private
	 * @define {number}
	 */
	this.width = undefined;

	/**
	 * @private
	 * @define {number}
	 */
	this.height = undefined;

	/**
	 * @public
	 * @returns string
	 */
	this.getId = function () {
		return this.id;
	};

	/**
	 * @public
	 * @return {Layout}
	 */
	this.getLayout = function () {
		return this.layout;
	};

	/**
	 * @public
	 * @param layout {Layout}
	 */
	this.setLayout = function (layout) {
		this.layout = layout;
	};

	/**
	 * @public
	 * @return {Boolean}
	 */
	this.isRendered = function () {
		return this.rendered;
	};

	/**
	 * @public
	 */
	this.render = function () {
		if (!this.isRendered()) {
			$('#' + this.layout.getId()).append(this.container);
			this.rendered = true;
		}
	};

	/**
	 * @public
	 */
	this.destroy = function () {
		if (this.isRendered()) {
			$(this.container).remove();
		}
		this.rendered = false;
	};

	/**
	 * @interface
	 */
	this.play = function () {};

	/**
	 * @public
	 * @param top number
	 * @param left number
	 */
	this.setPosition = function (top, left) {
		this.top = top;
		this.left = left;
		$(this.container).css('position', 'relative');
		$(this.container).css('top', this.top + 'px');
		$(this.container).css('left', this.left+ 'px');
	};


	/**
	 * @public
	 * @param width number
	 * @param height number
	 */
	this.setSize = function (width, height) {
		this.width = width;
		this.height = height;
		$(this.container).width(this.width);
		$(this.container).height(this.height);
	};

	this.getId = function () {
		return this.id;
	};

	this.getWidth = function () {
		return this.width;
	};

	this.getHeight = function () {
		return this.height;
	};

}

/**
 * @public
 * @constructor
 */
function VideoZone() {

	Zone.apply( this, arguments );

}

/**
 * @type {Zone}
 * @param layout {Layout}
 */
VideoZone.prototype = new Zone;


/**
 * @public
 * @param {QuicktimeVideoAsset}
 * @constructor
 */
function QuicktimeVideoZone (asset) {

	VideoZone.apply( this, arguments );

	this.asset = asset;

	/**
	 * @public
	 **/
	this.render = function () {
		if (!this.isRendered()) {
			this.container = $('<div/>').attr('id', this.getId());
			$('#' + this.getLayout().getId()).append(this.container);
			if (Player.preBufferVideo) {
				$(this.container).html('<embed type="video/quicktime" onMediaComplete="Player.playNextItem()" nopauseonhide="true" autoplay="0" id="gecko_' + this.id + '" width="' + this.getWidth() + '" height="' + this.getHeight() + '" showcontrols="false"></embed>');
				var mediaPlayer = $(this.container).find('embed');
				var zone = this;
				mediaPlayer.each(function () {
					this.Open(zone.asset.getSrc());
				});
			}
			this.rendered = true;
		}
	};

	this.destroy = function () {
		if (this.isRendered()) {
			$(this.container).html('');
			$(this.container).remove();
			this.container = undefined;
		}
		this.rendered = false;
	};

	/**
	 * @public
	 **/
	this.play = function () {
		this.render();
		/*
		 * Bogus gecko-mediaplayer event handling
		 * throws an onMediaComplete *before* the
		 * media plays.
		if (Player.preBufferVideo) {
			var mediaPlayer = $(this.container).find('embed');
			mediaPlayer.each(function() {
				var thisPlayer = this;
				if (!Player.preBufferVideo) {
					setTimeout( function() {
						thisPlayer.Play();
					}, 1000);
				} else {
					this.Play();
				}
			});
		} else {
		 */
		$(this.container).html('<embed type="video/quicktime" src="' + this.asset.getSrc() + '" onMediaComplete="Player.playNextItem()" id="gecko_' + this.getId() + '" width="' + this.getWidth() + '" height="' + this.getHeight() + '" showcontrols="false"></embed>');
//		}
	};
}

QuicktimeVideoZone.prototype = new VideoZone;