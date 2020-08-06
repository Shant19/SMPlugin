function Modal(container, content) {
	const self 		    = this;

	this.modalContainer = document.getElementsByClassName(container),
	this.modalContent   = document.getElementsByClassName(content),
	this.bUrl           = document.getElementById('base_url').getAttribute('data-url'),
	this.smList			= true,
	this.element,
	this.type,


	this.addUser = function() {
		if (this.getAttribute('data-type') == null) {
			self.type = this.parentElement.getAttribute('data-type');
		} else {
			self.type = this.getAttribute('data-type')
		}
		self.animation();
	},

	this.animation = function() {
		for (var i = 0; i < this.modalContent.length; i++) {
			if(this.modalContent[i].getAttribute('data-type') == this.type) {
				this.element = 	this.modalContent[i];
			}
		}

		this.element.style.display = 'grid';
		this.modalContainer[0].style.display = 'flex';
		this.modalContainer[0].style.opacity = 0;

		var i = 0, k = window.setInterval(function() {
		    if (i > 10) {
				clearInterval(k);
		    } else {
				self.modalContainer[0].style.opacity = i / 5;
				i++;
		    }
		}, 20), c = document.querySelectorAll("[data-modal='closeModal']");

		c.forEach((e) => {
			e.addEventListener('click', self.closeModal);	
		})

		this.modalContainer[0].addEventListener('click', (e) => {
			if(e.target.getAttribute("class") == 'sm-modal-container') {
				self.closeModal();
			}
		})
	},

	this.closeModal = function() {
		self.element.style.display = 'none';
		self.modalContainer[0].style.display = 'none';
		document.getElementById('social_network_list').style.display = 'none';
		self.smList = true;

	},

	this.showText = function() {
		let value =  this.value
		document.querySelectorAll('.cm-font-sm-bold').forEach(function (e) {
			 e.innerHTML = value;
		})
	},

	this.showImage = function() {
		let src =  this.value
		document.querySelectorAll('.preview_image').forEach(function (e) {
			 e.innerHTML = "<img src="+src+" width='100%'/>";
		})
	},

	this.changeStreamTAb = function() {
		document.getElementsByClassName('active-tab')[0].classList.remove('active-tab');
		this.classList.add('active-tab');
		document.getElementsByClassName('stream-active-body')[0].classList.remove('stream-active-body');
		document.querySelector(`.stream-tab-body[data-type=${this.getAttribute('data-type')}]`).classList.add('stream-active-body');
	},

	this.changeStreamType = function() {
		document.getElementsByClassName('active-type')[0].classList.remove('active-type');
		this.classList.add('active-type');
	},

	this.openSocialBody = function() {
		var id = this.getAttribute('data-type'),
			showingBlock = document.querySelector(`[data-id="${id}"]`),
			buttons = document.getElementsByClassName('cm-social-btn'),
			modalTexts = document.getElementsByClassName('cm-social-body');

		for (var i = 0; i < buttons.length; i++) {
			buttons[i].classList.remove('active-social-btn');
			if (modalTexts[i]) {
				modalTexts[i].style.display = 'none';
			}
		}

		this.classList.add('active-social-btn');
		if (id != 'stream') {
			showingBlock.style.display = 'block';
		}
	},

	this.removeModalAvatar = function() {
		let socid = this.getAttribute('data-remove')
		document.querySelector(`.panel-info[data-id="${socid}"]`).setAttribute('data-active','inactive')
		document.querySelector(`.cm-avatar-item[data-id="${socid}"]`).setAttribute('data-active','inactive')
	},

	this.openSmList = function() {
		var smlContainer = document.getElementById('social_network_list');

		this.smList ? (smlContainer.style.display = 'flex', this.smList = false) : (smlContainer.style.display = 'none', this.smList = true);
	},

	this.removeModalAvatar = function (element) {
		element.parentNode.remove()
	},

	this.chooseSmNetwork = function() {
		var smType    = this.getAttribute('data-soc'),
			container = document.getElementsByClassName('cm-avatar-box'),
			prPhoto   = document.getElementById('get-twitter-profile').getAttribute('src'),
			div	      = document.createElement('div'),
			sImg      = document.createElement('img'),
			cImg      = document.createElement('img'),
			child	  = container[0].lastElementChild,
			close     = document.createElement('i');

		while (child) { 
		    container[0].removeChild(child); 
		    child = container[0].lastElementChild; 
		}
		
		div.id = 'smPostType';
		div.setAttribute('data-smType', smType);
		div.classList.add('cm-avatar-item');
		sImg.setAttribute('src', prPhoto);
		sImg.classList.add('top-avatar-img');
		cImg.setAttribute('src', `${self.bUrl}images/calendar/${smType}-logo.png`);
		cImg.classList.add('avatar-item-social-icon');
		close.classList.add('fa');
		close.classList.add('fa-remove');
		close.classList.add('cm-modal-avatar-remove');

		close.addEventListener('click', e => {self.removeModalAvatar(e.target)})

		div.appendChild(sImg);
		div.appendChild(cImg);
		div.appendChild(close);

		container[0].appendChild(div);

	}


}

const modal         = new Modal('sm-modal-container', 'sm-modal-content'),
	  smList        = document.getElementById('openSmList'),
	  smPostContent = document.getElementById('smPostContent'),
	  smPostImg     = document.getElementById('smPostImg'),
	  cmSocialBtn   = document.getElementsByClassName('cm-social-btn'),
	  smItem        = document.getElementsByClassName('social_network_item'),
	  streamTabs    = document.getElementsByClassName('stream-tab-item'),
	  streamType	= document.getElementsByClassName('stream-type'),
	  modals        = document.querySelectorAll("[data-modal='openModal']");

setTimeout(() => {
	modals.forEach(function (e) {e.addEventListener('click', el => {modal.addUser.call(el.target)})})
}, 100)
smPostContent.addEventListener('input', function() {modal.showText.call(this)})
smPostImg.addEventListener('input', function() {modal.showImage.call(this)})
smList.addEventListener('click', function() {modal.openSmList()})

Object.values(cmSocialBtn).forEach(e => {e.addEventListener('click', function() {modal.openSocialBody.call(this)})})
Object.values(smItem).forEach(e => {e.addEventListener('click', function() {modal.chooseSmNetwork.call(this)})})
Object.values(streamTabs).forEach(e => {e.addEventListener('click', function() {modal.changeStreamTAb.call(this)})})
Object.values(streamType).forEach(e => {e.addEventListener('click', function() {modal.changeStreamType.call(this)})})
