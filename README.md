# DocCheck Provider for OAuth 2.0 Client
This package provides DocCheck OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```bash
composer require doccheck/oauth2-doccheck
```

## Usage

Usage is the same as The League's OAuth client, using `\Doccheck\OAuth2\Client\Provider\Doccheck` as the provider.

### Configuration

When initializing the provider, you can pass the following options:

| Option | Type | Description |
| --- | --- | --- |
| `clientId` | `string` | Your DocCheck Client ID. |
| `clientSecret` | `string` | Your DocCheck Client Secret. |
| `redirectUri` | `string` | The URL to redirect back to after authorization. **Must match exactly** (see below). |
| `stateless` | `bool` | Set to `true` if your client has a basic license or to disable the `state` parameter (defaults to `false`). |
| `authorizationLanguage` | `Language` | The language for the authorization page. (defaults to `Language::EN`). |

> [!IMPORTANT]  
> **Redirect URL must match exactly!**  
> The `redirectUri` provided in the configuration **must be identical** to the one you have configured in the DocCheck CPH (Client Control Center).  
> Common mistakes that cause errors:
> - **Missing/Extra `www`**: `https://example.com` is NOT the same as `https://www.example.com`.
> - **Missing/Extra trailing slash**: `https://example.com/callback` is NOT the same as `https://example.com/callback/`.
> - **Different Protocol**: `http` is NOT the same as `https`.

### Authorization Code Flow

```php
require_once('./vendor/autoload.php');

use Doccheck\OAuth2\Client\Provider\Doccheck;
use Doccheck\OAuth2\Client\Utils\Language;

session_start();

$provider = new Doccheck([
    'clientId'              => '{doccheck-client-id}',
    'clientSecret'          => '{doccheck-client-secret}',
    'redirectUri'           => 'https://example.com/callback-url',
    'stateless'            => false, // set true if client has basic license or to prevent state parameter 
    'authorizationLanguage' => Language::DE, // Optional: defaults to EN
]);

if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl([
        'scope' => ['email'], // Add required scopes
    ]);
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;
// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
} else {
    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $user->getEmail());

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear... ' . $e->getMessage());
    }

    // Use this to interact with an API on the users behalf
    // echo $token->getToken();
}
```

### Resource Owner

The `getResourceOwner` method returns a `DoccheckResourceOwner` instance which provides the following methods:

- `getId()`: The unique DocCheck user ID.
- `getEmail()`: The user's email address.
- [...]
- `toArray()`: Returns all available user data as an array.

*Note: Availability of data depends on the requested scopes and the user's DocCheck account.*

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## License

The MIT License (MIT).
