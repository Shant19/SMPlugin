function Followers() {
	const self = this;

	this.Http   = new XMLHttpRequest(),
	this.bUrl   = document.getElementById('base_url').getAttribute('data-url'),
	this.apiUrl = `${this.bUrl}includes/actions/actions.php`,

	this.init = function() {
		this.getFollowers();
	},

	this.post = function(data, action) {
		return new Promise(function(resolve, reject) {
			self.Http.open("POST", self.apiUrl, true);
			self.Http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			self.Http.onload = function() {
				resolve(self.Http.responseText)
			}

			data = JSON.stringify(data);
			self.Http.send(`action=${action}&data=${data}`);
		})
	},

	this.getFollowers = function() {

		self.post({}, 'get_tw_followers')
		.then(res => {
			this.showMap(JSON.parse(res));
		})
	},

	this.getLocation = function() {
		self.post({}, 'get_tw_locations')
		.then(res => {
			console.log(JSON.parse(res));
		})
	},

	this.showMap = function(info = false) {
		var baseMapPath = "https://code.highcharts.com/mapdata/",
		    showDataLabels = false,
		    mapCount = 0,
		    searchText,
		    mapOptions = '';

		// Populate dropdown menus and turn into jQuery UI widgets
		$.each(Highcharts.mapDataIndex, function (mapGroup, maps) {
		    if (mapGroup !== "version") {
		        mapOptions += '<option class="option-header">' + mapGroup + '</option>';
		        $.each(maps, function (desc, path) {
		            mapOptions += '<option value="' + path + '">' + desc + '</option>';
		            mapCount += 1;
		        });
		    }
		});

		searchText = 'Search ' + mapCount + ' maps';
		mapOptions = '<option value="custom/world.js">' + searchText + '</option>' + mapOptions;
		$("#mapDropdown").append(mapOptions).combobox();

		// Change map when item selected in dropdown
		$("#mapDropdown").change(function () {
		    var $selectedItem = $("option:selected", this),
		        mapDesc = $selectedItem.text(),
		        mapKey = this.value.slice(0, -3),
		        svgPath = baseMapPath + mapKey + '.svg',
		        geojsonPath = baseMapPath + mapKey + '.geo.json',
		        javascriptPath = baseMapPath + this.value,
		        isHeader = $selectedItem.hasClass('option-header');

		    // Dim or highlight search box
		    if (mapDesc === searchText || isHeader) {
		        $('.custom-combobox-input').removeClass('valid');
		        location.hash = '';
		    } else {
		        $('.custom-combob.ox-input').addClass('valid');
		        location.hash = mapKey;
		    }

		    if (isHeader) {
		        return false;
		    }

		    // Show loading
		    if (Highcharts.charts[0]) {
		        Highcharts.charts[0].showLoading('<i class="fa fa-spinner fa-spin fa-2x"></i>');
		    }


		    // When the map is loaded or ready from cache...
		    function mapReady() {

		        var mapGeoJSON = Highcharts.maps[mapKey],
		            data = {},
		            parent,
		            match;

		        // Update info box download links
		        $("#download").html(
		            '<a class="button" target="_blank" href="https://jsfiddle.net/gh/get/jquery/1.11.0/' +
		                'highcharts/highcharts/tree/master/samples/mapdata/' + mapKey + '">' +
		                'View clean demo</a>' +
		                '<div class="or-view-as">... or view as ' +
		                '<a target="_blank" href="' + svgPath + '">SVG</a>, ' +
		                '<a target="_blank" href="' + geojsonPath + '">GeoJSON</a>, ' +
		                '<a target="_blank" href="' + javascriptPath + '">JavaScript</a>.</div>'
		        );

		        // Generate non-random data for the map
		        $.each(mapGeoJSON.features, function (index, feature) {
		        	info.users.forEach(e => {
			        	// console.log(index, feature.properties.name, e.location);
			        	if (e.location == feature.properties.name) {
			        		if(data[e.location]) {
			        			data[e.location].value += 1;
			        		}else {
					            data[e.location] = {
					                key: feature.properties['hc-key'],
					                value: 1
					            };
			        		}
			        	}
		        	})
		        });

		        console.log(data)
		        data = Object.values(data);
		        console.log(data)

		        // Show arrows the first time a real map is shown
		        if (mapDesc !== searchText) {
		            $('.selector .prev-next').show();
		            $('#sideBox').show();
		        }

		        // Is there a layer above this?
		        match = mapKey.match(/^(countries\/[a-z]{2}\/[a-z]{2})-[a-z0-9]+-all$/);
		        if (/^countries\/[a-z]{2}\/[a-z]{2}-all$/.test(mapKey)) { // country
		            parent = {
		                desc: 'World',
		                key: 'custom/world'
		            };
		        } else if (match) { // admin1
		            parent = {
		                desc: $('option[value="' + match[1] + '-all.js"]').text(),
		                key: match[1] + '-all'
		            };
		        }
		        $('#up').html('');
		        if (parent) {
		            $('#up').append(
		                $('<a><i class="fa fa-angle-up"></i> ' + parent.desc + '</a>')
		                    .attr({
		                        title: parent.key
		                    })
		                    .click(function () {
		                        $('#mapDropdown').val(parent.key + '.js').change();
		                    })
		            );
		        }


		        // Instantiate chart
		        $("#container").highcharts('Map', {

		            title: {
		                text: null
		            },

		            mapNavigation: {
		                enabled: true
		            },

		            colorAxis: {
		                min: 0,
		                stops: [
		                    [0, '#FFF'],
		                    [0.5, '#4ac3ff'],
		                    [1, '#0576ae']
		                ]
		            },

		            legend: {
		                layout: 'vertical',
		                align: 'left',
		                verticalAlign: 'bottom'
		            },

		            series: [{
		                data: data,
		                mapData: mapGeoJSON,
		                joinBy: ['hc-key', 'key'],
		                name: 'Followers count',
		                states: {
		                    hover: {
		                        color: Highcharts.getOptions().colors[2]
		                    }
		                },
		                dataLabels: {
		                    // enabled: showDataLabels,
		                    // formatter: function () {
		                    //     return mapKey === 'custom/world' || mapKey === 'countries/us/us-all' ?
		                    //         (this.point.properties && this.point.properties['hc-a2']) :
		                    //         this.point.name;
		                    // }
		                },
		                point: {
		                    // events: {
		                    //     // On click, look for a detailed map
		                    //     click: function () {
		                    //         var key = this.key;
		                    //         $('#mapDropdown option').each(function () {
		                    //             if (this.value === 'countries/' + key.substr(0, 2) + '/' + key + '-all.js') {
		                    //                 $('#mapDropdown').val(this.value).change();
		                    //             }
		                    //         });
		                    //     }
		                    // }
		                }
		            }, {
		                type: 'mapline',
		                name: "Separators",
		                data: Highcharts.geojson(mapGeoJSON, 'mapline'),
		                nullColor: 'gray',
		                showInLegend: false,
		                enableMouseTracking: false
		            }]
		        });

		        showDataLabels = $("#chkDataLabels").prop('checked');

		    }

		    // Check whether the map is already loaded, else load it and
		    // then show it async
		    if (Highcharts.maps[mapKey]) {
		        mapReady();
		    } else {
		        $.getScript(javascriptPath, mapReady);
		    }
		});

		// Toggle data labels - Note: Reloads map with new random data
		$("#chkDataLabels").change(function () {
		    showDataLabels = $("#chkDataLabels").prop('checked');
		    $("#mapDropdown").change();
		});

		// Switch to previous map on button click
		$("#btn-prev-map").click(function () {
		    $("#mapDropdown option:selected").prev("option").prop("selected", true).change();
		});

		// Switch to next map on button click
		$("#btn-next-map").click(function () {
		    $("#mapDropdown option:selected").next("option").prop("selected", true).change();
		});

		// Trigger change event to load map on startup
		if (location.hash) {
		    $('#mapDropdown').val(location.hash.substr(1) + '.js');
		} else { // for IE9
		    $($('#mapDropdown option')[0]).attr('selected', 'selected');
		}
		$('#mapDropdown').change();
	}

}

document.addEventListener("DOMContentLoaded", function() {
	var followers = new Followers(),
		functions = {
			// getFollowers: document.getElementById('getFollowers'),
			// getLocation: document.getElementById('getLocation')
		};
	
	followers.init();

	for(func in functions) {
		functions[func].addEventListener('click', followers[func])
	}
})


