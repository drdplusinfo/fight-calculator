<img src="logo.png" alt="The modern DOM API for PHP 7 projects" align="right" />

# The modern DOM API for PHP 7 projects.

Built on top of PHP's native [DOMDocument](http://php.net/manual/en/book.dom.php), this project provides access to modern DOM APIs, as you would expect working with client-side code in the browser.

Performing DOM manipulation in your server-side code enhances the way dynamic pages can be built. Utilising a standardised object-oriented interface means the page can be ready-processed, benefitting browsers, webservers and content delivery networks.

***

<a href="https://circleci.com/gh/PhpGt/Dom" target="_blank">
	<img src="https://img.shields.io/circleci/project/PhpGt/Dom/master.svg?style=flat-square" alt="Build status" />
</a>
<a href="https://scrutinizer-ci.com/g/PhpGt/Dom" target="_blank">
	<img src="https://img.shields.io/scrutinizer/g/PhpGt/Dom/master.svg?style=flat-square" alt="Code quality" />
</a>
<a href="https://scrutinizer-ci.com/g/PhpGt/Dom" target="_blank">
	<img src="https://img.shields.io/scrutinizer/coverage/g/PhpGt/Dom/master.svg?style=flat-square" alt="Code coverage" />
</a>
<a href="https://packagist.org/packages/PhpGt/Dom" target="_blank">
	<img src="https://img.shields.io/packagist/v/PhpGt/Dom.svg?style=flat-square" alt="Current version" />
</a>
<a href="http://www.php.gt/dom" target="_blank">
	<img src="https://img.shields.io/badge/docs-www.php.gt/dom-26a5e3.svg?style=flat-square" alt="PHP.G/Dom documentation" />
</a>

## Example usage: Hello, you!

Consider a page with a form, with an input element to enter your name. When the form is submitted, the page should greet you by your name.

This is a simple example of how source HTML files can be treated as templates. This can easily be applied to more advanced template pages to provide dynamic content, without requiring non-standard techniques such as `{{curly braces}}` for placeholders, or `echo '<div class='easy-mistake'>' . $content['opa'] . '</div>'` horrible HTML construction from within PHP.

### Source HTML (`name.html`)

```html
<!doctype html>
<h1>
	Hello, <span id="your-name">you</span> !
</h1>

<form>
	<input name="name" placeholder="Your name, please" required />
	<button>Submit</button>
</form>
```

### PHP used to inject your name (`index.php`)

```php
<?php
require "vendor/autoload.php";

$html = file_get_contents("name.html");
$document = new \Gt\Dom\HTMLDocument($html);

if(isset($_GET["name"])) {
	$document->getElementById("your-name")->textContent = $_GET["name"];
}

echo $document->saveHTML();
```

## Features at a glance

+ DOM level 4 classes:
	+ [`HTMLDocument`][mdn-HTMLDocument]
	+ [`Element`][mdn-Element]
	+ [`HTMLCollection`][mdn-HTMLCollection]
	+ and more [extended DOM][mdn-DOM-levels] classes
+ Standardised traits to add functionality in accordance with W3C
+ Reference elements using CSS selectors via [`querySelector`][mdn-qs]([`All`][mdn-qsa])
+ Add/remove/toggle elements' classes using [`ClassList`][mdn-classList]
+ `Element` Nodes within the document traversable with W3C properties:
	+ [`previousElementSibling`][mdn-pes] and [`nextElementSibling`][mdn-nes]
	+ [`children`][mdn-children]
	+ [`lastElementChild`][mdn-lec] and [`firstElementChild`][mdn-fec]
+ [`Element::remove()`][mdn-remove] to detach it from the document
+ Add elements around another using [`Element::before()`][mdn-before] and [`Element::after()`][mdn-after]
+ Replace an element in place using [`Element::replaceWith()`][mdn-replaceWith]
+ Standard collection properties on the `HTMLDocument`:
	+ [`anchors`][mdn-anchors]
	+ [`forms`][mdn-forms]
	+ [`image`][mdn-images]
	+ [`links`][mdn-links]
	+ [`scripts`][mdn-scripts]
	+ [`title`][mdn-title]

### Page template features

This repository is intended to be as accurate to the DOM specification as possible. An extension to the repository is available at https://php.gt/domtemplate which adds page templating through custom elements and template attributes, introducing serverside functionality similar to that of WebComponents.

[mdn-HTMLDocument]: https://developer.mozilla.org/docs/Web/API/HTMLDocument
[mdn-Element]: https://developer.mozilla.org/docs/Web/API/Element
[mdn-HTMLCollection]: https://developer.mozilla.org/docs/Web/API/HTMLCollection
[mdn-DOM-levels]: https://developer.mozilla.org/docs/DOM_Levels
[mdn-qs]: https://developer.mozilla.org/docs/Web/API/Element/querySelector
[mdn-qsa]: https://developer.mozilla.org/docs/Web/API/Element/querySelectorAll
[mdn-classList]: https://developer.mozilla.org/docs/Web/API/Element/classList
[mdn-pes]: https://developer.mozilla.org/docs/Web/API/NonDocumentTypeChildNode/previousElementSibling
[mdn-nes]: https://developer.mozilla.org/en-US/docs/Web/API/NonDocumentTypeChildNode/nextElementSibling
[mdn-children]: https://developer.mozilla.org/en-US/docs/Web/API/ParentNode/children
[mdn-lec]: https://developer.mozilla.org/docs/Web/API/ParentNode/lastElementChild
[mdn-fec]: https://developer.mozilla.org/docs/Web/API/ParentNode/firstElementChild
[mdn-remove]: https://developer.mozilla.org/docs/Web/API/ChildNode/remove
[mdn-before]: https://developer.mozilla.org/docs/Web/API/ChildNode/before
[mdn-after]: https://developer.mozilla.org/docs/Web/API/ChildNode/after
[mdn-replaceWith]: https://developer.mozilla.org/docs/Web/API/ChildNode/replaceWith
[mdn-anchors]: https://developer.mozilla.org/docs/Web/API/Document/anchors
[mdn-forms]: https://developer.mozilla.org/docs/Web/API/Document/forms
[mdn-images]: https://developer.mozilla.org/docs/Web/API/Document/images
[mdn-links]: https://developer.mozilla.org/docs/Web/API/Document/links
[mdn-scripts]: https://developer.mozilla.org/docs/Web/API/Document/scripts
[mdn-title]: https://developer.mozilla.org/docs/Web/API/Document/title
