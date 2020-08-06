<div class="stream" style=" background:#f3f3f3">
<div class="stream-header users-header">
	
</div>
<?php if(empty($teams)) { ?> 
	<div class="user-content">
	    <img src="<?=BASE_URL?>images/buttons/icon-calender.png" alt="">
	    <p class="empty-message">No Teams right now<br>Please Add new Team</p>
	    <button class="btn-blue" data-modal='openModal' data-type='add-team'>Add team</button>
	</div>
<?php }else { ?>
	<div class="user-team">
		<div class="visible-teams">
			<ul id="visibleTeam">
				<?php foreach ($teams as $value) { ?>
					<li class="team" data-id="<?=$value->id?>" data-teammate="<?=$value->team_id?1:0?>"><?=$value->team_name?></li>
				<?php } ?>
			</ul>
		</div>
		<div class="dropdown-teams">
		<?php if (count($dTabs)) { ?>
			<span id="dTeamToggle">&gt;&gt;</span>
			<ul id="dropDownTeam">
				<?php foreach ($dTabs as $key => $value) { ?>
					<li class="team" data-id="<?=$value->id?>" data-teammate="<?=$value->team_id?1:0?>"><?=$value->team_name?></li>
				<?php } ?>
			</ul>
		<?php } ?>
		</div>
		<button class="btn btn-pink" data-modal='openModal' data-type='add-team'>
			<img src="<?=BASE_URL?>images/buttons/icon-plus.png" class="cm-mr-3">Add team
		</button>
	</div>
	<div class="users">
		<div class="teammate-body">
			<table id="userTable">
				<tr>
					<td class="user-table-header">Username</td>
					<td class="user-table-header">Role</td>
					<td class="user-table-header"></td>
				</tr>
			</table>
			<button class="add-users" data-modal="openModal" data-type="add-teammate">
				Add users
				<img src="<?=BASE_URL?>images/buttons/icon-plus-blue.png">
			</button>
		</div>
	</div>
<?php } ?>
</div>
<script data-id="ki-publish" src="<?=BASE_URL?>assets/js/modal.js"></script>
<script data-id="ki-publish" src="<?=BASE_URL?>assets/js/users.js"></script>