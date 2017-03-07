# EnMarcheMailjetBundle
This bundle provide an easy way to integrate Mailjet email service into a Symfony application.

## Installation
Run the following command from your project root directory:
```bash
$ composer install en-marche/mailjet-bundle
```
Activate the bundle into your `app/AppKernel.php` file:
```php
    public function registerBundles()
    {
        $bundles = [
            new EnMarche\Bundle\MailjetBundle\EnMarcheMailjetBundle(),
            // ... my other bundles
        ];
        
        return $bundles;
    }
```

Provide the configuration into your `app/config.yml` file:
```yaml
en_marche_mailjet:
    public_key: "my-service-pub-key"
    private_key: "my-service-private-key"
    sender_email: "johndoe@test.com" # The email address from which email will be sent from
    sender_name: "John Doe" # The name from which email will be sent from
    services:       # Optionnal section to override some used services
        transport: "api" # Value can be one of "api" or "null" (don't forget the quotes)
```

## Usage
Create a new Message class that inherits the `EnMarche\Bundle\MailjetBundle\Message\MailjetMessage` class.