<p align="center">
  <img src="https://avatars.githubusercontent.com/u/152215067?s=200&v=4" height="80">
</p>

# LabsMobile-Moodle

![](https://img.shields.io/badge/version-2.0-blue.svg)

Send SMS messages through the LabsMobile platform and the plugin for Moodle. Sign up to install the plugin, and in seconds you will be able to send SMS messages.

## Documentation

Labsmobile API documentation can be found[here][apidocs].

## Features

- SMS notifications to participants and students registered with Moodle.
- Send both individual and bulk messages.
- Manage templates and check the status of sent messages.

## Requirements

- Moodle v5.1 or higher information at [Moodle.org][moodle].
- LabsMobile module for Moodle.
- A user account with LabsMobile. Click on the link to create an account [here][signUp].

## Installation

- Download the Plugin corresponding to the version of your Moodle installation at the bottom of this page.
- Sign in into your Moodle installation with a user with Administrator privileges.
- Activate block mode.
- Install the Plugin with the downloaded .ZIP file following the instructions and configuring the plugin type as Blocks.
- At the end of the installation, the plugin configuration screen will be displayed. It is necessary to enter the following information:
  *v5.1 or higher*

  - username: Email that corresponds to the account's registration username.
  - password: API token generated from the Security and passwords option of the LabsMobile account.
  - sender: Numeric or alphanumeric sender of up to 11 characters (only in countries that allow this functionality).
- Go to the Home screen or the desired section of Moodle to add the SMS Notifier block.
- Two links will appear Send SMS and Create a message template.
- When creating a template in version  *v5.1 or higher* , you can add the following  variables to personalize the message :

  * `%VAR_COURSE%`
  * `%VAR_DEPARTMENT%`
  * `%VAR_FIRSTNAME%`
  * `%VAR_ADDRESS%`
  * `%VAR_LASTNAME%`
  * `%VAR_EMAIL%`
  * `%VAR_USERNAME%`
  * `%VAR_INSTITUTION%`
  * `%VAR_CITY%`

## Help

If you have questions, you can contact us through the support chat or through the support email support@labsmobile.com.

[apidocs]: https://apidocs.labsmobile.com/
[signUp]: https://www.labsmobile.com/en/signup
[sdk]: https://www.labsmobile.com/data/labs-mobile-android-sdk.zip
[moodle]: https://moodle.org/
