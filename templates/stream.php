<div class="stream" style=" background:#f3f3f3">
	<div class="stream-header">
	    
	</div>
	<div class="stream-content" id="streamContainer">
		<?php if (!count($tabs)) { ?>
			<div class="stream-empty">
			    <img src="<?=BASE_URL?>images/buttons/icon-calender.png" alt="">
			    <p class="empty-message">No created tabs right now<br>Please Add new tab</p>
			    <button class="btn-blue" data-modal='openModal' data-type='add-tab'>Add tab</button>
			</div>
		<?php } else {?>
			<div class="tabs-container">
				<ul id="visibleTab">
					<?php foreach ($tabs as $key => $value) { ?>
						<li class="tab" data-stream="<?=$value->tab_id?1:0?>" data-team="<?=$value->team_id?>" data-id="<?=$value->id?>"><?=$value->tab_name?></li>
					<?php } ?>
				</ul>
				<?php if (count($dTabs)) { ?>
					<span id="dTabToggle">&gt;&gt;</span>
					<ul id="dropDownTab">
						<?php foreach ($dTabs as $key => $value) { ?>
							<li class="tab" data-stream="<?=$value->tab_id?1:0?>" data-team="<?=$value->team_id?>" data-id="<?=$value->id?>"><?=$value->tab_name?></li>
						<?php } ?>
					</ul>
				<?php } ?>
				<div id="streamButtonsContainer">
					<button data-modal="openModal" data-type="add-tab">
						<img src="<?=BASE_URL?>images/buttons/015__circle_plus.png"> Add tab
					</button>
					<button class="add-edit-stream" data-modal="openModal" data-type="add-stream">
						<img src="<?=BASE_URL?>images/buttons/icon-plus.png"> Add stream
					</button>
				</div>
			</div>
		<?php } ?>

		<div class="stream-data-body"></div>
	</div>
</div>

<div class="strem-item-functions">
	<img src="" alt="" class="strem-item-function like">
	<img src="" alt="" class="strem-item-function respond">
	<img src="" alt="" class="strem-item-function retweet">
	<img src="" alt="" class="strem-item-function assign">
</div>

<button id="assignModal" style="display: none;" data-modal="openModal" data-type="assign"></button>

<script data-id="ki-publish" src="<?=BASE_URL?>assets/js/modal.js"></script>
<script data-id="ki-publish" src="<?=BASE_URL?>assets/js/stream.js"></script>