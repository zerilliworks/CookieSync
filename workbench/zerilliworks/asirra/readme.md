#Asirra for Laravel

[Asirra](http://research.microsoft.com/en-us/um/redmond/projects/asirra/) is a product of Microsoft Research that uses
images of cats and dogs as a CAPTCHA.

Asirra is already zero-config, so all this package does is wrap up validation nicely and add two things to Laravel:

## The Form Macro

Insert `{{ Form::asirra() }}` within your forms to inject the Asirra script. There are no options to configure.

## The Validator

Use the `asirra` form validation method to automatically verify an Asirra token. Use it like so:

```php
$creds = array(
    'name' => Input::get('username'),
    'password' => Input::get('password'),
    'password_confirmation' => Input::get('password_confirmation'),
    'asirra_ticket' => Input::get('Asirra_Ticket')
);

// Get the Asirra ticket from the form data
$creds['asirra_ticket'] = Input::get('Asirra_Ticket');

$rules = array(
               'name' => 'required',
               'password' => 'required|confirmed',

               // Use the asirra validator to check the ticket
               'asirra_ticket' => 'required|asirra'
          ));

$v = Validator::make($creds, $rules);

if($v->passes()) {...}
```

Just `composer require zerilliworks/asirra`, add `'Zerilliworks\Asirra\AsirraServiceProvider',` to your list of
providers in app/config/app.php, and voila!. Instant zero-config animal-based CAPTCHA.