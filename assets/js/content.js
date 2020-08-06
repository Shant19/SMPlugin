document.addEventListener("DOMContentLoaded", function() {

var elements = document.querySelectorAll("[rel='stylesheet']"); 

elements.forEach(e => {
	var dataId = e.getAttribute('data-id'), id = e.id;
	if (dataId != 'ki-publish' && id != 'dashicons-css' && id != 'admin-bar-css' && id != 'wp-block-library-css') {
		e.setAttribute('disabled', '')
	}
})

var container = document.getElementById('primary'),
getSiblings = function (elem) {
	var sibling = elem.parentNode.childNodes;
	sibling.forEach(e => {
		if(e.id !== 'primary' && e.nodeType === 1) {
			e.style.display = 'none'
		}
	})
};

getSiblings(container);

function Content() {
	const self = this;

	this.Http   = new XMLHttpRequest(),
	this.bUrl   = document.getElementById('base_url').getAttribute('data-url'),
	this.apiUrl = `${this.bUrl}includes/actions/actions.php`,
	this.posts  = [],
	this.event  = false, 
	this.pTime,
	this.pType,

	this.dropdownToggle = function() {
		var id 			  = this.getAttribute('data-toggle'),
			closedElement = id == 'dropdownReminder' ? document.getElementById('dropdownSettings') : document.getElementById('dropdownReminder'),
			element 	  = document.getElementById(id); 

		closedElement.classList.remove("active");

		if (element.classList.contains('active')) {
			element.classList.remove("active");
		} else {
			element.classList.add("active");
		}
	},

	this.getBase64Image = function(imgUrl, callback) {
	    var img = new Image();

	    img.onload = function(){
			var canvas = document.createElement("canvas");

			canvas.width  = img.width;
			canvas.height = img.height;

			var ctx = canvas.getContext("2d");

			ctx.drawImage(img, 0, 0);

			var dataURL = canvas.toDataURL("image/png");

			callback(dataURL);
	    };

	    img.setAttribute('crossOrigin', 'anonymous');
	    img.src = imgUrl;

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

	this.addBufferProfiles = function(data) {
		var select = document.createElement('select'),
			sType = document.getElementById('cKeywords'),
			choose;

		select.setAttribute('id', 'bufferS');
		select.classList.add('inp');
		choose = document.createElement('option');
		choose.value = choose.innerHTML = 'Choose...';
		choose.setAttribute('disabled', '');
		choose.setAttribute('selected', '');
		select.appendChild(choose);
		data.forEach(e => {
			let opt = document.createElement('option');

			opt.value     = e.profileId;
			opt.innerHTML = `${e.serviceName.charAt(0).toUpperCase()}${e.serviceName.slice(1)}`;
			select.appendChild(opt);
		})
		sType.parentNode.insertBefore(select, sType)
	},

	this.getAllPosts = function(profileId) {
		var action = 'get_all_buffer_posts',
			data   = {prID: profileId};	

		this.post(data, action)
		.then(res => {
			res = JSON.parse(res);
			this.posts = res.updates;
			this.setCalendarData();
		})
	},

	this.setCalendarData = function() {
		var items = document.getElementsByClassName('calendar-items');
		Object.values(items).forEach(e => {
			if (!this.event) {
				e.addEventListener('click', function() {
					if (self.checkSmType()) {
						self.getUpdates.call(this);
					} else {
						self.filterTwPosts.call(this);
					}
				});
			}
			var child = e.children[0].lastElementChild; 

	        while (child) { 
	            e.children[0].removeChild(child); 
	            child = e.children[0].lastElementChild; 
	        } 
		})

		this.event = true;

		this.posts.forEach((e, i) => {
			var date   = this.checkSmType() ? new Date(e.created_at * 1000) : this.pType == 'vk' ? new Date(e.date * 1000) : this.pType == 'hootsuite' ? new Date(e.scheduledSendTime) : new Date(e.created_at),
				img    = document.createElement('img'),
				dataId = `${date.getUTCFullYear()}-${date.getUTCMonth() + 1}-${date.getDate()}`,
				pId    = this.checkSmType() ? e.id : i,
				span   = document.querySelector(`[data-id='${dataId}']`);

			e.dateId = dataId;

			if (span) {
				e.profile_service = this.checkSmType() ? e.profile_service : this.pType == 'vk' ? 'vk' : this.pType == 'hootsuite' ? 'hootsuite' : 'twitter';
				img.setAttribute('data-pid', pId);
				img.setAttribute('src', `${this.bUrl}images/calendar/${e.profile_service}-logo.png`);
				span.children[0].appendChild(img);
			}
		})
	},

	this.checkSmType = function() {
		return this.pType == 'buffer' ? true : false;
	},

	this.filterTwPosts = function() {
		var elements  	 = this.children[0].children,
			currentPosts = [];

		Object.values(elements).forEach(e => {
			currentPosts.push(self.posts[e.getAttribute('data-pid')]);
		})
		self.showUpdates(currentPosts);
	},

	this.getUpdates = function() {
		var updatesId = [],
			action	  = 'get_buffer_updates',
			elements  = this.children[0].children;

		Object.values(elements).forEach(e => {
			updatesId.push(e.getAttribute('data-pid'));
		})

		self.post(updatesId, action)
		.then(res => {
			res = JSON.parse(res);
			self.showUpdates(res);
		})

	},

	this.showUpdates = function(updates) {
		var updatesContainer = document.getElementById('smUpdates'),
			headerColors	 = {twitter: '#36c7f6', instagram: '#DD2A7B', linkedin: '#0082c0', vk: '#5282b8', hootsuite: '#000'},
			child			 = updatesContainer.lastElementChild;

		while (child) { 
		    updatesContainer.removeChild(child); 
		    child = updatesContainer.lastElementChild; 
		}

		updates.forEach(e => {
			var update       = document.createElement('div'),
				updateHeader = document.createElement('div'),
				headerImg    = document.createElement('img'),
				headerSpan   = document.createElement('span'),
				updateBody   = document.createElement('div'),
				udateAuthor  = document.createElement('div'),
				authorImg    = document.createElement('img'),
				authorName   = document.createElement('div'),
				userName     = document.createElement('span'),
				daysAgo      = document.createElement('div'),
				updateText   = document.createElement('div'),
				updateImg    = document.createElement('div'),
				updatePhoto  = document.createElement('img');

			update.classList.add('updates-item');
			updateHeader.classList.add('update-header');
			updateBody.classList.add('update-body');
			udateAuthor.classList.add('update-author');
			authorName.classList.add('update-author-name');
			userName.classList.add('user-name');
			daysAgo.classList.add('days-ago');
			updateText.classList.add('update-text');
			updateImg.classList.add('update-img');

			if(!this.checkSmType()) {
				e.media = {};
				e.text_formatted  = e.text;

				switch(this.pType){
					case 'twitter':
				    	e.profile_service = 'twitter';
				    	e.media.picture	  = e.extended_entities ? e.extended_entities.media[0].media_url_https : '';
						e.user.gravatar   = e.user.profile_image_url_https;
						e.due_time        = e.created_at;
				    break;
				    case 'vk':
				    	e.profile_service = 'vk';
				    	e.user 			  = {};
				    	e.media.picture	  = e.attachments ? e.attachments[0].link ? e.attachments[0].link.url : e.attachments[0].photo.sizes[e.attachments[0].photo.sizes.length - 1].url : '';
				    	e.user.gravatar	  = document.getElementById('vk-user').children[0].getAttribute('src');
				    	e.user.name       = document.getElementById('vkUserName').value;
				    	e.due_time		  = e.dateId;
					case 'hootsuite':
				    	e.profile_service = 'hootsuite';
				    	e.user 			  = {};
				    	e.media			  = {};
				    	e.media.picture	  = e.mediaUrls[0] ? e.mediaUrls[0].url ? e.mediaUrls[0].url : '' : '';
				    	e.user.gravatar	  = document.getElementById('hootsuite-user').children[0].getAttribute('src');
				    	e.user.name       = document.getElementById('hoUserName').value;
				    	e.due_time		  = e.dateId;
					break;
				}
			}

			headerImg.setAttribute('src', `${this.bUrl}images/updates/${e.profile_service}.png`);
			authorImg.setAttribute('src', e.user.gravatar);

			headerSpan.innerHTML 		  = `${e.profile_service.charAt(0).toUpperCase()}${e.profile_service.slice(1)}`;
			userName.innerHTML   		  = e.user.name;
			daysAgo.innerHTML    		  = e.due_time;
			updateText.innerHTML 		  = e.text_formatted;
			updateHeader.style.background = headerColors[e.profile_service];


			updateHeader.appendChild(headerImg);
			updateHeader.appendChild(headerSpan);

			udateAuthor.appendChild(authorImg);
			udateAuthor.appendChild(authorImg);
			authorName.appendChild(userName);
			authorName.appendChild(daysAgo);
			udateAuthor.appendChild(authorName);

			if (this.checkPicture(e.media)) {
				updatePhoto.setAttribute('src', e.media.picture)
				updateImg.appendChild(updatePhoto);
			}

			updateBody.appendChild(udateAuthor);
			updateBody.appendChild(updateText);
			updateBody.appendChild(updateImg);

			update.appendChild(updateHeader);
			update.appendChild(updateBody);

			updatesContainer.appendChild(update);
		})
	},

	this.checkPicture = function(media) {
		if (!media || !media.picture) return false;

		var picture = media.picture.split('&');

		if(picture.length == 2) return false;
		else return true;
	},

	this.addTwPost = function() {
		var text   = document.getElementById('smPostContent'),
			img    = document.getElementById('smPostImg'),
			bHour  = new Date();
			date   = document.getElementById('yourDatePicker').value,
			type   = document.getElementById('smPostType').getAttribute('data-smtype'),
			action = {twitter: 'add_twitter_post', linkedin: 'add_linkedin_post', vk: 'add_vk_post', schedule: 'add_schedule_post'},
			data   = {};

		data.tPost  = text.value.replace(/&/ig, '%26');
		data.tPhoto = img.value.replace(/&/ig, '%26');

		if(self.pTime == 'schedule') {
			data.media = type;
			data.date  = date;
			data.bHour = bHour.getHours();

			self.post(data, action['schedule'])
			.then(res => {
				res = JSON.parse(res);
				window.location.reload();
			})
		}else {
			if (action[type]) {
				self.post(data, action[type])
				.then(res => {
					res = JSON.parse(res);
					window.location.reload();
				})
			}
		}
		
	},

	this.getTwUpdates = function() {
		var action = 'get_all_twitter_posts',
			data   = {};

		this.post(data, action)
		.then(res => {
			res = JSON.parse(res);
			this.posts = res;
			this.setCalendarData();
		})
	},

	this.getInUpdates = function() {
		var action = 'get_all_linkedin_posts',
			data   = {};

		this.post(data, action)
		.then(res => {
			res = JSON.parse(res);
			this.posts = res;
			// this.setCalendarData();
		})
	},

	this.getVkUpdates = function() {
		var action = 'get_all_vk_posts',
			data   = {};

		this.post(data, action)
		.then(res => {
			res = JSON.parse(res);
			this.posts = res;
			this.setCalendarData();
		})	
	},

	this.addHPost = function() {
		var data    	  = {},
			profile       = document.getElementById('hProfile').value.split('-'),
			hPost    	  = document.getElementById('hPost'),
			hPhoto   	  = document.getElementById('hPhoto'),
			action		  = 'add_hootsuite_post';
			data.hPost    = hPost.value;
			data.hPhoto   = hPhoto.value;
			data.hProfile = profile[0];

		this.post(data, action)
		.then(res => {
			bPost.value = bPhoto.value = bMedia.value = '';
			res = JSON.parse(res);
			console.log(res);
		});		
	},

	this.getHootsuiteProfiles = function() {
		this.post({}, 'get_hootsuite_profiles')
		.then(res => {
			res = JSON.parse(res);
			this.addHootsuiteProfiles(res);
		})
	},

	this.removeVkAccount = function(el) {
		this.post({}, 'remove_vk_account')
		.then(res => {
			console.log(JSON.parse(res));
			el.remove();
		})
	},

	this.rmInAcc = function (el) {
		this.post({}, 'remove_linkedin_account')
		.then(res => {
			console.log(JSON.parse(res));
			el.remove();
		})
	},

	this.rmHoAcc = function (el) {
		this.post({}, 'remove_hootsuite_account')
		.then(res => {
			console.log(JSON.parse(res));
			el.remove();
		})
	},

	this.addHootsuiteProfiles = function(profiles) {
		var select = document.createElement('select'),
			sType  = document.getElementById('cKeywords'),
			choose = document.createElement('option');

		select.setAttribute('id', 'hootsuiteS');
		select.classList.add('inp');
		choose.value = choose.innerHTML = 'Choose...';
		choose.setAttribute('disabled', '');
		choose.setAttribute('selected', '');
		select.appendChild(choose);
		select.addEventListener('change', (el) => {
			self.post({profileId: el.target.value}, 'get_all_hootsuite_posts')
			.then(res => {
				res = JSON.parse(res);
				self.posts = res;
				self.setCalendarData();
				// self.showUpdates(res);
			})
		})
		profiles.forEach(e => {
			let opt = document.createElement('option');

			opt.value     = e.profileId;
			opt.innerHTML = `${e.serviceName.charAt(0).toUpperCase()}${e.serviceName.slice(1)}`;
			select.appendChild(opt);
		})
		sType.parentNode.insertBefore(select, sType);
	},

	this.changePostType = function() {
		var date  = new Date(),
			month = date.getMonth() < 9 ? `0${date.getMonth() + 1}` : date.getMonth() + 1,
			hours = date.getHours() < 10 ? `0${date.getHours()}` : date.getHours(),
			minut = date.getMinutes() < 10 ? `0${date.getMinutes()}` : date.getMinutes(),
			day   = date.getUTCDate() < 10 ? `0${date.getUTCDate()}` : date.getUTCDate();

		self.pTime = this.id;

		if(this.id == 'schedule') {
			document.getElementById('dateTimePicker').style.display = 'block';
			document.getElementById('yourDatePicker').value = `${date.getFullYear()}-${month}-${day}T${hours}:${minut}`
		}else {
			document.getElementById('dateTimePicker').style.display = 'none';
		}
	},

	this.streamAction = function() {
		var streamType = document.querySelector('.stream-tab-item.active-tab').getAttribute('data-type'),
			data = {
			chatType: streamType == 'stream' && document.getElementsByClassName('active-type')[0].getAttribute('data-type'),
			searchPhrase: streamType == 'search' && document.getElementById('search_phrase').value,
			rssFeedName: streamType == 'rss' && document.getElementById('rss_feed_name').value,
			rssUrls: streamType == 'rss' && document.getElementById('rss_urls').value,
			socialMediaAccount: document.getElementsByClassName('active-social-btn')[0].getAttribute('data-social'),
			tabId: document.querySelector('.tab.active').getAttribute('data-id')
		};
		
		if((streamType == 'search' && data.searchPhrase) || (streamType == 'rss' && data.rssFeedName && data.rssUrls) || streamType == 'stream') {
			self.post(data, 'stream_action')
			.then(res => {
				window.location.reload();
			})
		}
		
	}

}

var content  = new Content(),
	elements = document.getElementsByClassName('dropdown-toggle'),
	addBPost = document.getElementById('bPostAdd'),
	addHPost = document.getElementById('hPostAdd'),
	addTPost = document.getElementById('addTwPost'),
	sType	 = document.getElementById('sType'),
	changeD  = document.getElementsByClassName('change-month'),
	rmVkAcc	 = document.getElementById('remove_vk_account'),
	rmInAcc	 = document.getElementById('remove_linkedin_account'),
	rmHoAcc  = document.getElementById('remove_hootsuite_account'),
	addStr   = document.getElementById('add_stream'),
	removeBu = document.getElementById('remove_buffer_account'),
	postType = document.querySelectorAll('[name="dateType"]');

Object.values(elements).forEach(e => {
	e.addEventListener('click', function(){
		content.dropdownToggle.call(this);
	})	
})

Object.values(changeD).forEach(e => {
	e.addEventListener('click', function() {
		content.event = false;
		if (content.posts.length != 0) content.setCalendarData();
	})
})

postType.forEach(e => {
	e.addEventListener('input', el => {
		content.changePostType.call(el.target);
	})
})

addStr.addEventListener('click', e => {
	content.streamAction.call(e.target);
})

if (rmVkAcc) {
	rmVkAcc.addEventListener('click', e => {
		e.stopPropagation();
		content.removeVkAccount(e.target.parentNode);
	})
}

if (rmInAcc) {
	rmInAcc.addEventListener('click', e => {
		e.stopPropagation();
		content.rmInAcc(e.target.parentNode);
	})
}

if (rmHoAcc) {
	rmHoAcc.addEventListener('click', e => {
		e.stopPropagation();
		content.rmHoAcc(e.target.parentNode);
	})
}


if (addBPost) {
	addBPost.addEventListener('click', function() {
		var data          = {},
			profile		  = document.getElementById('bProfile').value.split('-'),
			bPost    	  = document.getElementById('bPost'),
			bPhoto   	  = document.getElementById('bPhoto'),
			bMedia   	  = document.getElementById('bMedia'),
			action		  = 'add_buffer_post';
			data.bPost    = bPost.value;
			data.bPhoto   = bPhoto.value;
			data.bMedia	  = bMedia.value;
			data.bProfile = profile[0];

		content.post(data, action)
		.then(res => {
			bPost.value = bPhoto.value = bMedia.value = '';
			res = JSON.parse(res);
		});
	})
}

if (addHPost) {
	addHPost.addEventListener('click', function() {
		content.addHPost();
	})
}

if (removeBu) {
	removeBu.addEventListener('click', function() {
		console.log(this)
		var action = 'delete_buffer_account',
			data   = {},
			button = document.querySelector('[data-type="addBufferPost"]'),
		    elem   = document.getElementById('buffer-user');

		content.post(data, action)
		.then(res => {
			elem.parentNode.removeChild(elem);
			button.parentNode.removeChild(button);
		})
	})
}

if (sType) {
	sType.addEventListener('change', function() {
		content.pType = this.value;
		if (this.value == 'buffer') {
			var action 			 = 'get_buffer_profiles',
				removedHoElement = document.getElementById('hootsuiteS'),
				data   			 = {};

			removedHoElement ? removedHoElement.parentNode.removeChild(removedHoElement) : '';

			content.post(data, action)
			.then(res => {
				res = JSON.parse(res);
				content.addBufferProfiles(res);
			})
		} else if(this.value == 'twitter') {
			var removedBfElement = document.getElementById('bufferS'),
				removedHoElement = document.getElementById('hootsuiteS');

			content.getTwUpdates();
			removedBfElement ? removedBfElement.parentNode.removeChild(removedBfElement) : '';
			removedHoElement ? removedHoElement.parentNode.removeChild(removedHoElement) : '';
		} else if(this.value == 'vk') {
			var removedBfElement = document.getElementById('bufferS'),
				removedHoElement = document.getElementById('hootsuiteS');

			content.getVkUpdates();
			removedBfElement ? removedBfElement.parentNode.removeChild(removedBfElement) : '';
			removedHoElement ? removedHoElement.parentNode.removeChild(removedHoElement) : '';
		} else if(this.value == 'hootsuite') {
			var removedBfElement = document.getElementById('bufferS'),
				removedHoElement = document.getElementById('hootsuiteS');
			content.getHootsuiteProfiles();
			removedBfElement ? removedBfElement.parentNode.removeChild(removedBfElement) : '';
		}
	})
}

if (addTPost) {addTPost.addEventListener('click', function() {content.addTwPost.call(this)})}
document.addEventListener('change', e => {if (e.target && e.target.id == 'bufferS') {content.getAllPosts(e.target.value);}});

});