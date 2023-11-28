<p align="center">
  <img src="https://avatars.githubusercontent.com/u/152215067?s=200&v=4" height="80">
</p>

# LabsMobile-Android

![](https://img.shields.io/badge/version-1.0.0-blue.svg)
 
Send SMS messages through the LabsMobile platform and the plugin for Moodle. Sign up to install the plugin, and in seconds you will be able to send SMS messages.

## Documentation

Labsmobile API documentation can be found[here][apidocs].

## Features
  - SMS notifications to participants and students registered with Moodle.
  - Send both individual and bulk messages.
  - Manage templates and check the status of sent messages.

## Requirements

- Moodle v.2x y v.3x. More information at [Moodle.org][moodle].
- LabsMobile module for Moodle.
- A user account with LabsMobile. Click on the link to create an account [here][signUp].


## Installation

**Place the LabsMobile SDK in the libs directory of your application module.**

1. Download the Plugin corresponding to the version of your Moodle installation at the bottom of this page.

2. Sign in into your Moodle installation with a user with Administrator privileges.

3. Activate block mode.

4. Install the Plugin with the downloaded .ZIP file following the instructions and configuring the plugin type as Blocks.

5. At the end of the installation, the plugin configuration screen will be displayed. It is necessary to enter the following information:

v2.X - v3.6
- apikey: Unused field. You can leave it blank or enter any value.
- username: Email that corresponds to the account's registration username.
- password: API token generated from the Security and passwords option of the LabsMobile account.

6. Go to the Home screen or the desired section of Moodle to add the SMS Notifier block.

7. Two links will appear Send SMS and Create a message template.

## Samples

### Send of SMS

## Help

If you have questions, you can contact us through the support chat or through the support email support@labsmobile.com.

[apidocs]: https://apidocs.labsmobile.com/
[signUp]: https://www.labsmobile.com/en/signup
[sdk]: https://www.labsmobile.com/data/labs-mobile-android-sdk.zip
[moodle]: https://moodle.org/
