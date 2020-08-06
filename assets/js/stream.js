function Stream(){
	const self = this;

	this.Http   		 = new XMLHttpRequest(),
	this.bUrl   		 = document.getElementById('base_url').getAttribute('data-url'),
	this.apiUrl 		 = `${this.bUrl}includes/actions/actions.php`,
	this.streamContainer = document.getElementById('streamContainer'),
	this.streamBody      = document.getElementsByClassName('stream-data-body')[0],
	this.posts,
	this.tabs,
	this.activeTab,
	this.findTeams,
	this.message,

	this.init = function() {
		this.setActiveTab();
		this.bindEvents();
		this.getStreamData();
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
		this.tabs = document.getElementsByClassName('tab');
		if(!window.location.hash) {
			window.location.hash = '0';
		}

		if (this.tabs.length) {
			this.tabs[window.location.hash.substr(1, window.location.hash.length)].classList.add('active');
			this.activeTab = this.tabs[window.location.hash.substr(1, window.location.hash.length)];
			Object.values(this.tabs).forEach((e, k) => {
				e.addEventListener('click', e => {
					self.changeTab(e.target, k)
				})
			})
			this.checkStream(this.activeTab);
		}
	},

	this.checkStream = function(tab) {
		var stream 			= tab.getAttribute('data-stream'),
			team			= tab.getAttribute('data-team'),
			tabId			= tab.getAttribute('data-id'),
			del				= document.getElementById('removeTab'),
			editStream 		= document.getElementsByClassName('add-edit-stream'),
			buttonContainer = document.getElementById('streamButtonsContainer');

		if(del) {del.parentNode.remove()}
		if (stream == '1') {
			var div 	     = document.createElement('div'),
				removeTabBtn = document.createElement('button');

			removeTabBtn.id = 'removeTab';
			editStream[0].innerHTML = 'Edit stream';
			removeTabBtn.innerHTML = '<i class="fa fa-remove width-20"></i>';
			removeTabBtn.addEventListener('click', () => {
				self.removeTab(tabId, tab, editStream[0]);
			})
			div.appendChild(removeTabBtn);
			if (!team) {
				var assignTabBtn = document.createElement('button');

				assignTabBtn.innerHTML = '<i class="fa fa-share  width-20"></i>';
				assignTabBtn.id 	   = 'asignTeam';

				assignTabBtn.setAttribute('data-modal', 'openModal');
				assignTabBtn.setAttribute('data-type', 'assign-team');
				assignTabBtn.addEventListener('click', e => {
					modal.addUser.call(e.target)
				})
				div.appendChild(assignTabBtn);
			}

			buttonContainer.children[1].parentNode.insertBefore(div, buttonContainer.children[1]);
		}else {
			editStream[0].innerHTML = `<img src="${this.bUrl}images/buttons/icon-plus.png"> Add stream`;
		}
	},

	this.removeTab = function(tabId, tab, streamBtn) {
		var dTabs = document.getElementById('dropDownTab'), vTabs = document.getElementById('visibleTab'), clone;

		this.post(tabId, 'remove_tab')
		.then(res => {
			tab.remove();
			self.setActiveTab();
			if(dTabs) {
				if (dTabs.children.length) {
					clone = dTabs.children[0].cloneNode(true);
					dTabs.children[0].remove();
					if(dTabs.children.length <= 1) {
						document.getElementById('dTabToggle').remove();
						dTabs.remove();	
					}
				}

				streamBtn.innerHTML = `<img src="${this.bUrl}images/buttons/icon-plus.png"> Add stream`;
				document.getElementById('removeTab').remove();
				vTabs.appendChild(clone);
				vTabs.children[0].classList.add('active');
			}
			if (visibleTab.children.length == 0) {
				window.location.reload();
			}
		})
	}

	this.changeTab = function(element, i) {
		window.location.hash = i;
		this.activeTab.classList.remove('active');
		this.activeTab = element;
		element.classList.add('active');
		this.checkStream(this.activeTab);
		this.getStreamData(element);
	},

	this.updateSearch = function(respons, type) {
		var data = {tabId: this.activeTab.getAttribute('data-id')}, action;
		
		type == 'search' ? (
				data.searchPhrase = respons,
				action = 'updateSearchStream'
			) : (
				data.rssUrls = respons,
				action = 'updateRssStream'
			);

		this.post(data, action)
		.then(res => {
			this.activeTab.click();
		})
	},

	this.composeSearchResult = function(id) {
		var img = document.createElement('img');

		img.setAttribute('src', this.posts[id].image);
		img.setAttribute('width', '100%');

		document.querySelector('[data-type="post"]').click();
		document.getElementById('cm-font-sm-bold').innerHTML = `${this.posts[id].title} ${this.posts[id].link}`;
		document.getElementById('smPostContent').value = `${this.posts[id].title} ${this.posts[id].link}`;
		this.empty(document.getElementById('preview_image'));
		document.getElementById('preview_image').appendChild(img);
		document.getElementById('smPostImg').value = this.posts[id].image;
	},

	this.createRsshHtml = function(res) {
		var a, td, trB, table, button;
		table = this.createHeader(['Title', 'Description', 'Updated', ''], res);
		res.forEach((e, i) => {
			trB = document.createElement('tr');
			trB.classList.add('stream-search-body');

			for(let j = 0; j < 4; j++) {
				td = document.createElement('td');

				switch(j) {
					case 0:
						a = document.createElement('a');
						a.setAttribute('href', e.link);
						a.setAttribute('target', '_blank');
						a.innerHTML = e.title;
						td.appendChild(a);
					break;
					case 1:
						td.innerHTML = e.description;
					break;
					case 2:
						td.innerHTML = e.added_time;
					break;
					case 3:
						button = document.createElement('button');
						button.innerHTML = 'Compose';
						button.addEventListener('click', function() {
							self.composeRssResult(i);
						})
						td.appendChild(button);
					break;
				}
				trB.appendChild(td);
			}
			table.appendChild(trB);
		})

		this.streamBody.appendChild(table);
	},

	this.composeRssResult = function(id) {
		document.querySelector('[data-type="post"]').click();
		document.getElementById('cm-font-sm-bold').innerHTML = `${this.posts[id].title} ${this.posts[id].link}`;
		document.getElementById('smPostContent').value = `${this.posts[id].title} ${this.posts[id].link}`;
	},

	this.createHeader = function(arr, res) {
		var update = document.createElement('div'),
			button = document.createElement('button'),
			date   = document.createElement('span'),
			dateC  = document.createElement('div'),
			span   = document.createElement('span'),
			table  = document.createElement('table'),
			trH    = document.createElement('tr');

		for(let i = 0; i < arr.length; i++) {
			thE = document.createElement('th');

			thE.innerHTML = arr[i];
			thE.setAttribute('align', 'left');

			trH.appendChild(thE);
		}

		update.classList.add('search-update-container');
		trH.classList.add('stream-search-header');
		table.classList.add('stream-search-table');

		button.id        = 'updateSearch';
		button.innerHTML = 'Update';
		button.addEventListener('click', function() {
			self.updateSearch(res[0].keyword ? res[0].keyword : res[0].link, res[0].keyword ? 'search' : 'rss');
		});
		date.innerHTML   = res[0].created_at;
		span.innerHTML   = 'Last update: ';

		dateC.appendChild(span);
		dateC.appendChild(date);
		update.appendChild(button);
		update.appendChild(dateC);
		table.appendChild(trH);
		this.streamBody.appendChild(update);

		return table
	}

	this.createSearchHtml = function(res) {
		var table, thE, trB, td, compose, a, img;

		table = this.createHeader(['Title', 'Snippet', 'Link', 'Image', ''], res);

		res.forEach((e, i) => {
			trB = document.createElement('tr');

			trB.classList.add('stream-search-body');
			trB.setAttribute('data-id', i);

			for(let j = 0; j < 5; j++) {
				td = document.createElement('td');

				switch(j) {
					case 0:
						td.innerHTML = e.title;
					break;
					case 1:
						td.innerHTML = e.snippet;
					break;
					case 2:
						a = document.createElement('a');
						a.setAttribute('href', e.link);
						a.setAttribute('target', '_blank');
						a.innerHTML = `${e.link.substring(0, 30)}...`;
						td.appendChild(a);
					break;
					case 3:
						img = document.createElement('img');
						img.setAttribute('src', e.image);
						img.classList.add('search-img')
						td.appendChild(img);
					break;
					case 4:
						compose = document.createElement('button');
						compose.setAttribute('data-id', i);
						compose.innerHTML = 'Compose';
						compose.addEventListener('click', function() {
							self.composeSearchResult(i);
						});
						td.appendChild(compose);
					break;
				}
				trB.appendChild(td);
			}

			table.appendChild(trB)
		})

		this.streamBody.appendChild(table);
	},

	this.getStreamData = function(element = null) {
		if(!element) {
			element = document.querySelector('.tab.active');
		}
		if (element) {
			this.post({tabId: element.getAttribute('data-id')}, 'get_tab_data')
			.then(res => {
				var item = '';

				self.empty(this.streamBody);
				res = JSON.parse(res);

				if(res.result.length) {
					this.posts = res.result;
					if(res.type != 'search' && res.type != 'rss') {
						res.result.forEach((e, i) => {
							item += self.createHtml(e, i)
						})
						self.streamBody.innerHTML = item;
					}else if(res.type == 'search') {
						self.createSearchHtml(res.result);
					}else if(res.type == 'rss') {
						self.createRsshHtml(res.result);
					}

				}else {
					self.createInboxHtml(res.result);
				}
			})
		}
	},

	this.createHtml = function(obj, index) {
		var data = {};

		data.cretedAt = obj.created_at;
		data.text     = obj.text;
		data.userName = obj.user.name;
		data.postImg  = obj.extended_entities ? obj.extended_entities.media[0].media_url_https : `${this.bUrl}images/no-img.png`;
		data.userImg  = obj.user.profile_image_url_https;

		return `<div class="strem-item">
		    <div class="strem-item-header">
		    	<img src="${this.bUrl}images/updates/twitter.png">
		    	<span>Twitter</span>
		    </div>
		    <div class="strem-item-author">
		    	<img src="${data.userImg}">
		        <div class="strem-item-author-name">
		        	<span class="user-name">${data.userName}</span>
		            <div class="days-ago">${data.cretedAt}</div>
		        </div>
		    </div>
		    <div class="strem-item-body">
		        <div class="strem-item-text">${data.text}</div>
				<img src="${data.postImg}">
		    </div>
			<div class="strem-item-functions">
				<img src="${this.bUrl}images/buttons/noun_939691_cc.png" alt="" class="strem-item-function like" data-id=${index}>
				<img src="${this.bUrl}images/buttons/Shape.png" alt="" class="strem-item-function respond" data-id=${index}>
				<i class="fa fa-share Shape strem-item-function retweet" aria-hidden="true" data-id=${index}></i>
				<img src="${this.bUrl}images/buttons/group-4@3x.png" alt="" class="strem-item-function assign" data-id=${index}>
			</div>
		</div>`
	},

	this.createInboxHtml = function(obj) {
		console.log(obj)
		for(key in obj) {
			var item       = document.createElement('div'),
				headerImg  = document.createElement('img'),
				headerSpan = document.createElement('span'),
				header     = document.createElement('div'),
				message, sender, sImg, sName, recipient, rImg, rName, text, time, assign, assImg;

			item.classList.add('stream-dm-item');
			header.classList.add('stream-dm-item-header');
			headerImg.setAttribute('src', `${this.bUrl}images/updates/twitter.png`);
			headerSpan.innerHTML = 'Twitter';
			header.appendChild(headerImg);
			header.appendChild(headerSpan);
			item.appendChild(header);

			obj[key].forEach((e, i) => {
				message         = document.createElement('div');
				sender          = document.createElement('div');
				sImg            = document.createElement('img');
				sName           = document.createElement('span');
				recipient       = document.createElement('div');
				rImg            = document.createElement('img');
				rName           = document.createElement('span');
				text            = document.createElement('span');
				time            = document.createElement('span');
				assign			= document.createElement('div');
				assImg   	    = document.createElement('img');
				text.innerHTML  = e.text;
				time.innerHTML  = self.createDate(e.time);
				sName.innerHTML = e.sender_name;
				rName.innerHTML = e.recipient_name;
				assImg.key 		= {id: i, user: key};

				message.classList.add('stream-dm-item-message');
				sender.classList.add('stream-dm-item-sender');
				recipient.classList.add('stream-dm-item-recipient');
				text.classList.add('stream-dm-item-text');
				time.classList.add('stream-dm-item-time');
				assign.classList.add('stream-dm-item-assign')

				sImg.setAttribute('src', e.sender_image);
				rImg.setAttribute('src', e.recipient_image);
				assImg.setAttribute('src', `${self.bUrl}images/buttons/group-4@3x.png`);

				assImg.addEventListener('click', e => {
					self.teamUsers(e.target.key, obj);
				})

				sender.appendChild(sImg);
				sender.appendChild(sName);
				recipient.appendChild(rImg);
				recipient.appendChild(rName);
				assign.appendChild(assImg);
				message.appendChild(sender);
				message.appendChild(recipient);
				message.appendChild(time);
				message.appendChild(text);
				message.appendChild(assign);
				item.appendChild(message);
			})

			this.streamBody.appendChild(item);
		}
	},

	this.teamUsers = function(id, obj) {
		this.message = obj[id.user][id.id];
		document.getElementById('assignModal').click();

		this.post({team_id: this.activeTab.getAttribute('data-team'), type: 'message'}, 'get_assign_teammates')
		.then(res => {
			res = JSON.parse(res);
			var select = document.getElementById('assignmentUsers'),
				option;

			res.forEach(e => {
				option = document.createElement('option');

				option.value     = e.user_id;
				option.innerHTML = e.user_name; 

				select.appendChild(option);
			})
		})
	},

	this.assignUser = function() {
		var data = {
			user_id: document.getElementById('assignmentUsers').value,
			message_id: self.message.message_id,
			tab_id: document.querySelector('.tab.active').getAttribute('data-id')
		}
		self.post(data, 'assign_message')
		.then(res => {
			modal.closeModal();
		})
	},

	this.createDate = function(time) {
		var date = new Date(+time);

		return `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()} ${date.getHours()}:${date.getMinutes()}`;
	},

	this.opneTabs = function() {
		var dTab = document.getElementById('dropDownTab'), style = getComputedStyle(dTab);

		if(style.display == 'flex') {
			dTab.style.display = 'none';
		}else {
			dTab.style.display = 'flex';
		}
	},

	this.addTab = function() {
		var tabName = document.getElementById('tab-name').value;

		if (tabName) {
			self.post({tabName: tabName}, 'add_tab')
			.then(res => {
				res = JSON.parse(res);
				if (!res.error) {
					var newTab  = document.createElement('li'),
						visible = document.getElementById('visibleTab');

					newTab.classList.add('tab');
					newTab.setAttribute('data-id', res.id);
					newTab.innerHTML = res.tab_name;
					newTab.setAttribute('data-stream', '0');
					count = document.querySelectorAll('.tab').length;
					newTab.addEventListener('click', () => {self.changeTab(newTab, count)});
					if (document.getElementById('dropDownTab')) {
						document.getElementById('dropDownTab').appendChild(newTab);
					} else if(visible) {
						if(visible.children.length == 7) {
							var span = document.createElement('span'),
								ul   = document.createElement('ul');
							
							span.addEventListener('click', e => {
								self.opneTabs.call(e.target);
							})
							span.innerHTML = '>>';
							span.id 	   = 'dTabToggle';
							ul.id 		   = 'dropDownTab';
							visible.parentNode.insertBefore(span, document.getElementById('streamButtonsContainer'));
							ul.appendChild(newTab);
							visible.parentNode.insertBefore(ul, document.getElementById('streamButtonsContainer'));
						}else {
							document.getElementById('visibleTab').appendChild(newTab);
						}
					} else {
						window.location.reload();
					}
				}else {
					alert(res.error);
				}
				modal.closeModal();
			})
		}
	},

	this.teamSearch = function() {
		if (!this.value) {
			self.empty(document.getElementById('teamList'), 0);
			return;
		}
		self.post({sQuery: this.value}, 'get_manager_teams')
		.then(res => {
			self.findTeams = JSON.parse(res);
			self.setTeamList();
		})
	},

	this.setTeamList = function() {
		if(this.findTeams) {
			var lContainer = document.getElementById('teamList'), span, li; self.empty(lContainer, 0);

			this.findTeams.forEach((e, i) => {
				li   		   = document.createElement('li');
				span 		   = document.createElement('span');
				span.innerHTML = e.team_name;

				li.setAttribute('data-id', i);
				li.appendChild(span);
				lContainer.appendChild(li);
				li.addEventListener('click', e => {
					self.chooseTeam.call(e.target, lContainer)
				})
			})
		}
	},

	this.chooseTeam = function(lContainer) {
		var searchInput = document.getElementById('teamSearch'),
			user 		= self.findTeams[this.getAttribute('data-id')?this.getAttribute('data-id'):this.parentNode.getAttribute('data-id')];

		searchInput.value = user.team_name;
		self.empty(lContainer, 0);
	},

	this.addTeam = function() {
		var team = document.getElementById('teamSearch').value;

		if(team) {
			self.post({team: team, tabId: self.activeTab.getAttribute('data-id')}, 'assign_team')
			.then(res => {
				res = JSON.parse(res);
				if (res.success) {
					document.getElementById('asignTeam').remove();
					document.getElementsByClassName('active')[0].setAttribute('data-team', res.id);
				}
				modal.closeModal();
			})
		}
	},

	this.empty = function(elem) {
		var child = elem.lastElementChild;

		while (child) { 
		    elem.removeChild(child); 
		    child = elem.lastElementChild; 
		}
	},

	this.checkFuncType = function(type) {
		self.posts[this.getAttribute('data-id')].id
		if (type != 'assign') {
			self.post({postId: self.posts[this.getAttribute('data-id')].id_str, type: type}, 'check_func_type')
			.then(res => {
				console.log(res);
			})
		}else {
			self.message = {message_id: self.posts[this.getAttribute('data-id')].id_str}
			document.getElementById('assignModal').click();
			self.post({team_id: self.activeTab.getAttribute('data-team'), type: 'post'}, 'get_assign_teammates')
			.then(res => {
				res = JSON.parse(res);
				var select = document.getElementById('assignmentUsers'),
					option;

				res.forEach(e => {
					option = document.createElement('option');

					option.value     = e.user_id;
					option.innerHTML = e.user_name; 

					select.appendChild(option);
				})
			})
		}
	},

	this.respondTweet = function(type) {
		document.querySelector('[data-type="post"]').click();
		document.querySelector('[data-soc="twitter"]').click();
		document.getElementById('social_network_list').style.display = 'none';
		document.getElementById('smPostContent').value = `@${self.posts[this.getAttribute('data-id')].user.screen_name} `
	},

	this.bindEvents = function() {
		var events = {
			addTab: {
				value: document.getElementById('add_tab'),
				event: 'click'
			},
			opneTabs: {
				value: document.getElementById('dTabToggle'),
				event: 'click'
			},
			addTeam: {
				value: document.getElementById('addTeamToTab'),	
				event: 'click'
			}, 
			teamSearch: {
				value: document.getElementById('teamSearch'),
				event: 'input'
			},
			assignUser: {
				value: document.getElementById('assignTeam'),
				event: 'click'
			}
		}, onClick = {
			checkFuncType: {
				targets: ['like', 'retweet', 'assign'],
				by: 'classList'
			},
			respondTweet: {
				targets: ['respond'],
				by: 'classList'
			}
		}

		for(key in events) {
			if (events[key].value) {
				events[key].value.key = key;
				events[key].value.event = events[key].event;
				events[key].value.addEventListener(events[key].value.event, e => {
					self[e.target.key].call(e.target);
				});
			}
		}

		document.addEventListener('click', e => {
			for(key in onClick) {
				for(let i = 0; i < onClick[key].targets.length; i++) {
					switch(onClick[key].by){
						case 'classList':
							if(Object.values(e.target[onClick[key].by]).includes(onClick[key].targets[i])) {
								self[key].call(e.target, onClick[key].targets[i])
							}
						break;
					}
				}
			}
		})

	}
}

document.addEventListener("DOMContentLoaded", function() {
	var stream = new Stream();stream.init();
})