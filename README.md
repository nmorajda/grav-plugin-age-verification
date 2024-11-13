# Age Verification Plugin

The **Age Verification** Plugin is an extension for [Grav CMS](https://github.com/getgrav/grav). 

## Installation

### Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `your-site-grav/user/plugins`. Then rename the folder to `age-verification`. You can find these files on [GitHub](https://github.com/nmorajda/grav-plugin-age-verification).

You should now have all the plugin files under

```bash
    /your/site/grav/user/plugins/age-verification
```

After installation, it's recommended to **copy** the configuration file `user/plugins/age-verification/age-verification.yaml` to `user/config/plugins/` and the template file `user/plugins/age-verification/templates/age-verification.html.twig` to `user/themes/your-theme/templates/` to prevent them from being overwritten by future plugin updates.

## Usage

Once installed, the plugin will prompt users to confirm they are 18 years or older before accessing the site's content. 

## Why Server-Side Verification

Age verification is handled server-side to ensure that access restrictions cannot be bypassed by disabling JavaScript or manipulating client-side scripts. This approach provides a more secure and reliable method of enforcing age restrictions.

## Configuration

You can customize the plugin by editing the `age-verification.yaml` file. The following options are available:

- **enabled**:  
  Enables or disables the age verification plugin functionality.
  **Default:** `true` 

- **built_in_css**:  
  Determines if the plugin's default CSS styling is applied.
  **Default:** `enabled` 

- **cookie_duration**:  
  Defines the duration (in seconds) for which the age verification cookie remains valid.  
  **Default:** `60` (60 seconds)

- **cookie_name**:  
  Sets the name of the age verification cookie.  
  **Default:** `age_verified`

- **redirect_url**:  
  External URL to redirect underage users
  **Default:** `https://www.google.com`

**Example 1**

```yaml
enabled: true
built_in_css: true
cookie_duration: 60 # seconds
cookie_name: 'age_verified'
redirect_url: 'https://www.google.com' # URL to redirect underage users
```

**Example 2**

```yaml
enabled: true
built_in_css: false
cookie_duration: 86400 # 1 day in seconds (60 seconds * 60 minuts * 24 hours)
cookie_name: 'user-age-verified'
redirect_url: 'https://abmstudio.pl' # URL to redirect underage users
```

## Default Styling

The plugin includes a default CSS file located in the `user/plugins/age-verification/assets` directory. This file defines the basic styling for the age verification prompt to ensure it has a consistent and user-friendly appearance. If `built_in_css` is set to `true` in the configuration file, these styles will be automatically applied. You can modify this file directly or add custom CSS in your theme to override the default styles as needed.

**Location of default CSS file:**

```bash
    user/plugins/age-verification/assets/age-verification.css
```
