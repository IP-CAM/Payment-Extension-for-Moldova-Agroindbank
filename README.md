# Moldova Agroindbank (maib) payment method for OpenCart

[![N|Solid](https://www.maib.md/images/logo.svg)](https://www.maib.md)

https://github.com/maibank/oc-maib

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

 * PHP: >=8.1.0
 * OpenCart: 4.x


INSTALLATION
============

 * Method 1 (preferred): via Marketplace > Extensions page of you site administration panel.
 * Method 2: upload extension zip file to Marketplace > Extensions installer page of your site.
 * Method 3 (developper): clone or download extension source code from github, run composer install in the library folder, create and upload extension zip file on Extensions installer page. 


BEFORE USAGE
============

To initiate an payment transaction you will need get the access by IP and set the return callback URL of your site at bank side.

Write an email to Maib commerce support (ecom@maib.md) and indicate your site's external IP address and callback URL (it is available on the extensions settings page after installation).


USAGE
=====

 * Adjust accordingly extensions options on admin page.
 * Follow mail instructions in order to perform few payments in testing mode (adjust extensions setings accordingly).
 * Make sure cron is setup properly on your site, this extension will make automated requests to bank at midnight in order to close business day.
 * After you get the certificate, extract pem keys (openssl commands can be found on extensions settings page), indicate keys location path on settings page and change the mode from testing to live.
 * If received client redirect and/or merchant urls do not match extensions default live urls - add this urls to admin and catalog config.php files (details on settings page) and adjust mode option to use new urls.


TROUBLESHOTING
==============

Enable debug option on settings page, this will activate full requests logging to maib_requests.log file in system log directory.

If further support is required, you can contact bank ecommerce support by writing an email to ecom@maib.md.
Please provide in the mail following information:

- Merchant name,
- Web site name,
- Date and time of the transaction made with errors
- Responses received from the server


MAINTAINERS
===========

Current maintainers:

 * [Indrivo](https://github.com/indrivo)
