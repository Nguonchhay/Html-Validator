Welcome to HTML Validator
======================

This is the CLI for validating rendered HTML in your website, page by page. This package could be used unless your website is configured `sitemap`
with url `your-website-base-url/sitemap.xml`.

Installation
------------

* Verify your existing Node.js and make sure you run the latest stable version.

```
	sudo npm cache clean -f
	sudo npm install -g n
	sudo n stable
	node -v

```

** Note: if your computer does not have NodeJS, follow this [how to install NodeJS](https://nodejs.org/en/download/)

* First you need to install `valimate` command. We need to install this command only one time.

```
	cd ~
	sudo npm i -g valimate

```

For local running, checkout this [original source](https://github.com/jamesseanwright/valimate)

* Clone repository to your desire path.

	git clone origin https://github.com/Nguonchhay/Html-Validator.git

How to run HTML validator
--------------------------

* Go to your HTML validator root directory

	cd /path/to/your/HtmlValidator

* Run validator command

	./html-validator  --baseUrl=your-website-base-url

* By default the report will be placed at `/path/to/your/HtmlValidator/Report`. If you want to override report path, pass the argument `--output` or shortcut `-o`.

	./html-validator --baseUrl=your-website-base-url -o=build/logs

* By default the report filename is `html-validator.html`. If you want to override report filename, pass the argument `--filename`.

	./html-validator --baseUrl=your-website-base-url -o=build/logs --filename=html-test.html

* Note: in case there is unexpected error related while your configuration is valid. Try to remove the node cache

	rm  -rf ~/.npm
	npm cache clear

References
----------
1. [https://medium.com](https://medium.com/@jamesseanwright/automatically-validate-html-with-node-js-and-valimate-196c71a349bf#.g4dsvqfhv)

## Credit to `James Wright`
