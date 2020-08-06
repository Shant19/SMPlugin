function Users(){
	const self = this;

	this.Http   	   = new XMLHttpRequest(),
	this.bUrl   	   = document.getElementById('base_url').getAttribute('data-url'),
	this.apiUrl 	   = `${this.bUrl}includes/actions/actions.php`,
	this.userContainer = document.getElementById('streamContainer'),
	this.teams,
	this.activeTeam,
	this.findedTeammates,

	this.init = function() {
		this.bindEvents();
		this.setActiveTab();
	},

	this.post = function(data, action) {
		return new Promise(function(resolve, reject) {
			self.Http.open("POST", self.apiUrl, true);
			self.Http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			self.Http.onload = function() {
				resolve(self.Http.responseText);
			}

			data = JSON.stringify(data);
			self.Http.send(`action=${action}&data=${data}`);
		})

	},

	this.setActiveTab = function() {
		this.teams = document.getElementsByClassName('team');
		if(this.teams.length) {
			this.teams[0].classList.add('active');
			this.activeTeam = this.teams[0];
			this.getTeammates(this.activeTeam.getAttribute('data-id'));
			this.empty(document.getElementById('userTable'), 1);
			if (this.activeTeam.getAttribute('data-teammate') == '1') {
				this.getTeammates(this.activeTeam.getAttribute('data-id'))
			}
			Object.values(this.teams).forEach(e => {
				e.addEventListener('click', e => {
					self.changeTeam(e.target);
					if (this.activeTeam.getAttribute('data-teammate') == '1') {
						self.getTeammates(e.target.getAttribute('data-id'));
					}
				})
			})
		}
	},

	this.getTeammates = function(teamId) {
		this.post(teamId, 'get_teammates')
		.then(res => {
			res = JSON.parse(res);
			res.res.forEach(e => {
				var tr = document.createElement('tr'),
					user = document.createElement('td'),
					role = document.createElement('td'),
					remove = document.createElement('button'),
					select = document.createElement('select'),
					removeTd = document.createElement('td'),
					option;

				console.log(e)
				user.innerHTML = e.user_name;
				select.classList.add('role-select');
				select.setAttribute('teammate-id', e.id);
				user.classList.add('user-table-body');
				role.classList.add('user-table-body');
				remove.classList.add('delete-teammate');
				remove.setAttribute('data-id', e.id);
				remove.innerHTML = 'Delete';
				remove.addEventListener('click', e => {
					self.removeTeammate(e.target);
				})
				select.addEventListener('change', e => {
					self.changeUserRole(e.target);
				})

				res.role.forEach(el => {
					option = document.createElement('option');
					if (el.name == e.role) {
						option.setAttribute('selected', true);
					}
					option.value 	 = el.name;
					option.innerHTML = el.value;

					select.appendChild(option);
				})
				removeTd.appendChild(remove);
				tr.setAttribute('data-id', e.user_id);
				role.appendChild(select);
				tr.appendChild(user);
				tr.appendChild(role);
				tr.appendChild(removeTd);
				document.getElementById('userTable').appendChild(tr);
			})
		})
	},

	this.changeUserRole = function(el) {
		this.post({teammateId: el.getAttribute('teammate-id'), role: el.value}, 'change_user_role')
		.then(res => {
			console.log(res)
		})
	},

	this.removeTeammate = function(teammate) {
		this.post({tmId: teammate.getAttribute('data-id')}, 'remove_teammate')
		.then(res => {
			console.log(res)
			teammate.parentNode.parentNode.remove();
		})
	},

	this.empty = function(elem, except = false) {
		var child  = elem.lastElementChild;

		while (child && elem.children.length > except) { 
		    elem.removeChild(child); 
		    child = elem.lastElementChild; 
		}
	},

	this.opneTeams = function() {
		var dTeam = document.getElementById('dropDownTeam'), style = getComputedStyle(dTeam);

		if(style.display == 'flex') {
			dTeam.style.display = 'none';
		}else {
			dTeam.style.display = 'flex';
		}
	},

	this.changeTeam = function(element) {
		this.activeTeam.classList.remove('active');
		this.activeTeam = element;
		element.classList.add('active');
		this.empty(document.getElementById('userTable'), 1);
	},

	this.addTeam = function() {
		var teamName = document.getElementById('teamName').value;

		if (teamName) {
			self.post({team_name: teamName}, 'add_team')
			.then(res => {
				res = JSON.parse(res);
				if (!res.error) {
					self.setTeam(res);
				}else {
					alert(res.error);
				}
			})
		}
	},

	this.setTeam = function(team) {
		var teams 	     = document.getElementById('visibleTeam'),
			dTeamToggle  = document.getElementById('dTeamToggle'),
			newTeam      = document.createElement('li'),
			dTeams		 = document.getElementsByClassName('dropdown-teams')[0],	
			dropDownTeam;

			newTeam.classList.add('team');
			newTeam.setAttribute('data-id', team.id);
			newTeam.setAttribute('data-teammate', `${team.team_id?1:0}`);
			newTeam.innerHTML = team.team_name;

			newTeam.addEventListener('click', e => {
				self.changeTeam(e.target)
			})
			if (teams) {
				if(teams.children.length < 7) {
					teams.appendChild(newTeam);
				}else if(teams.children.length == 7 && !dTeamToggle) {
					dTeamToggle  			   = document.createElement('span');
					dropDownTeam 			   = document.createElement('ul');
					dTeamToggle.id 			   = 'dTeamToggle';
					dTeamToggle.innerHTML 	   = '>>';
					dropDownTeam.id 		   = 'dropDownTeam';
					dropDownTeam.style.display = 'none';

					dTeamToggle.addEventListener('click', e => {
						self.opneTeams.call(e.target)
					})
					dropDownTeam.appendChild(newTeam);
					dTeams.appendChild(dTeamToggle);
					dTeams.appendChild(dropDownTeam);
				}else {
					dTeams.children[1].appendChild(newTeam)
				}
				modal.closeModal();
			}else {
				window.location.reload();
			}
	},

	this.setUserList = function() {
		if(this.findedTeammates) {
			var lContainer = document.getElementById('userList'), img, span, li; self.empty(lContainer, 0);

			this.findedTeammates.forEach((e, i) => {
				li   		   = document.createElement('li');
				span 		   = document.createElement('span');
				img  		   = document.createElement('img');
				span.innerHTML = e.username;

				img.setAttribute('src',e.picture);
				li.setAttribute('data-id', i);
				li.appendChild(img);
				li.appendChild(span);
				lContainer.appendChild(li);
				li.addEventListener('click', e => {
					self.chooseUser.call(e.target, lContainer)
				})
			})
		}
	},

	this.chooseUser = function(lContainer) {
		var searchInput = document.getElementById('teammateSearch'),
			user 		= self.findedTeammates[this.getAttribute('data-id')?this.getAttribute('data-id'):this.parentNode.getAttribute('data-id')];

		searchInput.value = user.username;
		searchInput.setAttribute('data-uid', user.oauth_uid);
		self.empty(lContainer, 0);
	},

	this.teammateSearch = function() {
		this.setAttribute('data-uid', '');
		if(!this.value){
			self.empty(document.getElementById('userList'), 0);
			return
		}else {
			self.post({sQuery: this.value}, 'find_user')
			.then(res => {
				self.findedTeammates = JSON.parse(res);
				self.setUserList();
			})
		}
	},

	this.addTeammate = function(){
		var data = {
			teamId: self.activeTeam.getAttribute('data-id'),
			userName: document.getElementById('teammateSearch').value,
		};

		if(data.userName) {
			self.post(data, 'insert_user_team')
			.then(res =>{
				alert(JSON.parse(res).info)
				window.location.reload();
				modal.closeModal();
			});
		}
	},

	this.bindEvents = function() {
		var clickEvents = {
			addTeam: document.getElementById('addTeam'),
			opneTeams: document.getElementById('dTeamToggle'),
			addTeammate:document.getElementById('addTeammate')
		},inputEvents = {
			teammateSearch: document.getElementById('teammateSearch')
		};


		for(key in clickEvents) {
			if (clickEvents[key]) {
				clickEvents[key].key = key;
				clickEvents[key].addEventListener('click', e => {
					self[e.target.key].call(e.target);
				});
			}
		}

		for(key in inputEvents) {
			if (inputEvents[key]) {
				inputEvents[key].key = key;
				inputEvents[key].addEventListener('input', e => {
					self[e.target.key].call(e.target);
				});
			}
		}
	}
}

document.addEventListener("DOMContentLoaded", function() {
	var users = new Users();users.init();
})