/**
 * Reactions Work plugin for Craft CMS
 *
 * ReactionsWorkField Field JS
 *
 * @author    info@24hoursmedia.com
 * @copyright Copyright (c) 2020 info@24hoursmedia.com
 * @link      https://en.24hoursmedia.com
 * @package   ReactionsWork
 * @since     1.0.0ReactionsWorkReactionsWorkField
 */

 ;(function ( $, window, document, undefined ) {

    var pluginName = "ReactionsWorkReactionsWorkField",
        defaults = {
        };

    // Plugin constructor
    function Plugin( element, options ) {
        this.element = element;

        this.options = $.extend( {}, defaults, options) ;

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {

        init: function(id) {
            var _this = this;

            $(function () {

/* -- _this.options gives us access to the $jsonVars that our FieldType passed down to us */

            });
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new Plugin( this, options ));
            }
        });
    };

})( jQuery, window, document );
