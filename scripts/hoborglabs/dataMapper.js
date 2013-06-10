;
define([
	'lib/lodash',
	'lib/bonzo',
], function(_, bonzo) {

	/**
	 * Simple Data mapping 
	 */
	function DataMapper() {
		this.mappings = {};
	}

	var applyHtml = function(el, value) {
		el.html(value);
	}
	var applyAttribute = function(attributeName) {
		return function(el, value) {
			el.attr(attributeName, value);
		};
	}

	DataMapper.prototype.addMapping = function(key, element, options) {
		var cb = options.callback;
		var type = options.type || 'html'
		if (!cb) {
			if ('html' == type) {
				cb = applyHtml;
			}
			if ('attr' == type) {
				cb = applyAttribute(options.typeAttr || 'data-missing-typeAttr');
			}
		}

		this.mappings[key] = {
			el: element,
			cb: cb,
		}
	}

	DataMapper.prototype.update = function(data) {
		_.each(this.mappings, function(mapping, key) {
			if (data[key]) {
				mapping.cb(mapping.el, data[key]);
			}
		});
	}

	return DataMapper;
});
