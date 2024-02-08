(function () {

	//Плейсхолдеры будут заменяться уникальными данными для каждого сайта
	const options = {
		vapidPublicKey: 'PLACEHOLDER(public_vapid_key)',
		selfHost: 'PLACEHOLDER(host)',
		serviceHost: 'PLACEHOLDER(service_host)',
		overlay: 'false',
		overlayText: 'Подпишись!',
		overlayTextColor: '#1f2dff',
		overlayBgColor: '#f23f5f',
	};

	//Определение типа браузера
	var browser = {
		isAndroid: /Android/.test(navigator.userAgent),
		isEdge: /Edge/.test(navigator.userAgent),
		isEdge2: /Edg/.test(navigator.userAgent),
		isFirefox: /Firefox/.test(navigator.userAgent),
		firefoxVersion: (navigator.userAgent.match(/Firefox\/(\d+)/) || [0, 0])[1],
		isChrome: /Google Inc/.test(navigator.vendor) && /Chrome/.test(navigator.userAgent) && !/OPR/.test(navigator.userAgent),
		isChromeIOS: /CriOS/.test(navigator.userAgent),
		isChromiumBased: !!window.chrome && !/Edge/.test(navigator.userAgent),
		isIE: /Trident/.test(navigator.userAgent),
		isYandex: /YaBrowser/.test(navigator.userAgent),
		isYandexApp: /YaApp/.test(navigator.userAgent),
		isIOS: /(iPhone|iPad|iPod)/.test(navigator.platform),
		isOpera: /OPR/.test(navigator.userAgent),
		isSafari: /Safari/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent),
		isMobile: !!(navigator.maxTouchPoints || 'ontouchstart' in document.documentElement),
		isMac: /Mac OS X/.test(navigator.userAgent),
	};

	//Подключение стиля
	var overlay_style = document.createElement('link');
	overlay_style.rel = 'stylesheet';
	overlay_style.href = options.serviceHost + '/css/overlay.css';
	document.querySelector('head').appendChild(overlay_style);

	//Подключение стиля плашки с текстом
	var overlay2_style = document.createElement('link');
	overlay2_style.rel = 'stylesheet';
	overlay2_style.href = options.serviceHost + '/css/overlay2.css';
	document.querySelector('head').appendChild(overlay2_style);

	//Инициализация запроса подписки
	window.addEventListener('load', () => setTimeout(webpush.init, 1));

	function getUnixTimestamp() {
		return Math.floor(Date.now() / 1000);
	}

	var webpush = {
		swRegistration: null,

		//Инициализация
		init: function() {
			//Установка времени первого посещения
			webpush.setFirstVisitTimeTstamp();

			//Отключаем в мобильном приложении яндекса
			if (browser.isYandexApp) {
				return;
			}

			//В мобильной опере пуши не работают, отключаем
			if (browser.isMobile && browser.isOpera) {
				return;
			}

			//В мобильном яндекс браузере уведомления браузера начинают работать через сутки после первого посещения (особенность браузера)
			//поэтому начинаем показывать оверлей только через сутки после первого посещения
			if (browser.isMobile && browser.isYandex && webpush.getFirstVisitTimeTstamp() + 86400 > getUnixTimestamp()) {
				return;
			}

			//Если есть поддержка пушей браузером
			if('serviceWorker' in navigator && 'PushManager' in window) {
				//Регистрация сервис воркера и запрос подписки
				navigator.serviceWorker.register('PLACEHOLDER(host)/fk-push-worker.js').then(swRegistration => {
					webpush.swRegistration = swRegistration;
					webpush.requestSubscription(webpush.swRegistration);
				});
			}
		},

		//Запрос подписки
		requestSubscription: function(swRegistration) {
			swRegistration.pushManager.getSubscription().then(subscription => {
				var tstamp_start = + new Date();

				//Порог времени в микросекундах для определения автоматического закрытия браузером.
				//Для мобильного firefox порог больше, потому что он тормозит.
				var close_treshold = browser.isAndroid && browser.isFirefox ? 200 : 90;

				//Инициализируем запрос на получение уведомлений, если разрешение еще не получено
				if (Notification.permission === 'default') {
					overlay.show();

					//Попытка создания подписки автоматически
					webpush.doSubscription(swRegistration).then(() => {
						//Проверка времени, прошедшего с момента вывода запроса.
						//Если прошло меньше {close_treshold} мкс, значит запрос закрыт браузером автоматически
						var treshold = + new Date() - tstamp_start;
						if (treshold > close_treshold) {
							//Запрос закрыт вручную, просто скрываем оверлей
							overlay.remove();
							overlay2.remove();
						} else {
							//Запрос закрыт автоматически браузером, слушаем событие ручного подтверждения подписки
							navigator.permissions.query({name: 'notifications'}).then(status => {
								status.onchange = () => {
									overlay.remove();
									overlay2.remove();

									//Если уведомления разрешены вручную, создаем подписку
									if (status.state === 'granted') {
										//Создание подписки
										webpush.doSubscription(swRegistration);
									}
								};
							});
						}
					});
				}

				//Если уведомления разрешены в браузере, но подписка не создана
				if (Notification.permission === 'granted' && subscription === null) {
					//Создание подписки
					webpush.doSubscription(swRegistration);
				}
			});
		},

		//Создание новой подписки
		doSubscription: function(swRegistration) {
			return new Promise((resolve, reject) => {
				//Запрашиваем разрешение на подписку и если разрешение получено, отправляем ключи на сервер
				swRegistration.pushManager.subscribe({
					userVisibleOnly: true,
					applicationServerKey: urlB64ToUint8Array(options.vapidPublicKey)
				}).then(subscription => {
					resolve();
					webpush.sendSubscriptionToServer(subscription);
				}).catch(() => {
					resolve();
				});
			});
		},

		//Отправка подписки на сервер
		sendSubscriptionToServer: function(subscription) {
			const endpoint = subscription.endpoint;
			const key = btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('p256dh'))));
			const token = btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('auth'))));

			//Данные для передачи
			var request = {
				host: options.selfHost,
				endpoint: endpoint,
				key: key,
				token: token,
				keyPublic: options.vapidPublicKey
			};

			//Отправка подписки на сервер
			fetch(options.serviceHost + '/api/subscribe', {
				method: 'POST',
				headers: {Accept: 'application/json', 'Content-Type': 'text/plain'},
				body: JSON.stringify(request)
			});
		},

		//Установка времени первого посещения
		setFirstVisitTimeTstamp: function() {
			if (localStorage.getItem('first_visit') === null) {
				localStorage.setItem('first_visit', getUnixTimestamp());
			}
		},

		//Получить время первого посещения
		getFirstVisitTimeTstamp: function() {
			return parseInt(localStorage.getItem('first_visit') || getUnixTimestamp());
		}
	};

	var overlay = {
		//Статус оверлея (сейчас на странице или нет)
		status: false,

		//html шаблон оверлея
		html: `
			<div id="webpush__overlay">
				<div class="webpush__wrapper" style="background:${options.overlayBgColor};">
					<div class="webpush__arrow"><div><i></i><i></i><i></i></div></div>
					<div class="webpush__text" style="color:${options.overlayTextColor};"><div class="size_contaner"><div class="screen_s"></div>Подпишитесь!</div></div>
					<svg id="webpush__close" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
						<path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"/>
					</svg>
					<div class="webpush__firefox-desktop-layer"></div>
				</div>
			</div>
		`,

		//Показать оверлей
		show: function() {
			if (options.overlay === false) return;
			if (overlay.status === true) return;

			overlay.status = true;

			//Вставка html кода оверлея
			var body = document.querySelector('body');
			var template = document.createElement('template');
			template.innerHTML = overlay.html.trim();
			body.appendChild(template.content.firstChild);

			//Применение стиля для скрытия скроллбаров к body
			body.classList.add('webpush__hidescrolls');

			//Навешивание обработчика клика на крестик закрытия
			document.getElementById('webpush__close').onclick = overlay.remove;

			var arrow = body.querySelector('#webpush__overlay .webpush__arrow');
			var wrapper = body.querySelector('#webpush__overlay .webpush__wrapper');
			var img_block = body.querySelector('#webpush__overlay .webpush__wrapper .screen_s');

			//Применение зависимых от устройства настроек
			if (browser.isMac && browser.isYandex) {
				//Desktop Mac Yandex Browser
				arrow.classList.add('webpush__arrow_mac-yandex');
				img_block.style.display = 'flex';
				img_block.classList.add('y_m');	// Добавляем картинку с поясннеием для mac yandex browser
			} else if (!browser.isAndroid && browser.isYandex) {
				//Desktop Yandex Browser
				arrow.classList.add('webpush__arrow_yandex');
				img_block.style.display = 'flex';
				img_block.classList.add('y_w');	// Добавляем картинку с поясннеием для win yandex browser
			} else if (browser.isEdge || browser.isEdge2) {
				//Desktop Edge Browser WITH image
				arrow.classList.add('webpush__arrow_chrome');
				img_block.style.display = 'flex';
				img_block.classList.add('e_w'); // Добавляем картинку с поясннеием для win edge browser
			} else if (browser.isMac && browser.isChrome) {
				//Desktop Mac Chrome
				arrow.classList.add('webpush__arrow_mac-chrome');
			} else if (browser.isMac && browser.isOpera) {
				//Desktop Mac Opera
				arrow.classList.add('webpush__arrow_mac-opera');
			} else if (!browser.isAndroid && browser.isChrome) {
				//Desktop Chrome
				arrow.classList.add('webpush__arrow_chrome');
			} else if (browser.isOpera) {
				//Desktop Opera
				arrow.classList.add('webpush__arrow_opera');
			} else if (browser.isMobile && browser.isYandex) {
				//Mobile Yandex Browser
				var ff_layer = body.querySelector('#webpush__overlay .webpush__firefox-desktop-layer');
				ff_layer.style.display = 'block';
				ff_layer.onclick = overlay.ff_fix_layer;
				arrow.classList.add('webpush__arrow_mobile-firefox');
			} else if(browser.isAndroid && browser.isChrome) {
				//Mobile Chrome
				arrow.classList.add('webpush__arrow_mobile-chrome');
				wrapper.classList.add('webpush__wrapper_mobile-chrome');
			} else if(!browser.isAndroid && browser.isFirefox && browser.firefoxVersion >= 72) {
				//Desktop Firefox >= 72
				var ff_layer = body.querySelector('#webpush__overlay .webpush__firefox-desktop-layer');
				ff_layer.style.display = 'block';
				ff_layer.onclick = overlay.ff_fix_layer;
			} else if(browser.isAndroid && browser.isFirefox && browser.firefoxVersion >= 72) {
				//Mobile Firefox >= 72
				var ff_layer = body.querySelector('#webpush__overlay .webpush__firefox-desktop-layer');
				ff_layer.style.display = 'block';
				ff_layer.onclick = overlay.ff_fix_layer;
				arrow.classList.add('webpush__arrow_mobile-firefox');
			}
		},

		//Скрыть оверлей
		remove: function() {
			if (options.overlay === false) return;

			overlay.status = false;

			var body = document.querySelector('body');
			var overlay_block = document.getElementById('webpush__overlay');

			//Удаление блока оверлея
			if (overlay_block) {
				overlay_block.remove();
			}

			//Удаление стиля для скрытия скроллбаров к body
			body.classList.remove('webpush__hidescrolls');
		},

		//Для браузера Firefox поверх основного оверлея выводится вспомогательный для того, чтобы по клике на нем показать запрос подписки.
		//Это сделано потому, что Firefox новых версий не позволяет показывать запрос подписки автоматически.
		ff_fix_layer: function() {
			var ff_layer = document.querySelector('#webpush__overlay .webpush__firefox-desktop-layer');
			ff_layer.remove();
			webpush.requestSubscription(webpush.swRegistration);
		}

	};

	var overlay2 = {
		//Статус оверлея (сейчас на странице или нет)
		status: false,

		webpush: function() {
			webpush.requestSubscription(webpush.swRegistration);
			overlay2.remove();
		},

		//html шаблон оверлея
		html: `
			<div class="overlay2" id="overlay2" style="background: ${options.overlay2BgColor};">
				<div class="overlay2__title">
					<div class="overlay2__title--text" style="color: ${options.overlay2TitleTextColor}">${options.overlay2TitleText}</div>
					<div class="overlay2__title--close" id="overlay2__close">×</div>
				</div>
				<div class="overlay2__body" style="color: ${options.overlay2TextColor}">${options.overlay2Text}</div>
				<div class="overlay2__button" style="background: ${options.overlay2ButtonColor}; color: ${options.overlay2ButtonTextColor}">${options.overlay2ButtonText}</div>
			</div>
		`,

		//Показать оверлей
		show: function() {
			if (options.overlay2 === false) return;
			if (overlay2.status === true) return;
			if (overlay2.checkClose() === true) return;

			overlay2.status = true;

			//Вставка html кода оверлея
			var body = document.querySelector('body');
			var template = document.createElement('template');
			template.innerHTML = overlay2.html.trim();
			body.appendChild(template.content.firstChild);
			var overlay2_html = document.querySelector('#overlay2 .overlay2__button');
			overlay2_html.onclick = overlay2.webpush;
			webpush.requestSubscription(webpush.swRegistration);

			//Навешивание обработчика клика на крестик закрытия
			document.getElementById('overlay2__close').onclick = overlay2.remove;
		},

		//Скрыть оверлей
		remove: function() {
			if (options.overlay2 === false) return;

			overlay2.status = false;

			var overlay_block = document.getElementById('overlay2');

			//Удаление блока оверлея
			if (overlay_block) {
				overlay_block.remove();
				overlay2.setClose();
			}
		},

		setClose: function() {
			let date_check = new Date(Date.now() + 86400e3).toUTCString();
			let status_check = overlay2.getCookie('Overlay2Close');

			if (status_check !== true) {
				document.cookie = "Overlay2Close=true; expires=" + date_check;
			}
		},

		checkClose: function() {
			return overlay2.getCookie('Overlay2Close');
		},

		getCookie: function(name) {
			let matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
			return matches ? Boolean(decodeURIComponent(matches[1])) : undefined;
		}
	};

	//urlB64ToUint8Array is a magic function that will encode the base64 public key
	function urlB64ToUint8Array(base64String) {
		const padding = '='.repeat((4 - base64String.length % 4) % 4);
		const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
		const rawData = window.atob(base64);
		const outputArray = new Uint8Array(rawData.length);
		for (let i = 0; i < rawData.length; ++i) {
			outputArray[i] = rawData.charCodeAt(i);
		}
		return outputArray;
	};

})();
