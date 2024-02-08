'use strict';

self.addEventListener('push', function(event) {
	const payload = event.data.json();
	const data = payload.data; //custom data
	const title = payload.title; //title
	const options = {
		data: data, //custom data
		body: payload.body, //text
	};

	const pushInfoPromise = self.registration.showNotification(title, options)

	event.waitUntil(Promise.all([pushInfoPromise]));
});

self.addEventListener('notificationclick', function(event) {
	const data = event.notification.data;
	const openWindowPromise = clients.openWindow(data.url);

	event.notification.close();
	event.waitUntil(Promise.all([openWindowPromise]));
});
