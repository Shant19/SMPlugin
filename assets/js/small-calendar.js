/**
 * smallcal 0.1.1
 * jQuery calendar widget/large view plugin with Bootstrap compatibility
 *
 * James Waples 2013
 *
 * https://github.com/jamwaffles/smallcal
 **/

(function(factory) {
	if(typeof define === 'function' && define.amd) {
		define(['jquery'], factory);
	} else {
		factory(jQuery);
	}
}(function($) {
	var defaults = {
		todayButton: true,		// Show the button to reset to today's date?
		showInput: true,		// Show input
		weekStart: 0,			// Start day of the week. 0 is Sunday, 6 for Saturday, 1 for Monday (default)
		widget: true,
		cellRatio: 1,
		format: 'd/m/y',
		footer: true,
		dayHeader: true,
		mode: 'widget',			// 'widget' (default), 'tiny', 'popup', 'page'
		animDuration: 200,
		transition: '',
		tableClasses: 'table table-condensed',
		hidden: true,
		setOnMonthChange: true,
		condensed: false
	};
	
	var now = new Date();

	var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
	var shortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
	var shortDays = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

	// Get the number of days in the current month (or next month if delta == 1, or prev month if delta == -1, etc)
	Date.prototype.daysInMonth = function(delta) {
		delta = delta === undefined ? 0 : delta;

		return new Date(this.getFullYear(), this.getMonth() + 1 + delta, 0).getDate();
	}

	// Is this date today? Can also test against another date when specifying `now`
	Date.prototype.isDay = function(day) {
		if(day === undefined) {
			day = new Date();
		}

		return this.getFullYear() == day.getFullYear() 
			&& this.getMonth() == day.getMonth() 
			&& this.getDate() == day.getDate();
	}

	Date.prototype.isValid = function() {
		return Object.prototype.toString.call(this) === "[object Date]" && !isNaN(this.getTime());
	}

	$.fn.smallcal = function(method) {
		// Private methods
		var pMethods = {
			drawCalendar: function(selectedDate, replace) {
				var options = $(this).data('options');

				selectedDate = selectedDate || now;

				if(replace !== undefined && replace == true) {
					this.empty();
				}

				pMethods.drawHeader(selectedDate, options).appendTo(this);

				var month = pMethods
					.drawMonth(selectedDate, options)
					.addClass('current');

				$('<div />')
					.addClass('smallcal-month')
					.html(month)
					.appendTo(this);

				pMethods.drawFooter(selectedDate, options).appendTo(this);

				$(this).data('smallcal', true);
				$(this).data('date', selectedDate);
				$(this).data('element', this);

				return this;
			},
			drawPopupCalendar: function(selectedDate, replace) {
				var options = $(this).data('options');

				selectedDate = selectedDate || now;

				var container, calendar;

				if($(this).parent('.smallcal-popup-wrapper').length) {
					container = $(this).parent();
					calendar = $(this).parent().find('.smallcal-popup');
					calendar.empty();
				} else {
					container = $('<div />').addClass('smallcal-popup-wrapper');
					calendar = $('<div />')
						.addClass('smallcal smallcal-popup')
						.width($(this).outerWidth(true));

					$(this).wrap(container);
				}

				$(this).after(calendar);

				if(options.hidden) {
					calendar.hide();
				}

				pMethods.drawHeader(selectedDate, options).appendTo(calendar);

				var month = pMethods
					.drawMonth(selectedDate, options)
					.addClass('current');

				$('<div />')
					.addClass('smallcal-month')
					.html(month)
					.appendTo(calendar);

				calendar.data('smallcal', true);
				calendar.data('date', selectedDate);
				calendar.data('element', this);
				calendar.data('options', options);

				$(this).parent().wrap($('<div />').addClass('smallcal-affix'));
				
				return this;
			},
			drawHeader: function(date, options) {
				var header = $('<div />').addClass('smallcal-header');
				var monthNames = options.shortMonths ? shortMonths : months;

				$('<button />')
					.addClass('prev-month change-month btn')
					.html('<')
					.appendTo(header);

				$('<span />')
					.addClass('month')
					.append('<div>' + monthNames[date.getMonth()] + ' ' + date.getFullYear() + '</div>')
					.appendTo(header);


				$('<button />')
					.addClass('next-month change-month btn')
					.html('>')
					.appendTo(header);


				return header;
			},
			drawMonth: function(date, options) {
				date = date || now;
				var monthStart = new Date(date.getFullYear(), date.getMonth(), 1, 0, 0, 0);
				var days = [];
				var rows = [];
				var table = $('<table />').addClass(options.tableClasses);

				var numPrevDays = monthStart.getDay() - options.weekStart;
				var numCurrentDays = date.daysInMonth();
				var numNextDays = 42 - numPrevDays - numCurrentDays;

				var daysInLastMonth = date.daysInMonth(-1);

				// Header
				if(options.dayHeader) {
					var tableHeader = $('<tr />');

					for(var i = 0; i <= 6; i++) {
						var day = i + options.weekStart;

						if(day > 6) {
							day = i - 6;
						}

						$('<th />')
							.text(shortDays[day])
							.appendTo(tableHeader);
					}

					table.append(tableHeader);
				}

				// Add previous month's days
				for(var i = 1; i <= numPrevDays; i++) {
					var day = (daysInLastMonth - numPrevDays) + i;

					days.push({
						date: new Date(date.getFullYear(), date.getMonth() - 1, day, 0, 0, 0),
						displayNumber: day,
						classes: 'month-prev'
					});
				}

				// Add current month's days
				for(var i = 1; i <= numCurrentDays; i++) {
					var day = {
						date: new Date(date.getFullYear(), date.getMonth(), i, 0, 0, 0),
						displayNumber: i,
						classes: ''
					};

					// If this date is today's date, add the `today` class
					day.classes = day.date.isDay() ? 'today' : '';

					// If this date is the one selected, add the `selected` class
					day.classes += day.date.isDay(date) ? ' selected' : '';

					days.push(day);		// Add date to array
				}

				// Add next month's days
				for(var i = 1; i <= numNextDays; i++) {
					days.push({
						date: new Date(date.getFullYear(), date.getMonth() + 1, i, 0, 0, 0),
						displayNumber: i,
						classes: 'month-next'
					});
				}

				// Slice array into 7s
				rows = [
					days.slice(0, 7),
					days.slice(7, 14),
					days.slice(14, 21),
					days.slice(21, 28),
					days.slice(28, 35),
					days.slice(35)
				];

				// Append arrays to table
				for(var row = 0; row < 6; row++) {
					var tr = $('<tr />');

					for(var col = 0; col < 7; col++) {
						var cell = rows[row][col];

						var td = $('<td />');

						$('<td />')
							.data('date', cell.date)
							.text(cell.displayNumber)
							.addClass(cell.classes)
							.attr('id', (date.getMonth()+1).toString()+ '-'+ cell.displayNumber + '-' + date.getFullYear())
							.appendTo(tr);
					}

					tr.appendTo(table);
				}

				return table;
			},
			drawFooter: function(date, options) {
				var footer = $('<div />').addClass('smallcal-footer input-prepend');

				if(options.footer == false) {
					footer.hide();
				}
				footer.hide();

				if(options.todayButton) {
					$('<button />')
						.text('Today')
						.addClass('btn smallcal-today')
						.attr('type', 'button')
						.appendTo(footer);
				}

				if(options.showInput) {
					$('<span />')
						.text(pMethods.formatDate(date, options))
						.addClass('smallcal-input uneditable-input span2')
						.appendTo(footer);
				}

				$('<input />')
					.prop('type', 'hidden')
					.val(parseInt(date.getTime() / 1000), 10)
					.appendTo(footer);

				return footer;
			},
			// Split out into helper object
			formatDate: function(date, options) {		// Verrrry primitive date format function. Does what it needs to do...
				return options.format
					.replace('d', ('0' + date.getDate()).substr(-2))
					.replace('m', ('0' + (date.getMonth() + 1)).substr(-2))
					.replace('y', date.getFullYear().toString().substr(-2))
					.replace('Y', date.getFullYear());
			}
		};

		var methods = {
			init: function(settings) {
				var options = $.extend({}, defaults, settings);

				// Events
				if(!$(document).data('smallcal-events')) {
					$(document).on('click.smallcal', '.smallcal .change-month', function(e) {
							e.preventDefault();
							e.stopPropagation();

							methods.changeMonth.apply($(this).closest('.smallcal'), [ $(this).hasClass('next-month') ? 1 : -1, $(this).closest('.smallcal').data('options') ]);
						})
						.on('click.smallcal', '.smallcal-today', function() {
							methods.changeMonth.apply($(this).closest('.smallcal'), [ now, $(this).closest('.smallcal').data('options') ]);
						})
						.on('click.smallcal', '.smallcal table.current td', function() {
							if($(this).hasClass('month-next')==false && $(this).hasClass('month-prev')==false) {
								var container = $(this).closest('.smallcal');
								var table = $(this).closest('table');

								table.find('.selected').removeClass('selected');

								$(this).addClass('selected');

								container.find('.smallcal-footer').replaceWith(pMethods.drawFooter($(this).data('date'), $(this).closest('.smallcal').data('options')));
								container.data('date', $(this).data('date'));
							}
						})
						// Popups
						.on('click.smallcal', '.smallcal-popup-trigger', function(e) {
							$(this).parent('.smallcal-popup-wrapper').addClass('smallcal-open').find('.smallcal-popup').show();

							// Close other calendars
							$('.smallcal-popup-wrapper.smallcal-open').not($(this).parent()).removeClass('smallcal-open').find('.smallcal-popup').hide();
						})
						.on('click.smallcal', function(e) {
							var target = $(e.target);

							if(!target.closest('.smallcal-popup-wrapper').length) {
								$('.smallcal-popup-wrapper.smallcal-open').removeClass('smallcal-open').find('.smallcal-popup').hide();
							}
						})
						.on('click.smallcal', '.smallcal td', function() {
							var thisDate = $(this).data('date');
							var originalElement = $(this).closest('.smallcal').data('element');
							var formattedDate = pMethods.formatDate($(this).data('date'), $(this).closest('.smallcal').data('options'));

							$(originalElement).trigger('dateselect', [ thisDate ]);

							// Set date on input element if it exists
							$(originalElement).children('.smallcal-popup-trigger').val(formattedDate).trigger('change');
						});

					$(document).data('smallcal-events', true);
				}

				return this.each(function() {
					if($(this).is(':input')) {
						var element = $('<div />');

						$(this).addClass('smallcal-target').after(element);
					} else {
						var element = this;
					}

					$(element).addClass('smallcal ' + options.transition);
					$(element).data('options', options);

					if(options.transition) {
						$(element).addClass('transition');
					}

					if(options.condensed) {
						$(element).addClass('condensed');
					}

					switch(options.mode) {
						case 'popup':
							$(element).addClass('smallcal-popup-trigger');

							pMethods.drawPopupCalendar.apply(element, options.date);
						break;
						case 'widget':
						default:
							pMethods.drawCalendar.call(element, options.date);
					}
				});
			},
			changeMonth: function(month, options) {
				var newDay, newDate, direction, newCalendar;

				var container = $(this);
				var calendar = $(this).find('table');

				var currentDate = container.data('date');
				var calWidth = calendar.outerWidth(true);
				var calHeight = calendar.outerHeight(true);

				calendar.parent().height(calHeight);		// Set height of calendar's container to height of table

				if(typeof month === 'number') {
					direction = month > 0 ? 1 : -1;
					newDay = Math.min(currentDate.daysInMonth(month), currentDate.getDate());		// 31st of March clamped to 28th Feb, for example
					newDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + direction, newDay);
				} else if(month instanceof Date) {
					direction = (month > currentDate) ? 1 : -1;
					newDate = now;
				}

				calendar.stop(true, true);
				container.data('date', newDate);
				newCalendar = pMethods.drawMonth(newDate, options).addClass('current');

				switch(options.transition) {
					case 'fade':
						calendar.fadeOut(options.animDuration, function() {
							$(this).replaceWith(newCalendar.hide().fadeIn(options.animDuration));
						});
					break;
					case 'crossfade':		// Crossfade
						calendar.removeClass('current').after(newCalendar);
						calendar.animate({ opacity: 0 }, options.animDuration);

						newCalendar.css({ opacity: 0, position: 'absolute', top: 0 }).animate({ opacity: 1 }, options.animDuration);
					break;
					case 'carousel-horizontal':
						calendar.css({ position: 'absolute' }).animate({ left: -(calWidth * direction) }).after(newCalendar);

						newCalendar.css({ left: direction * calWidth, position: 'absolute' }).animate({ left: 0 });
					break;
					case 'carousel-vertical':		// Vertical slide
						calendar.css({ position: 'absolute' }).animate({ top: -(calHeight * direction) }).after(newCalendar);

						newCalendar.css({ top: direction * calHeight, position: 'absolute' }).animate({ top: 0 });
					break;
					default:		// No transition - default
						calendar.replaceWith(newCalendar);
					break;
				}

				// Remove old calendar
				newCalendar.promise().done(function() {
					container.find('table').not(newCalendar).remove();
				});

				// Update header and footer
				container.find('.smallcal-header').replaceWith(pMethods.drawHeader(newDate, options));
				container.find('.smallcal-footer').replaceWith(pMethods.drawFooter(newDate, options));

				// Set date on input element if it exists
				if(options.setOnMonthChange) {
					container.prev('.smallcal-target').val($(this).data('date')).trigger('change');
				}
			},
			date: function() {		// Return current selected date
				if($(this).next('.smallcal').length) {
					return $(this).next('.smallcal').data('date');
				} else if($(this).data('smallcal')) {
					return $(this).data('date');
				}

				return false;
			}
		};

		if(methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if(typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		}
	};
}));
