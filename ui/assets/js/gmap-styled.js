/* Google styled map */
(function(){
  "use strict";

  // When the window has finished loading create our google map below
  google.maps.event.addDomListener(window, 'load', init);
  var googleNull = {
    LatLng: function (l, r) {
      return;
    },
    Size: function (l, r) {
      return;
    },
    Point: function (l, r) {
      return;
    }
  }
  var gm = typeof google !== 'undefined' ? google.maps : googleNull;
  var params = {
    zoom: 17,
    center: new gm.LatLng(51.9468403,15.5138024), // Retro
    markSize: new gm.Size(100, 79),
    startPoint: new gm.Point(0, 0),
    chPoint: new gm.Point(25, 79),
    mapMarkUrl: indicatorUrl,//"/media/google-mark-retro.png",
    title: 'Retro',
    infowindows: new google.maps.InfoWindow({content: "<strong>Reprezentuj.com / reklama i wydarzenia</strong><br/>ul.Zamkowa 42,<br/>65-086 Zielona Góra" }),
    styles: []
  };

  function init() {
    // Preventing the Google Maps libary from downloading an extra font

    console.log("start map");
    var head = document.getElementsByTagName('head')[0];
    var insertBefore = head.insertBefore;
    head.insertBefore = function (newElement, referenceElement) {
      // intercept font download
      if (newElement.href
        && newElement.href.indexOf('https://fonts.googleapis.com/css?family=Roboto') === 0) {
          return;
        }
        // intercept style elements for IEs
        if (newElement.tagName.toLowerCase() === 'style'
        && newElement.styleSheet
        && newElement.styleSheet.cssText
        && newElement.styleSheet.cssText.replace('\r\n', '').indexOf('.gm-style') === 0) {
          return;
        }
        // intercept style elements for other browsers
        if (newElement.tagName.toLowerCase() === 'style'
        && newElement.innerHTML
        && newElement.innerHTML.replace('\r\n', '').indexOf('.gm-style') === 0) {
          return;
        }
        insertBefore.call(head, newElement, referenceElement);
      };
      // Basic options for a simple Google Map
      // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
      var styledMap = new gm.StyledMapType(params.styles, {
        name: params.title + " Mapa"
      });
      var image = {
        url: params.mapMarkUrl, //'{{ @TEMPLATE }}images/rcom-gmap-mark.png',
        size: params.markSize,
        origin: params.startPoint,
        anchor: params.chPoint
      }
      var mapOptions = {
        zoom: params.zoom,
        center: params.center,
        styles: params.styles,
        scrollwheel: false,
        mapTypeControl: false,
        zoomControl: true,
        zoomControlOptions: {
          position: google.maps.ControlPosition.LEFT_TOP
        },
        panControl: false,
        streetViewControl: false,
        mapTypeIds: [gm.MapTypeId.ROADMAP, 'map_style']
      };

      var mapElement = document.getElementById('google-map');
      var map = new gm.Map(mapElement, mapOptions);
      map.mapTypes.set('map_style', styledMap);
      map.setMapTypeId('map_style');

      // Let's also add a marker while we're at it
      var marker = new gm.Marker({
        position: params.center,
        map: map,
        title: params.title,
        icon: image
      });
      marker.addListener('click', function() {
          window.open("https://www.google.com/maps/place/Hotel+Retro+B.A+Zientarski/@51.94684,15.513802,17z/data=!4m5!3m4!1s0x0:0xd34abe4e77c0f173!8m2!3d51.94683!4d15.5137488?hl=pl", "_blank");
      });
    };
  })();
