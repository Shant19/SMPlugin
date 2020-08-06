<div class="calendar-content">
	<input type="hidden" id="vkUserName" value="<?=$_SESSION['vk_user']['username']?>">
	<input type="hidden" id="hoUserName" value="<?=$_SESSION['hootsuite_user']['username']?>">
	<div class="calendar-header">
		<div class="calendar-header-left">
			<i class="fa fa-download platform-download btn" aria-hidden="true"></i>
			<select id="sType" class="calendar-select inp">
				<option value="no" disabled selected>Choose...</option>
				<option value="twitter">Twitter</option>
				<?php if (isset($_SESSION['buffer_user'])) {
					echo '<option value="buffer">Buffer</option>';
				}
				if (isset($_SESSION['linkedin_user'])) {
					echo '<option value="linkedin">Linkedin</option>';
				}
				if (isset($_SESSION['vk_user'])) {
					echo '<option value="vk">VK</option>';
				}
				if (isset($_SESSION['hootsuite_user'])) {
					echo '<option value="hootsuite">Hootsuite</option>';
				}?>
			</select>
			<input type="text" placeholder="Keyword" class="calendar-keywords inp" id="cKeywords">
		</div>
		<div class="calendar-header-right">
			<button class="btn change-month" id="prev"><</button>
			<span class="month-year" id="monthYear"></span>
			<button class="btn change-month" id="next">></button>
		</div>
	</div>
	<div class="calendar-grid-header">
		<span class="calendar-header-text" data-week="1">Sun</span>
		<span class="calendar-header-text" data-week="2">Mon</span>
		<span class="calendar-header-text" data-week="3">Tue</span>
		<span class="calendar-header-text" data-week="4">Wed</span>
		<span class="calendar-header-text" data-week="5">Thu</span>
		<span class="calendar-header-text" data-week="6">Fri</span>
		<span class="calendar-header-text" data-week="7">Sat</span>
	</div>
	<div class="calendar-grid" id="calendarContainer"></div>
	<div class="updates-container" id="smUpdates"></div>
</div>
<script data-id="ki-publish" src="<?=BASE_URL?>assets/js/calendar.js"></script>
<script data-id="ki-publish" src="<?=BASE_URL?>assets/js/modal.js"></script>