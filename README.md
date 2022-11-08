# MAIB payment method for OpenCart

####
[![N|Solid](https://www.maib.md/images/logo.svg)](https://www.maib.md)

Download extension (v. 3.x and 4.x)

https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=44246

Github: https://github.com/maibank/opencart-maib


CONTENTS OF THIS FILE
=====================

 * Introduction
 * Requirements
 * Recommended modules
 * Installation
 * Before usage
 * Usage
 * Troubleshoting
 * Maintainers


INTRODUCTION
============

This extension is used to easily integrate the MAIB Payment method into your OpenCart project.

It is based on the maibapi library freely available on https://github.com/maibank/maibapi.

REQUIREMENTS
============

 * PHP: >=7.1.0
 * OpenCart: 3.x


INSTALLATION
============

 * Method 1 (preferred): via Marketplace > Extensions page of you site administration panel.
 * Method 2: upload extension zip file to Marketplace > Extensions installer page of your site.
 * Method 3 (advanced): extract contents of zip file over your site.
 * Method 4 (developper): clone or download extension source code from github, run composer install in the library folder and copy over site files. 


BEFORE USAGE
============

Is required to provide IP and Callback from your ecommerce solution!

Write an email to maib ecommerce support (ecom@maib.md) and indicate your site's external IP address and Callback URL (it is available on the extensions settings page after installation).


USAGE
=====

 * Adjust accordingly extensions options on admin page.
 * Test certificates you can find in github repository.
 * **The following tests are required: payment, payment reversal and closing of business day.**
 * For payment reversal change status order to ***Reversed***. Funds will be returned to the client. (reversal.png)
 * Setup a cron job which will trigger closing of business day somethere around midnight (23:59). Wget or curl can be used. Ex: "*wget https://your.site/index.php?route=extension/payment/maib/closeday*"
 * After successful tests you will get .pfx certificate for live transactions from maib. Extract pem keys (openssl commands can be found on extensions settings page), indicate keys location path on settings page and change the mode from testing to live.


TROUBLESHOTING
==============

Enable debug option on settings page, this will activate full requests logging to maib_requests.log file in system log directory.

If further support is required, you can contact bank ecommerce support by writing an email to ecom@maib.md.
Please provide in the mail following information:

- Merchant name;
- Web site name;
- Date and time of the transaction made with errors;
- Responses received from the server;


MAINTAINERS
===========

Current maintainers:

 * [Indrivo](https://github.com/indrivo)
