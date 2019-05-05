self.addEventListener('push', function(event) {
  if (event.data) {
      var data = event.data.json();
    self.registration.showNotification(data.title,{
        body: data.body,
        icon: data.icon,
        data: data.data
    });
    console.log('This push event has data: ', event.data.text());
  } else {
    console.log('This push event has no data.');
  }
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    var data = event.notification.data;
    if(data != null)
    {
        event.waitUntil(clients.matchAll({
           type: "window"
        }).then(function(clientList) {
           for (var i = 0; i < clientList.length; i++) {
              var client = clientList[i];
              if (client.url == data && 'focus' in client)
                 return client.focus();
           }
           if (clients.openWindow)
               return clients.openWindow(data);
        }));
    }
});
