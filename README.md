# Master PHP
Author: [Scott Lesovic](http://guilefulmagic.com/)

## About
A remastered version of master.php by [Chris Rose](http://www.chrisrosemagic.com/)

### One file, multiple request types
Instead of using the request method of `$_GET`,
We are going to use the `$_REQUEST` method. This
allows for all request methods to be supported.

The order that they take effect depends on settings
in your [php.ini](http://php.net/manual/en/ini.core.php#ini.request-order)
file.

The downside of this method is that the URL parameters
must match the way [1ShoppingCart](http://www.1shoppingcart.com/)
sends the request.

ie: `.../master.php?Email1=user@example.com&Name=John Doe&Zip=12345`
