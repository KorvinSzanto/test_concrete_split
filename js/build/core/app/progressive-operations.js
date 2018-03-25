!function (global, $) {
	'use strict';

	function ConcreteProgressiveOperation(options) {
		'use strict';
		var my = this;
		options = $.extend({
			url: '',
			data: {},
			title: '',
			response: null, // If we have already performed the queueing action, as in a form, we will have a response, and no URL/data
			onComplete: null,
			onError: null,
			pollRetryTimeout: 1000,
			$element: null
		}, options);
		my.options = options;
		my.current = 0;
		my.total = -1; // unknown
		my.pnotify = false;
		my.execute();
	}

	ConcreteProgressiveOperation.prototype.poll = function(queue, token, remaining) {
		var my = this,
			url = CCM_DISPATCHER_FILENAME + '/ccm/system/queue/monitor/' + queue + '/' + token;

		$.concreteAjax({
			loader: false,
			url: url,
			type: 'POST',
			dataType: 'json',
			success: function(r) {

				if (my.total == -1) {
					// We haven't set the total yet.
					my.total = r.remaining;
				}

				my.current += my.total - r.remaining;
				NProgress.set((my.total - r.remaining) / my.total);

				$('div[data-wrapper=progressive-operation-status]').html(r.remaining + ' remaining');

				if (r.remaining > 0) {
					setTimeout(function() {
						my.poll(queue, token, r.remaining);
					}, my.options.pollRetryTimeout);
				} else {
					setTimeout(function() {
						// give the animation time to catch up.
						NProgress.done();
						my.pnotify.remove();
						if (typeof(my.options.onComplete) == 'function') {
							my.options.onComplete(r);
						}
					}, 1000);

				}
			}
		});
	}

	ConcreteProgressiveOperation.prototype.startPolling = function(queue, token) {
		var my = this;
		my.pnotify = new PNotify({
			text: '<div data-wrapper="progressive-operation-status">' + ccmi18n.progressiveOperationLoading + '</div>',
			hide: false,
			title: my.options.title,
			buttons: {
				closer: false
			},
			type: 'info',
			icon: 'fa fa-refresh fa-spin'
		});

		my.poll(queue, token);
	}

	ConcreteProgressiveOperation.prototype.execute = function() {
		var my = this;
		if (!my.options.$element) {
			NProgress.set(0);
		}

		if (my.options.response) {
			// We have already performed the submit as part of another operation,
			// like a concrete5 ajax form submission
			my.startPolling(my.options.response.queue, my.options.response.token)
		} else {
			$.concreteAjax({
				loader: false,
				url: my.options.url,
				type: 'POST',
				data: my.options.data,
				dataType: 'json',
				success: function(r) {
					my.startPolling(r.queue, r.token)
				}
			});
		}
	}

	global.ConcreteProgressiveOperation = ConcreteProgressiveOperation;

}(this, $);