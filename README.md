# PicoLock Plugin

A simple "hide your Pico CMS website behind a password" plugin.
Meant for those who do not need all the authentification options the big [PicoAuth](https://github.com/picoauth/picoauth) plugin provides.

The password mechanism and password screen design was copied from [astappiev/pico-editor](https://github.com/astappiev/pico-editor).

## Features
- Tested with Pico CMS 3.0 Alpha
- Show password screen in front of all pages (extending plugin to support specific pages should be easy)
- Messages configurable through config file

## Install
1. Extract a copy of the plugin into your Pico "plugins" folder (should be plugins/PicoLock/PicoLock.php)
   - via Composer `composer require mswiege/picolock main`
   - or `cd plugins && git clone https://github.com/mswiege/picolock.git PicoLock`
2. Set password in Pico's configuration file: check [Configuration](#configuration)

## Configuration
The configuration can be specified in Pico's `config/config.yml`
```yml
# Pico Editor Configuration
PicoEditor:
    password: SHA512-HASHED-PASSWORD        # You have to use your own password (you should use hash, not raw password! https://sha512.online/)
    enterPasswordMessage: "Please enter password:"            # to change the message before the password field (optional)
    wrongPasswordMessage: "Wrong password. Please try again." # to change the error message if a wrong password was entered (optional)
```
