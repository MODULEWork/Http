HTTP Package
------------

[![Latest Stable Version](https://poser.pugx.org/MODULEWork/Http/v/stable.png)](https://packagist.org/packages/MODULEWork/Http)
[![Build Status](https://travis-ci.org/MODULEWork/Http.png?branch=master)](https://travis-ci.org/MODULEWork/Http)


The HTTP Package of the Modulework Framework.

It provides a convient way of handling HTTP request and HTTP response.

So for example you could already create a application with these two classes:

```php
$req = Request::makeFromGlobals();
    
$content = 'Hello ' . $req->query->get('name', 'Stranger');
    
$res = Response::make($content);
$res->send();
```

This will app will great every vistor with their name or if the vistor didn' t provide the name in the query string  it will fallback to "Stranger".

Of course this is a very basic example but it can do a lot more!

We could expand this and save the name into a cookie:

```php
$req = Request::makeFromGlobals();
    
$name = $req->query->get('name', Stranger);
$content = 'Hello ' . $name;
    
$res = Response::make($content);
$res->addCookie(Cookie::make(
		'name',
		$name
));
$res->send();
```

Or check if the method is GET or POST, so a user could also POST it' s name:

```php
$req = Request::makeFromGlobals();

$name = $req->query->get('name',
	$req->request->get('name', 'Stranger')
	);
// Or just getting the method:
$method = $req->getMethod();

$content = 'Hello ' . $name;

$res = Response::make($content);
$res->addCookie(Cookie::make(
		'name',
		$name
		));
$res->send();
```

Or display the client' s IP:

```php
echo Request::makeFromGlobals()->getClientIp();
```

Now we also send a cookie a too the user. But we can do even more:

```php
$res = Response::make()
->setContent('Foo')
->setStatusCode(200)
->addHeader('Expire', 'never')
->setDate(new DateTime)
->addCookie(Cookie::make('foo')
	->setValue('bar')
	->setSecure(false)
	->setHttpOnly(false)
)
->prepare($request)
->send();
```

**Chained methods! Custom Headers! Custom Status Codes! And much more!**

The Response class is intelligent enough to set the status code, if a redirect is issued:

```php
$res = Response::make()
->addHeader('Location', 'foo.bar')
->prepare($request)
->send()
```

Will result in this header

```
HTTP/1.0 302 Found
Location: foo.bar
Date [...]
```

But the Request class can do even more, we can use it for very basic routing:

```php
$req = Request::makeFromGlobals();
if ('/' == $req->getPath()) {
    echo "You are on the homepage"
} elseif ('/foo/bar' == $req->getPath()) {
    echo "You are on the bar page of foo!"
}
```

This is very basic and not best practice, but it shows for what we can use this class for!

There are also some more Response classes, like the RedirectResponse and JsonResponse class.

Here is an usage example:

```php
$res = RedirectResponse::make('http://foo.bar')
->withCookie(Cookie::make('foo'))
->prepare(Request::makeFromGlobals)
->send();
```

As you can see very easy to use. *NOTE!* This is just a **wrapper** for a normal Response. You could also do this:

```php
$res = Response::make('<extra>', 302, array('Location' => 'http://foo.bar'))
->addCookie(Cookie::make('foo'))
->prepare(Request::makeFromGlobals)
->send();
```

The only thing what is not shown in the alternative way is the HTML meta redirect (in case the header doesn' t fire).

The JsonResponse class is pretty straight forward as well:

```php
$res = JsonResponse::make(array('foo' => 'bar'))
->prepare(Request::makeFromGlobals)
->send();
```

This would result in this response:

```
HTTP/1.0 200 OK
Content-Type application/json
Date [...]

{"foo": "bar"}
```

Pretty nifty, eeh!!?
