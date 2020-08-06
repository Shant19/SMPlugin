function Assignments(){
	const self = this;


	this.Http   		 = new XMLHttpRequest(),
	this.bUrl   		 = document.getElementById('base_url').getAttribute('data-url'),
	this.apiUrl 		 = `${this.bUrl}includes/actions/actions.php`,
	this.activeTab       = document.querySelector('.ass-tab-item.active'),
	this.body      		 = document.getElementById('assBody'),
	this.streams         = [],

	this.init = function() {
		this.bindEvents();
		this.setActiveTab();
		this.checkTab();
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
		var tabs = document.getElementsByClassName('ass-tab-item');

		Object.values(tabs).forEach(e => {
			e.addEventListener('click', e => {
				this.activeTab.classList.remove('active');
				e.target.classList.add('active');
				this.activeTab = e.target;
				self.checkTab();
			})
		})
	},

	this.empty = function(elem) {
		var child = elem.lastElementChild;

		while (child) { 
		    elem.removeChild(child); 
		    child = elem.lastElementChild; 
		}
	},

	this.checkTab = function() {
		this.post({tabType: this.activeTab.getAttribute('data-type')}, 'get_assigment_data')
		.then(res => {
			res = JSON.parse(res)
			self.streams = res;
			self.empty(this.body);

			if(!res.type) {
				if (res.length) {
					var ul = document.createElement('ul'), li;

					ul.classList.add('ass-tab-container');
					res.forEach((e, i) => {
						li = document.createElement('li');

						li.setAttribute('data-id', i);
						li.classList.add('ass-stream-tabs');
						li.innerHTML = `${e.tab_name}(${e.manager})`
						li.addEventListener('click', e => {
							self.changeStreamTab(e.target);
						})
						ul.appendChild(li);
					})
					this.body.appendChild(ul);
				}
			}else {
				var table    = document.createElement('table'),
					header   = ['Team Lead', 'Team name', 'Assigned date'],
					trHeader = document.createElement('tr'),
					td;

				trHeader.classList.add('ass-team-header')
				table.classList.add('ass-team-table')

				for(let i = 0; i < header.length; i++) {
					td = document.createElement('td');
					td.innerHTML = header[i];
					trHeader.appendChild(td);
				}
				table.appendChild(trHeader);
				this.createFields(res.result ,table);
				this.body.appendChild(table);
			}
		})
	},

	this.createFields = function(obj, table) {
		obj.forEach(e => {
			var tr 	  = document.createElement('tr'),
				td, img, name, fname;

			for(let i = 0; i < 3; i++) {
				td    = document.createElement('td');
				switch (i) {
					case 0:
						img   = document.createElement('img');
						name  = document.createElement('span');
						fname = document.createElement('span');

						img.setAttribute('src', e.picture);
						fname.classList.add('ass-team-fname');

						fname.innerHTML = e.fname;
						name.innerHTML  = `@${e.username}`;

						td.appendChild(img);
						td.appendChild(fname);
					break;
					case 1:
						name 			= document.createElement('span');
						name.innerHTML  = e.team_name;
					break;
					case 2:
						name 		   = document.createElement('span');
						name.innerHTML = self.createDate(e.created_at * 1000);
					break;
				}
				name.classList.add('ass-team-info')

				td.appendChild(name);
				tr.appendChild(td);
			}

			tr.classList.add('ass-team-body');
			table.appendChild(tr)
		})
	},

	this.changeStreamTab = function(e) {
		var activeTab = document.querySelector('.ass-stream-tabs.active'),
			div       = document.createElement('div'),
			remove    = document.getElementsByClassName('ass-stream-body')[0],
			item 	  = '';

		activeTab && activeTab.classList.remove('active');
		remove && remove.remove();
		e.classList.add('active');
		div.classList.add('ass-stream-body');
		this.streams[e.getAttribute('data-id')].assignments = true;

		this.post(this.streams[e.getAttribute('data-id')], 'get_assign_stream_data')
		.then(res => {
			res = JSON.parse(res);
			this.response = res;
			if (res.type != 'inbox') {
				res.result.forEach((e, index) => {
					item += self.createHtml(e, index);
				})
				div.innerHTML = item;
				self.body.appendChild(div);
			}else {
				self.createInboxHtml(res.result, div);
				self.body.appendChild(div);
			}
		})
	},

	this.createHtml = function(obj, index) {
		var data = {}, string;

		data.cretedAt = obj.created_at;
		data.text     = obj.text;
		data.userName = obj.user.name;
		data.postImg  = obj.extended_entities ? obj.extended_entities.media[0].media_url_https : `${this.bUrl}images/no-img.png`;
		data.userImg  = obj.user.profile_image_url_https;

		switch(obj.role) {
			case 'repost':
				string = `<i class="fa fa-share Shape strem-item-function retweet" aria-hidden="true" data-id=${index}></i>`;
			break;
			case 'send':
				string = `<img src="${this.bUrl}images/buttons/Shape.png" alt="" class="strem-item-function respond" data-id=${index}>`;
			break;
			case 'all':
				string = `<img src="${this.bUrl}images/buttons/Shape.png" alt="" class="strem-item-function respond" data-id=${index}>
						  <i class="fa fa-share Shape strem-item-function retweet" aria-hidden="true" data-id=${index}></i>`
			break;
		}

		if(obj.role == '') {
			string = `<i class="fa fa-share Shape strem-item-function retweet" aria-hidden="true" data-id=${index}></i>`;
		}else

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
		    <div class="strem-item-functions">${string}</div>
		</div>`
	},

	this.createInboxHtml = function(obj, div) {
		for(key in obj) {
			var item       = document.createElement('div'),
				headerImg  = document.createElement('img'),
				headerSpan = document.createElement('span'),
				header     = document.createElement('div'),
				message, sender, sImg, sName, recipient, rImg, rName, text, time, respond, respondImg;



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
				respond			= document.createElement('div');
				respondImg		= document.createElement('img');
				text.innerHTML  = e.text;
				time.innerHTML  = self.createDate(e.time);
				sName.innerHTML = e.sender_name;
				rName.innerHTML = e.recipient_name;

				message.classList.add('stream-dm-item-message');
				sender.classList.add('stream-dm-item-sender');
				recipient.classList.add('stream-dm-item-recipient');
				text.classList.add('stream-dm-item-text');
				time.classList.add('stream-dm-item-time');
				respond.classList.add('stream-dm-item-respond');

				respondImg.setAttribute('src', `${this.bUrl}images/buttons/Shape.png`)
				sImg.setAttribute('src', e.sender_image);
				rImg.setAttribute('src', e.recipient_image);

				respondImg.key = i;
				respondImg.addEventListener('click', e => {
					self.answerMessage(obj, e.target.key, key);
				})

				sender.appendChild(sImg);
				respond.appendChild(respondImg);
				sender.appendChild(sName);
				recipient.appendChild(rImg);
				recipient.appendChild(rName);
				message.appendChild(sender);
				message.appendChild(recipient);
				message.appendChild(time);
				message.appendChild(text);
				message.appendChild(respond);
				item.appendChild(message);
			})

			div.appendChild(item);
		}
	},

	this.answerMessage = function(obj, index, key) {
		document.querySelector('[data-type="post"]').click();
		document.getElementById('smPostContent').value = `@${obj[key][index].sender ? obj[key][index].recipient_name : obj[key][index].sender_name} `
	},

	this.openMessageModal = function(el) {
		var mModal = document.getElementById('messageModal');

		el.classList.add('active');
		mModal.click();
	},

	this.sendMessage = function() {
		var text    = document.getElementById('messageText'),
			active  = document.querySelector('.stream-dm-send.active'),
			example = self.response.result[active.getAttribute('data-id')][0],
			manager = example.sender_id == active.getAttribute('data-id') ? example.recipient_id : example.sender_id;


		active.classList.remove('active');
		self.post({text: text.value, manager: manager, recipient: active.getAttribute('data-id')}, 'send_twitter_message')
		.then(res => {
			console.log(res)
		})
	},

	this.retweet = function(type) {
		self.post({postId: self.response.result[this.getAttribute('data-id')].id_str, type: type}, 'check_func_type')
		.then(res => {
			console.log(res);
		})
	},

	this.respondTweet = function(type) {
		document.querySelector('[data-type="post"]').click();
		document.querySelector('[data-soc="twitter"]').click();
		document.getElementById('social_network_list').style.display = 'none';
		document.getElementById('smPostContent').value = `@${self.response.result[this.getAttribute('data-id')].user.screen_name} `
	},

	this.createDate = function(time) {
		var date = new Date(+time);

		return `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()} ${date.getHours()}:${date.getMinutes()}`;
	},

	this.bindEvents = function() {
		var events = {
			sendMessage: {
				value: document.getElementById('sendMessage'),
				event: 'click'
			}
		}, onClick = {
			retweet: {
				targets: ['retweet'],
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
	var assignments = new Assignments();assignments.init();
})