# Age Verification Plugin

The **Age Verification** Plugin is an extension for [Grav CMS](https://github.com/getgrav/grav). 

## Installation

### Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `age-verification`. You can find these files on [GitHub](https://github.com/nmorajda/grav-plugin-age-verification).

You should now have all the plugin files under

    /your/site/grav/user/plugins/age-verification


## Usage

Once installed, the plugin will prompt users to confirm they are 18 years or older before accessing the site's content. 

## Why Server-Side Verification

Age verification is handled server-side to ensure that access restrictions cannot be bypassed by disabling JavaScript or manipulating client-side scripts. This approach provides a more secure and reliable method of enforcing age restrictions.

## Configuration

You can customize the plugin by editing the `age-verification.yaml` file located in `user/config/plugins/`. The following options are available:

- **cookie_duration**:  
  Defines the duration (in seconds) for which the age verification cookie remains valid.  
  **Default:** `2592000` (30 days)

- **cookie_name**:  
  Sets the name of the age verification cookie.  
  **Default:** `age_verified`

To modify these settings, open the `age-verification.yaml` file and adjust the values as needed. For example:

```yaml
enabled: true
cookie_duration: 60 # seconds
cookie_name: 'age_verified'
```

**Example **

```yaml
enabled: true
cookie_duration: 604800 # 7 days in seconds (60 seconds * 24 hours * 30 days)
cookie_name: 'age_verified'
```


