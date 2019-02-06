# AStad M-Profiel OAuth 2.0 Client Provider

This package provides AStad M-Profiel OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require tactics/oauth2-mprofiel
```

## Usage

Usage is the same as The League's OAuth client, using this M-Profiel provider as the provider.

### Authorization Code Flow

```php
$provider = new MProfielProvider([
    'environment'   => 'dev',
    'clientId'      => '{your-client-id}',
    'clientSecret'  => '{your-client-secret}',
    'redirectUri'   => 'https://example.com/callback-url'
]);

// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {
    
    // Fetch the authorization URL from the provider; this returns the
    // urlAuthorize option and generates and applies any necessary parameters
    // (e.g. state).
    $authorizationUrl = $provider->getAuthorizationUrl();
    
    // Get the state generated for you and store it to the session.
    $_SESSION['oauth2state'] = $provider->getState();
    
    // Redirect the user to the authorization URL.
    header('Location: ' . $authorizationUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
    
    if (isset($_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
    }
    
    exit('Invalid state');
    
} else {
    
    try {
        
        // Try to get an access token using the authorization code grant.
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);
        
        // Using the access token, we may look up details about the resource owner.
        $resourceOwner = $provider->getResourceOwner($accessToken);
    
        echo 'Id: ' . $resourceOwner->id() . "<br>";
        echo 'userName: ' . $resourceOwner->userName() . "<br>";
        echo 'firstName: ' . $resourceOwner->firstName() . "<br>";
        echo 'lastName: ' . $resourceOwner->lastName() . "<br>";
        echo 'avatarUrl: ' . $resourceOwner->avatarUrl() . "<br>";
        echo 'emailPrimary: ' . $resourceOwner->email() . "<br>";
        echo 'phonePrimary: ' . $resourceOwner->phone() . "<br>";
    
    } catch (Exception $e) {
        // Failed to get the access token or user details.
        exit($e->getMessage());
    }
    
}
```

## License

The MIT License (MIT). Please see [License File](https://github.com/tactics/oauth2-aprofiel/blob/master/LICENSE) for more information.
