# Система рассылки push уведомлений

Система для сбора подписок на пуш уведомлений с сайтов и рассылки пуш уведомлений.  

### Запуск
Для запуска приложения нужно:
- Добавить команду `php console core:cron`  в cron для запуска каждую минуту (или чаще).

### Консольные команды  
- `php console core:cron`  
Команда должна быть запущена в cron для выполнения каждую минуту.  
Для принудительного запуска задачи вручную (имя задачи - имя класса из пространства имен \Kanakhin\Push\Application\PeriodicTasks) можно запускать команду с флагом -f c переданным именем задачи:  `php console core:cron -f SyncCachedStatistics`  

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
    "send_time": "2020-07-10 18:00:00", //Запланированное время рассылки в mysql формате
    "subscribe_from": "2020-06-01", //Фильтр по дате подписки, дата в mysql формате (опционально или null, если фильтрация по дате не нужна)
    "subscribe_to": "2020-06-30", //Фильтр по дате подписки, дата в mysql формате (опционально или null, если фильтрация по дате не нужна)
}
```  

- `GET /api/admin/message/cancel/{id}/`  
Отмена запланированной рассылки по id.  

- `GET /api/admin/sites/`  
Получение списка зарегистрированных сайтов.  

- `POST /api/admin/site/add/`  
Добавление нового сайта.  
Пример запроса:  
```
{
    "site": "https://newsite.com"
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

- `GET /api/admin/site/{id}/`  
Получение общей информации о сайте по id.  
При передаче необязательного GET параметра integrationcheck на сайте будет принудительно перепроверено наличие javascript кода и файла воркера.  
Пример ответа:  
```
{
    "success": true,
    "id": 1, //id сайта
    "host": "testsite1.local", //host сайта
    "url": "https://testsite1.local", //url сайта
}
```

- `DELETE /api/admin/site/{id}/`  
Удаление сайта.  



