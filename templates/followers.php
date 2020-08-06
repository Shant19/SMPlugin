<style>
#demo-wrapper {
    margin: 0 auto;
    height: 560px;
    background: white;
}
#mapBox {
    width: 80%;
    float: left;
}
#container {
    height: 500px;
}
#sideBox {
    float: right;
    width: 16%;   
    margin: 100px 1% 0 1%;
    padding-left: 1%;
    border-left: 1px solid silver;
    display: none;
}
#infoBox {
    margin-top: 10px;
}
.or-view-as {
    margin: 0.5em 0;
}
#up {
    line-height: 30px;
    height: 30px;
    max-width: 400px;
    margin: 0 auto;
}
#up a {
    cursor: pointer;
    padding-left: 40px;
}
.selector {
    height: 40px;
    max-width: 400px;
    margin: 0 auto;
    position: relative;
}
.selector .prev-next {
    position: absolute;
    padding: 0 10px;
    font-size: 30px;
    line-height: 20px;
    background: white;
    font-weight: bold;
    color: #999;
    top: -2px;
    display: none;
    border: none;
}
.selector .custom-combobox {
    display: block;
    position: absolute;
    left: 40px;
    right: 65px;
}
.selector .custom-combobox .custom-combobox-input {
    position: absolute;
    font-size: 14px;
    color: silver;
    border-radius: 3px 0 0 3px;
    height: 32px;
    display: block;
    background: url(https://www.highcharts.com/samples/graphics/search.png) 5px 8px no-repeat white;
    padding: 1px 5px 1px 30px;
    width: 100%;
    box-sizing: border-box;
}
.selector .custom-combobox .ui-autocomplete-input:focus {
    color: black;
}
.selector .custom-combobox .ui-autocomplete-input.valid {
    color: black;
}
.selector .custom-combobox-toggle {
    position: absolute;
    display: block;
    right: -32px;
    border-radius: 0 3px 3px 0;
    height: 32px;
    width: 32px;
}

.selector #btn-next-map {
    right: -12px;
}
.ui-autocomplete {
    max-height: 500px;
    overflow: auto;
}
.ui-autocomplete .option-header {
    font-style: italic;
    font-weight: bold;
    margin: 5px 0;
    font-size: 1.2em;
    color: gray;
}

.loading {
    margin-top: 10em;
    text-align: center;
    color: gray;
}
.ui-button-icon-only .ui-button-text {
    height: 26px;
    padding: 0 !important;
    background: white;
}
#infoBox .button {
    border: none;
    border-radius: 3px;
    background: #a4edba;
    padding: 5px;
    color: black;
    text-decoration: none;
    font-size: 12px;
    white-space: nowrap;
    cursor: pointer;
    margin: 0 3px;
    line-height: 30px;
}

@media (max-width: 768px) {
    #demo-wrapper {
        width: auto;
        height: auto;
    }
    #mapBox {
        width: auto;
        float: none;
    }
    #container {
        height: 310px;
    }
    #sideBox {
        float: none;
        width: auto;
        margin-top: 0;
        border-left: none;
        border-top: 1px solid silver;
    }
}
.highcharts-credits {
    display: none;
}
.selector {
    display: none;
}
@font-face {
  font-family: myFirstFont;
  src: url(/wp-content/plugins/ki-publish/fonts/Segoe3.woff);
}

.test {
    font-family: myFirstFont;
}
</style>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://code.highcharts.com/maps/highmaps.js"></script>
<script src="https://code.highcharts.com/mapdata/index.js?1"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
<script src="https://www.highcharts.com/samples/maps/demo/all-maps/jquery.combobox.js"></script>
<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
<link href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<div class="followers-content">
    <!-- <span class="test">Asd</span> -->
	<!-- <button id="getFollowers">Get followers</button>
	<button id="getLocation">Get location</button> -->

	<div id="demo-wrapper">
	    <div id="mapBox">
	        <div id="up"></div>
	        <div class="selector">
	            <button id="btn-prev-map" class="prev-next"><i class="fa fa-angle-left"></i></button>
	            <select id="mapDropdown" class="ui-widget combobox"></select>
	            <button id="btn-next-map" class="prev-next"><i class="fa fa-angle-right"></i></button>
	        </div>
	        <div id="container"></div> 
	    </div>
	</div>
</div>

<script data-id="ki-publish" src="<?=BASE_URL?>assets/js/followers.js"></script>
<script data-id="ki-publish" src="<?=BASE_URL?>assets/js/modal.js"></script>

