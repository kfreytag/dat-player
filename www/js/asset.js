/**
 *
 * @interface
 */
function Asset() {}
Asset.prototype.render = function () {};

/**
 *
 * @param {String}
 * @implements {Asset}
 * @constructor
 */
function VideoAsset(src) {

	/**
	 * @private
	 * @type {string}
	 */
	this.src = src;

	/**
	 * @public
	 * @return {string}
	 */
	this.getSrc = function() {
		return this.src;
	};


}

VideoAsset.prototype = new Asset;

/**
 * @extends {VideoAsset}
 * @constructor
 */
function QuicktimeVideoAsset (src) {

	VideoAsset.apply( this, arguments );

}

QuicktimeVideoAsset.prototype = new VideoAsset;