/**
 * @typedef {Object} MarkerInfoBubbleSettings
 *
 * @extends {GeolocationMapFeatureSettings}
 *
 * @property {bool} closeButton
 * @property {bool} closeOther
 * @property {String} closeButtonSrc
 * @property {String} shadowStyle
 * @property {Number} padding
 * @property {Number} borderRadius
 * @property {Number} borderWidth
 * @property {String} borderColor
 * @property {String} backgroundColor
 * @property {Number} minWidth
 * @property {Number} maxWidth
 * @property {Number} minHeight
 * @property {Number} maxHeight
 */

/**
 * @typedef {Object} GoogleInfoBubble
 * @property {Function} open
 * @property {Function} close
 *
 * @property {Boolean} enable
 * @property {Boolean} closeButton
 * @property {Boolean} closeOther
 * @property {String} closeButtonSrc
 * @property {String} shadowStyle
 * @property {Number} padding
 * @property {Number} borderRadius
 * @property {Number} borderWidth
 * @property {String} borderColor
 * @property {String} backgroundColor
 * @property {Number} minWidth
 * @property {Number} maxWidth
 * @property {Number} minHeight
 * @property {Number} maxHeight
 */

/**
 * @property {GoogleInfoBubble} GeolocationGoogleMap.infoBubble
 * @property {function({}):GoogleInfoBubble} InfoBubble
 */

/* global InfoBubble */

(function ($, Drupal) {

  'use strict';

  /**
   * Marker InfoBubble.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches common map style functionality to relevant elements.
   */
  Drupal.behaviors.geolocationMarkerInfoBubble = {
    attach: function (context, drupalSettings) {
      Drupal.geolocation.executeFeatureOnAllMaps(
        'marker_infobubble',

        /**
         * @param {GeolocationGoogleMap} map - Current map.
         * @param {MarkerInfoBubbleSettings} featureSettings - Settings for current feature.
         */
        function (map, featureSettings) {
          map.addMarkerAddedCallback(function (currentMarker) {
            var content = currentMarker.locationWrapper.find('.location-content').html();
						
						if (content.length < 1) {
              return;
            }

            google.maps.event.addListener(currentMarker, 'click', function () {
							$.each(map.mapMarkers, function(index, test) {
								test.setIcon('/themes/santacity/images/black.png');
							});
            
							currentMarker.setIcon('/themes/santacity/images/orange.png');
              if (typeof currentMarker.infoBubble === 'undefined') {
                currentMarker.infoBubble = new InfoBubble({
                  map: map.googleMap,
                  content: content,
                  shadowStyle: featureSettings.shadowStyle,
                  padding: featureSettings.padding,
                  borderRadius: featureSettings.borderRadius,
                  borderWidth: featureSettings.borderWidth,
                  borderColor: featureSettings.borderColor,

                  arrowSize: 10,
                  arrowPosition: 30,
                  arrowStyle: 2,

                  hideCloseButton: !featureSettings.closeButton,
                  closeButtonSrc: featureSettings.closeButtonSrc,
                  backgroundClassName: 'infobubble',
                  backgroundColor: featureSettings.backgroundColor,
                  minWidth: featureSettings.minWidth,
                  maxWidth: featureSettings.maxWidth,
                  minHeight: featureSettings.minHeight,
                  maxHeight: featureSettings.maxHeight
                });
              }

              if (featureSettings.closeOther) {
                if (typeof map.infoBubble !== 'undefined') {
                  map.infoBubble.close();
                }
                map.infoBubble = currentMarker.infoBubble;
              }

              currentMarker.infoBubble.open(map.googleMap, currentMarker);
            });
          });

          return true;
        },
        drupalSettings
      );
    },
    detach: function (context, drupalSettings) {}
  };
})(jQuery, Drupal);
