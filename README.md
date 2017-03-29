Welcome to HTML Validator
======================

This is the CLI for validating rendered HTML in your website, page by page. You can validate by providing urls that you want or 
validate the whole valid url within your website. If you want to validate the whole website, your website has to configured `sitemap`
with the url `your-website-base-url/sitemap.xml`.

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

* List all available arguments

	./html-validator

	OR

	./html-validator --help

* Run validator command for validating the whole website

	./html-validator  --baseUrl=your-website-base-url

* Run validator command for validating some urls. The urls can be from different websites.

	./html-validator  --urls="url_1,url_2,...,url_n"

* By default the report will be placed at `/path/to/your/HtmlValidator/Report`. If you want to override report path, pass the argument `--output` or shortcut `-o`.

	./html-validator --baseUrl=your-website-base-url -o=path/that/you/want/to/store/report

* By default the report filename is `html-validator.html`. If you want to override report filename, pass the argument `--filename`.

	./html-validator --baseUrl=your-website-base-url --filename=html-test.html

** Note: in case there is unexpected error related while your configuration is valid. Try to remove the node cache

	rm  -rf ~/.npm
	npm cache clear

References
----------
1. [https://medium.com](https://medium.com/@jamesseanwright/automatically-validate-html-with-node-js-and-valimate-196c71a349bf#.g4dsvqfhv)

## Credit to `James Wright`
