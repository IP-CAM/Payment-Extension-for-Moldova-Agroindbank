# MAIB payment method for OpenCart

[![N|Solid](https://www.maib.md/images/logo.svg)](https://www.maib.md)

Download extension (v.2.3.x / v.3.x / v.4.x)

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

 * OpenCart 2.3.x: PHP >= 7.2
 * OpenCart 3.x: PHP >= 7.2
 * OpenCart 4.x: PHP >= 8.1

INSTALLATION
============

 * Method 1 (preferred): via Marketplace page in administration panel.
 * Method 2: upload extension zip file to Extensions installer page in administration panel.
 * Method 3 (developper): clone or download extension source code from github, run composer install in the library folder, create and upload extension zip file on Extensions installer page. 


BEFORE USAGE
============

Is required to provide IP and Callback from your ecommerce solution!

Write an email to maib ecommerce support (ecom@maib.md) and indicate your site's external IP address and Callback URL (it is available on the extensions settings page after installation).


USAGE
=====

 * Adjust accordingly extensions options on admin page.
 * Test certificates you can find in this github repository.
 * **The following tests are required: payment, payment reversal and closing of business day.**
 * For payment reversal change status order to ***Reversed*** (order_status_id=12). Please see *reversal.png*. Funds will be returned to the client.
 * Make sure cron is setup properly on your site, this extension will make automated requests at midnight in order to close business day.
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

 * [Constantin](https://github.com/kostealupu)
 * [Indrivo](https://github.com/indrivo)
