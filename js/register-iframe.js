$(function( ) {

	initMap();

	$('.leaflet-control-mapbox-geocoder .leaflet-control-mapbox-geocoder-form input').attr('placeholder', 'Search for address...');

	function initMap() {
		var stopIconUrl = 'images/map-marker-icon.png';
		var selectedStopIconUrl = 'images/map-marker-selected-icon.png';

		var stopIcon = L.icon({
			iconUrl: stopIconUrl,
			iconSize: [30, 30],
			iconAnchor: [15, 30],
			popupAnchor: [0,-30]
		});

		var selectedStopIcon = L.icon({
			iconUrl: selectedStopIconUrl,
			iconSize: [30, 30],
			iconAnchor: [15, 30],
			popupAnchor: [0,-30]
		});

		L.mapbox.accessToken = 'pk.eyJ1IjoianJoYXJzaGF0aCIsImEiOiJLQ19oQ0lnIn0.WOJhLVoEGELi8cW93XIS1Q';

		var geocoder = L.mapbox.geocoderControl('mapbox.places', {autocomplete: true, keepOpen: true});
		var map = L.mapbox.map('map', 'mapbox.streets', {zoomControl: false})
			.addControl(geocoder)
			.setView([33.7615242074253, -84.38117980957031], 17);
		new L.Control.Zoom({ position: 'topright' }).addTo(map);

		geocoder.on('select', function(res) {
			var lonlat = res.feature.center;
			map.setView([lonlat[1], lonlat[0]], 17);
		});

		// todo control spinners in these functions
		function startSpinner() {  }
		function stopSpinner() {  }

		function debounce(fn, delay) {
			var timer = null;
			return function () {
				var context = this, args = arguments;
				clearTimeout(timer);
				timer = setTimeout(function () {
					fn.apply(context, args);
				}, delay);
			};
		}

		var loadedStops = [];

		var refreshMap = debounce(function() {
			startSpinner();
			var latlng = map.getCenter();  
			var url = 'http://atlanta.onebusaway.org/api/api/where/stops-for-location.json' +
			          '?key=TEST&lat='+latlng.lat+'&lon=' + latlng.lng + '&radius=400';

			$.ajax({
				url: url,
				dataType: 'jsonp',
				success: function(json) {
					var stops = json.data.list;
					for (var i = 0; i < stops.length; i++) {
						var stop = stops[i];
						if(-1 == $.inArray(stop['id'], loadedStops)) {
							addMarker(stop);
							loadedStops.push(stop['id']);
						}
					};
					stopSpinner();
				}
			})
		}, 250);

		refreshMap();

		map.on('moveend', refreshMap);
		geocoder.on('select', refreshMap);

		var markers = [];
		function addMarker(stop) {
			var marker = L.marker([stop.lat, stop.lon])
				.setIcon(stopIcon)
				.on('click', markerClicked)
				.addTo(map);

			var description = stop['name']+"<br/><a href='#' class='adopt-stop'>Adopt this stop</a></div></div>";
			marker.bindPopup(description)

			marker.stopname = stop['name'];
			marker.stopid = stop['id'];
			marker.selected = false;			

			markers.push(marker);
		}

		var selectedMarker = null;
		function markerClicked(e) {
			selectedMarker = e.target;		
		}

		$(document).on('click', 'a.adopt-stop', function(e) {
			e.preventDefault();

			var stopid = selectedMarker.stopid; 
			var stopname = selectedMarker.stopname;

			var $nonemsg = $('#nostopselectedmsg');
			var $list = $('#stoplist_ol');
			
			if(selectedMarker.selected) {
				selectedMarker.setIcon(stopIcon);
				selectedMarker.selected = false;

				if($list.find('li').length==1) {
					$nonemsg.slideDown();
					$list.slideUp();
				}

				$list.find('span.stopid').each(function(i,sp) {
					var $sp = $(sp);
					if($sp.text() == stopid) {
						var $li = $sp.closest('li');
						$li.slideUp(function() { $li.remove() });
					}
				});

				var description = stopname+"<br/><a href='#' class='adopt-stop'>Adopt this stop</a></div></div>";
				selectedMarker.bindPopup(description)
				$(this).text('Adopt this stop');
				
			} else {
				selectedMarker.setIcon(selectedStopIcon);
				selectedMarker.selected = true;

				$nonemsg.slideUp();
				
				$list.append("<li>"+stopname+" <span class='stopid'>"+stopid+"</span> <a href='#'>remove</a></li>");
				$list.slideDown();

				var description = stopname+"<br/><a href='#' class='adopt-stop'>Unselect this stop</a></div></div>";
				selectedMarker.bindPopup(description)
				$(this).text('Unselect this stop');
			}
		});

		$(document).on('click', '#stoplist_ol li a', function(e) {
			e.preventDefault();

			var $li = $(this).closest('li');
			var stopid = $li.find('span.stopid').text();
			$.each(markers, function(i,m) {
				if(m.stopid==stopid) {
					m.selected = false;
					m.setIcon(stopIcon);
				}
			});
			$li.slideUp(function() { $li.remove() });
		});
	}
	
	$('#stopmap-div a.togglelink').click(function() {
		$('#stopmap-div').slideUp();
		$('#stopaddress-div').slideDown();
	});

	$('#stopaddress-div a.togglelink').click(function() {
		$('#stopaddress-div').slideUp();
		$('#stopmap-div').slideDown();
	})

	
	$('#signup-form button').click(function(e) {
		e.preventDefault();

		var name = $('#name').val();
		var email = $('#email').val();
		var phone = $('#phone').val();
		var comment = $('#comment').val();

		var data = {name: name, email: email, phone: phone, comment: comment}


		if($('#stopmap-div').is(':visible')) {
			var stopids = [];
			var stopnames = [];
			$('#stoplist_ol li').each(function(i,li) {
				var $li = $(li);
				stopids.push($li.find('span.stopid').text());

				$li = $li.clone();
				$li.find('span.stopid').remove();
				$li.find('a').remove();
				stopnames.push($li.text());
			});
			data.stopmode = 'stopids';
			data.stopids = stopids;
			data.stopnames = stopnames;
		} else {
			var stopaddress = $('#stopaddress-div input').val();
			data.stopmode = 'stopaddress';
			data.stopaddress = stopaddress;
		}

		$('#signup-form button').prop('disabled', true);

		$.ajax({
		  url:  "ajax/register-iframe.php",
		  type: "POST",
		  data:    data,
		  dataType: 'json',
		  
		  success: function(d) {
			switch(d.status) {
			case 'success':
				$('#signup-form').slideUp();
				$('#success-message').slideDown();
				break;

			case 'noname':
				showError('Oops! A name is required...');
				break;
			case 'bademail':
				showError('Email seems invalid. Check it again?');
				break;
			case 'nocomment':
				showError('Please leave a comment for us!');
				break;
			case 'nostoptoadopt':
				showError("You haven't selected any stop yet!");
				break;
			default:
				showError("Oops, something broke on our side. Please try again later, and if it doesn't work, please let us know at themartaarmy@gmail.com!", 0);
				break;
			}
			$('#signup-form button').prop('disabled', false);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			showError("Oops, something broke on our side. Please try again later, and if it doesn't work, please let us know at themartaarmy@gmail.com!", 0);
			$('#signup-form button').prop('disabled', false);
 		}});
	});

	function showError(msg, delay) {
		if(delay === undefined) { delay = 3000; }

		var $msg = $('#error-message');
		$msg.html(msg).slideDown();
		if(delay !== 0) {
			setTimeout(function() { $msg.slideUp(); }, delay);
		}
	}

	function showMessage(msg) {
		var $msg = $('#success-message');
		$msg.html(msg).slideDown();
	}
});
