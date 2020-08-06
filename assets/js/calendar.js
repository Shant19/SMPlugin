function Calendar(id, myId) {
	const self = this;
	
	this.now 		   	   = new Date(),
	this.currentDay    	   = this.now.getDate(),
	this.currentMonth  	   = this.now.getMonth(),
	this.currentYear   	   = this.now.getFullYear(),
	this.calendarContainer = document.getElementById(id),
	this.monthYear     	   = document.getElementById(myId),

	this.months      = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	this.shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],

	this.getDaysInMonth = function(month, year) {
     	var date  = new Date(Date.UTC(year, month, 1)),
     		prevM = new Date(year, month, 0).getDate(),
     		days  = [];

     	while (date.getMonth() === month) {
     		var d = new Date(date)
        	days.push({
        		day: d.getDate(),
				week: d.getUTCDay(),
        		month: d.getUTCMonth() + 1,
        		year: d.getUTCFullYear(),
        		type: ''
			});
        	date.setDate(date.getDate() + 1);
     	}

     	var prevW = days[0].week;

     	for (let i = days[0].week - 1; i >= 0; i--) {
     		days.unshift({
        		day: prevM--,
				week: i,
        		month: month,
        		year: year,
        		type: 'month-prev'
			});
     	}

     	let len = 42 - days.length, week = days[days.length - 1].week + 1;

     	for (let i = 0; i < len; i++) {
     		week = week == 7 ? 0 : week;
     		days.push({
        		day: i + 1,
				week: week++,
        		month: month + 2,
        		year: year,
        		type: 'month-next'
			});
     	}

     	return days;
	},

	this.changeCalendar = function(type) {
		if (type == 'prev') {
			this.currentMonth == 0 ? ( this.currentMonth = 11, this.currentYear-- ) : this.currentMonth--;
		} else {
			this.currentMonth == 11 ? ( this.currentMonth = 0, this.currentYear++ ) : this.currentMonth++;
		}

		this.getCalendarDays(this.currentMonth, this.currentYear)
	},

	this.getCalendarDays = function(month, year) {
		days = this.getDaysInMonth(month, year);
		this.createCalendar(days);
	},

	this.createCalendar = function(days) {
		this.deleteChild();
		this.appendChild(days);
	},

	this.deleteChild = function() { 
        var child = this.calendarContainer.lastElementChild;
        while (child) { 
            this.calendarContainer.removeChild(child); 
            child = this.calendarContainer.lastElementChild; 
        } 
    },

    this.appendChild = function(days) {
    	this.monthYear.setAttribute('data-month', this.currentMonth)
    	this.monthYear.innerHTML = `${this.months[this.currentMonth]} ${this.currentYear}`;

    	days.forEach((e) => {
    		var node     = document.createElement("span"),
                nodeitem = document.createElement("div");

    		if(e.type != '') node.classList.add(e.type)
			else if(self.chooseDate != undefined) document.querySelector(`[data-id='${self.chooseDate}']`) ? document.querySelector(`[data-id='${self.chooseDate}']`).classList.add('selected') : null;
			else if(self.currentDay == e.day && (self.now.getMonth() + 1) == e.month) node.classList.add('selected')


    		node.classList.add("calendar-items");
    		node.innerHTML = e.day;
    		node.setAttribute('data-id', `${e.year}-${e.month}-${e.day}`);
            node.appendChild(nodeitem);
    		node.addEventListener('click', function() { self.selectDay.call(this)});
    		this.calendarContainer.appendChild(node);
    	})

    },

    this.selectDay = function() {
    	var elements = document.getElementsByClassName('calendar-items');
    	Object.values(elements).forEach((e) => {
    		e.classList.remove("selected");
    	});
    	self.chooseDate = this.getAttribute('data-id');
    	this.classList.add('selected');
    }


}

const calendar = new Calendar('calendarContainer', 'monthYear');

calendar.getCalendarDays(calendar.currentMonth, calendar.currentYear);

document.getElementById('prev')
.addEventListener('click', () => {
	calendar.changeCalendar('prev')	
})

document.getElementById('next')
.addEventListener('click', () => {
	calendar.changeCalendar('next')	
})