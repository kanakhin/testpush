# Система рассылки push уведомлений

Система для сбора подписок на пуш уведомлений с сайтов и рассылки пуш уведомлений.  

### Запуск
Для запуска приложения нужно:
- Добавить команду `php console core:cron`  в cron для запуска каждую минуту.
- Запустить команду `php console core:worker` в cron для запуска каждую минуту.

### Консольные команды  
- `php console core:cron`  
Команда должна быть запущена в cron для выполнения каждую минуту.  
Для принудительного запуска задачи вручную (имя задачи - имя класса из пространства имен \Kanakhin\Push\Application\PeriodicTasks) можно запускать команду с флагом -f c переданным именем задачи:  `php console core:cron -f SyncCachedStatistics`  

- `php console core:worker`  
Воркер, который рассылает пуш сообщения получателям из очереди.   

- `php console sites:add site.com`  
Добавление нового сайта.  

- `php console sites:remove site.com`  
Удаление сайта.  

- `php console sites:showcode site.com`  
Показать javascript код для установки на сайт.  

### Методы API
!!! Все POST запросы на api должны быть отправлены в виде json !!!  

- `GET /js/push/{xxx}.js`  
Получение уникального javascript кода для сайта, где {xxx} - уникальный хеш для сайта.  

- `POST /api/subscribe`  
Сохранение запроса на подписку.  
Пример запроса:  
```
{
    "host": "test.local", //Адрес сайта
    "endpoint": "https://fcm.googleapis.com/fcm/send/cW5j...", //Генерируется браузером
    "key": "BEaP/jK3zmQbFj4Dw6+nvA6J...", //Генерируется браузером
    "token": "TUGWEx3wIz4oRaHnq8sKBw==" //Генерируется браузером
}
```  

Постбеки аналитики:
- `GET /api/metrics/{id}/visit`  - посещение сайта.  
- `GET /api/metrics/{id}/request`  - показан запрос на подписку.  
- `GET /api/metrics/message/{id}/receive` - сообщение доставлено.  
- `GET /api/metrics/message/{id}/click`  - клик на сообщение.  

### Методы API администратора
Доступ к администраторским api может быть ограничен по ip (настройки конфигурации).  

- `POST /api/admin/message/load/`  
Загрузка в базу новой рассылки.  
Пример запроса:  
```
{
    "sites": [ //Список сайтов для рассылки
        "test.local",
        "testsite2.com"
    ],
    "title": "Achtung!", //Заголовок сообщения
    "body": "The sky is falling.", //Тело сообщения
    "link": "https://google.com", //Ссылка
    "image_small": "TUGWEx3wIz4oRaHnq8sKBw...", //Картинка в base64 (или null, если без картинки)
    "image_big": null, //Большая Картинка в base64 (или null, если без картинки)
    "send_time": "2020-07-10 18:00:00", //Запланированное время рассылки в mysql формате
    "subscribe_from": "2020-06-01", //Фильтр по дате подписки, дата в mysql формате (опционально или null, если фильтрация по дате не нужна)
    "subscribe_to": "2020-06-30", //Фильтр по дате подписки, дата в mysql формате (опционально или null, если фильтрация по дате не нужна)
}
```  

- `GET /api/admin/message/cancel/{id}/`  
Отмена запланированной рассылки по id.  

- `GET /api/admin/statistics/push/[?from=2020-01-01&to=2020-03-31]`  
Получение сводной статистики по рассылкам за период.  

- `GET /api/admin/statistics/push/{id}/`  
Получение детальной статистики для расски по id.  

- `GET /api/admin/statistics/visits/[?from=2020-01-01&to=2020-03-31][&sites=1,2,3]`  
Получение статистики посещений за период. Если не указывать параметр sites, будет возвращена статистика по всем сайтам.  

- `GET /api/admin/sites/`  
Получение списка зарегистрированных сайтов.  

- `POST /api/admin/site/add/`  
Добавление нового сайта.  
Пример запроса:  
```
{
    "site": "https://newsite.com",
    "placeholders": [ //Список плейсхолдеров (опционально)
        "subid1": "123"
    ]
}
```

Пример ответа:  
```
{
    "success": true,
    "added": true,
    "description": "Сайт успешно добавлен",
    "id": 213,
    "host": "newsite.com",
    "url": "https://newsite.com"
}
```

- `GET /api/admin/site/{id}/[?integrationcheck=1]`  
Получение общей информации о сайте по id.  
При передаче необязательного GET параметра integrationcheck на сайте будет принудительно перепроверено наличие javascript кода и файла воркера.  
Пример ответа:  
```
{
    "success": true,
    "id": 1, //id сайта
    "host": "testsite1.local", //host сайта
    "url": "https://testsite1.local", //url сайта
    "info": {
        "subscribers_active": 9, //Количество активных подписчиков
        "subscribers_total": 50, //Количество подписчиков за всё время
        "subscribers_today": 1, //Количество подписчиков за сегодня
        "subscribers_unsubscribe": 3, //Количество отписок
        "sendings": 10, //Количество совершенных рассылок для сайта
        "integration_success": true //Статус установки javascript кода и файла воркера на сайте
    },
    "integration": {
        "javascript": "<script src=\"https://testpush.ru/js/push/5d237064baf93ae822523d7f9f7f1ee9.js\" async></script>", //Уникальный javascript код для сайта
        "worker_file": "https://testpush.ru/api/worker.zip", //Ссылка на архив с файлом воркера
        "worker_filename": "fk-push-worker.js", //Имя файла воркера
        "javascript_integration": true, //Статус установки javascript кода на сайте
        "worker_integration": true //Статус установки файла воркера на сайте
    },
    "settings": {
        "overlay_status": true, //Включение показа всплывающего сообщения
        "overlay_text": "Внимание! Спасибо за внимание.", //Текст всплывающего сообщения
        "overlay_text_color": "#ffffff", //Цвет текста
        "overlay_bg_color": "#222222", //Цвет фоновой заливки
        "placeholders": [ //Список плейсхолдеров
            "subid1": "123",
            "subid2": "test"
        ]
    }
}
```

- `POST /api/admin/site/{id}/`  
Изменение настроек для сайта. Все параметры являются необязательными, настройки будут изменены только для переданных параметров.  
Пример запроса:
```
{
    "overlay_status": true, //Включение показа всплывающего сообщения
    "overlay_text": "Внимание! Спасибо за внимание.", //Текст всплывающего сообщения
    "overlay_text_color": "#ffffff", //Цвет текста
    "overlay_bg_color": "#222222", //Цвет фоновой заливки
    "placeholders": [ //Список плейсхолдеров
        "subid1": "123",
        "subid2": "test"
    ]
}
```

- `DELETE /api/admin/site/{id}/`  
Удаление сайта.  



