laravelSms
==========

package for send sms with laravel5.6 (published for [melipayamak.com](http://melipayamak.com)) this package only work for iranian mobile operator

installation
------------
For install this package Edit your project's ```composer.json``` file to require parsidev/melipayamak

```php
"require": {
    "parsidev/melipayamak": "5.6.x-dev"
},
```
Now, update Composer:
```
composer update
```
Publish config files:
```
php artisan vendor:publish
```
for change username, password and other configuration change ```config/melipayamak.php```

Usage
-----
for use this package, please register on [melipayamak.com](http://melipayamak.com)


### Send Message
```php
MeliPayamak::sendSMS('Recieptor number', 'text message'); // send normal message for a person

MeliPayamak::sendSMS(array('Recieptor number1', 'Recieptor number2'), 'text mesage'); // send normal message for persons

//---------------------------------------
$url   = 'www.google.com'; // Doesn't need http://
$title = 'Google Search Engine';
MeliPayamak::sendSMS('Recieptor number', "\n".$title."\n".$url, 'wap'); // send wap push message for a person

//---------------------------------------

MeliPayamak::sendSMS('Recieptor number', 'text message', 'flash'); // send flash message for a person
```

### Get Credit
```php
MeliPayamak::getCredit();
```

### Get Status
```php
MeliPayamak::getStatus('unique id'); // get status of sent message, you receive unique id from sendSMS function.


$response = MeliPayamak::sendSMS('Recieptor number', 'text message');

$uniqeId = $response[0]->uid;

```
