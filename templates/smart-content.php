<div class="body-container">
	<ul class="assigned-menu smart-content">
		<li>General</li>
	</ul>
	<div class="kl-smart-body">
		<div class="kl-smart-body-consistence">
			<div class="kl-smart-body-consistence-header">
				<h2>Smart Content Config</h2>
				<p>Syndicate content based on sources and keywords.</p>
			</div>
			<div class="kl-smart-body-consistence-inputs">
				<div>
					<label for="twitter_user_to_follow">
						Twitter Name
						<input type="text" id="twitter_user_to_follow" name="twitter_user_to_follow">
					</label>
					<label for="keawords">
						Keawords (max 20 comma separated, leave empty to get all tweets)
						<input type="text" id="keawords" name="keawords">
					</label>
					<label for="timer">
						Timer 1
						<input type="text" id="timer1" name="timer" placeholder="HH:MM">
					</label>
				</div>
				<div>
					<button id="add-timer" class="btn btn-default" type="button">Add timer</button>
					<button type="button" class="btn btn-default" id="submitFormCronActions">Submit</button>				
				</div>
			</div>
			<table class="timer-table">
				<tr>
					<th>ID</th>
					<th>twitter_user_to_follow</th>
					<th>quantity twitts</th>
					<th>keawords</th>
					<th>timers</th>
					<th>last_run</th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
				<tr class="second_part">
					<th>36</th>
					<th>Asd</th>
					<th>1</th>
					<th>social,media</th>
					<th>01:00, 07:00, 12:00, 19:00</th>
					<th>2018-03-05 06:01:02</th>
					<th class="centered_icons"><i class="fa fa-pencil" title="Edit"></i></th>
					<th class="centered_icons"><i class="fa fa-pause"></i></i></th>
					<th class="centered_icons"><i class="fa fa-trash" aria-hidden="true"></i></th>
				</tr>
			</table>
			<br><br>
			<span><?="Current Server Time:" . date('Y-m-d') . ' (' . date("D") . ') ' . date("H:i");?></span>
		</div>
	</div>
</div>
<script data-id="ki-publish" src="<?=BASE_URL?>assets/js/modal.js"></script>