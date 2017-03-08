# ProcessWire Feedback

Module to be able to give feedback easily  
in order to handle feedback better  
because we want to make the process of getting feedback more transparent.

## Create templates and pages in the PW backend to get an API endpoint

The API endpoint is the page where the API can be accessed. Go to your ProcessWire site admin and:

- create a new templates: It’s enough to assign only the title field
    - in the *Files Tab* set *Content-Type* to `application/json`
    - in the *Files Tab* as well enable *Disable automatic prepend of file: xxx.php*
- create a new page for the *”api*”
    -  assign the just created template
    -  set the page status to *Hidden* 

## Module settings

First you've to decide whether you want to use **Basic HTTP Authentication** or **Key/Secret using hashed signatures** (strongly recommended!).

> The popular choice is HTTP Basic because all you've to do is to pass your username and password.
> However sending such information across the wire isn't the most secure approach.
> OAuth is another popular choice, but often it's an overkill.
> Sending the request as a hash (using a shared key and secret including a timestamp so the hash will be different every time) is a good alternative.

Fill in module settings, add all fields you want to attach to the form. You could either use existing fields or create new ones. All new fields will be prefixed with `feedback_`.

If you want to change the field settings, edit the field and change all settings there (e.g. fieldtype, required, length).

Assign the created page as parent.

## Basic Usage

Edit the template of the page you’ve selected as parent page. 

```php
echo $modules->get('Feedback')->render(); // OR
echo $modules->get('Feedback')->saveFeedback(); 
```

It’s important that this template includes *”only”* the line above.

### Render Form

```php
$options = array(
  'btnText' => 'Let me fly',
  'btnClass' => 'button',
  'classes' => array(
    'item' => 'form-item  form-item__{name}'
  )
));

$key = 'optionalKey'; // if empty the first one from module settings will be taken

echo $modules->get('Feedback')->render($options, $key); 

echo '<div class="feedback-status--' . $this->session->feedbackstatus . '"></div>';
```

The session variable **feedbackstatus** can be used to add additional content for different states.  
`$this->session->feedbackstatus` equals either `init`, `error` or `success`.

#### Available Keys

| key                | type    | description                                                |
| ---                | ----    | -----------                                                |
| btnClass           | string  | add custom submit button class(es)                         |
| btnText            | string  | add custom submit button text, defaults to `Send`          |
| markup             | array   | overwrite markup                                           |
| classes            | array   | overwrite classes                                          |

To get an overview of what's possible, have a look at [How to overwrite classes and markup][1]

### Sending Feedback

```php
$params = array(
  'field1' => 'value1',
  'field2' => 'value2',
  ..
);

$key = 'optionalKey'; // if empty the first one from module settings will be taken

$modules->get('Feedback')->sendFeedback($params, $key);
```

## API

### Endpoint

```
POST https://api.url/endpoint/
```

### Parameters

All the fields you've added in module settings.

### Content Type

Must be set to `application/json`, otherwise your request will be rejected.

### Authentication

This module uses basic HTTP authentication with username and password. In combination with the https protocol this should be fairly safe.

### Response

By default, the response will take the following form:

**Status Code**: 201 Created

```
{"success":true}
```

### Common Errors

There are an additional set of errors that can occur. The following examples show JSON responses.

#### Incorrect client credentials

If the apiUser or apiKey you pass are incorrect you will receive this error response.

**Status Code**: 401 Unauthorized

```
{  
  "success":false, 
  "error": "incorrect_client_credentials", 
  "error_description":"Authorization failed."
}
```

#### Incorrect request method

If the request method does not equal **`POST`** you will receive this error response.

**Status Code**: 400 Bad Request

```
{  
  "success":false, 
  "error": "incorrect_request_method", 
  "error_description":"Incorrect request method."
}
```

#### Incorrect content type

If the content type does not match **`application/json`** you will receive the following error response.

**Status Code**: 400 Bad Request

```
{  
  "success":false, 
  "error":"incorrect_content_type",
  "error_description":"Incorrect content type."
}
```

#### No params have been passed

If the request does not contain any parameters the response looks like the following:

**Status Code**: 400 Bad Request

```
{  
  "success":false, 
  "error":"missing_parameters",
  "error_description":"Parameters are missing."
}
```

#### Feedback could not be saved

If the feedback could not be saved you will receive this error message:

**Status Code**: 503 Service Unavailable

```
{  
  "success":false, 
  "error":"error_saving_request",
  "error_description":"Page could not be successfully created."
}
```

#### Validation Error

If the parameters does not pass the validation you get the following error response:

**Status Code**: 400 Bad Request

```
{  
  "success":false, 
  "error":"validation_error",
  "error_description":"Validation error.",
  "errors":
    {
      "email":"Please enter a valid e-mail address",
      "subject":"Missing required value"
    }
}
```

### Example using curl [Basic HTTP Authentication]

```
curl -i -H "Content-Type: application/json" --user myuser:12345 -X POST -d '{"field1":"2","field2":"test","field3":"xyz@foo.de","field4":"It Works :)"}' https://api.url/endpoint/
```

Response

```
HTTP/1.1 201 Created
Server: nginx/1.11.3
Date: Wed, 22 Feb 2017 10:46:08 GMT
Content-Type: application/json
Transfer-Encoding: chunked
Connection: keep-alive
Set-Cookie: wire=le6grhc6htrii3t8k5cb8capt5; path=/; HttpOnly
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Cache-Control: no-store, no-cache, must-revalidate
Pragma: no-cache
X-Powered-By: ProcessWire CMS

{"success":true}
```

## Testing

**[behat](http://behat.org/)**: A php framework for autotesting your business expectations.

First you've to install the required composer packages:

```
composer install
```

Then copy `behat.yml.example` and rename it to `behat.yml`.  
Now edit the copied file and replace everything below `AuthenticationContext`:

1. the baseurl containing `http[s]`
2. valid apiKey
3. valid apiSecret
4. json including all needed parameters including valid values

If you're using **Basic HTTP Authentication** use test suite **basichttp**, otherwise use **hashed**.

Now you should be able to execute the following command:

```
$ vendor/bin/behat --suite=basichttp
$ vendor/bin/behat --suite=hashed
```

You can also run behat scenarios using tag filters:

```
$ vendor/bin/behat --suite=hashed --tags @send
$ vendor/bin/behat --suite=hashed --tags @receive
$ vendor/bin/behat --suite=hashed --tags "@send&&@success"
$ vendor/bin/behat --suite=hashed --tags "@send,@success"
$ vendor/bin/behat --suite=hashed --tags "~@success"
```

As you see tag filters supports different logical operators:

1. **AND**: separation by `&&`
2. **OR**: separation by `comma`
3. **NOT**: prefixing a `~`

Or for example you can run a scenario by using a part of the name:

```
$ vendor/bin/behat --suite=hashed --name 'Send feedback'
$ vendor/bin/behat --suite=hashed --name 'Unsuccessfull api request'
```

[1]: https://github.com/justb3a/processwire-simplecontactform/blob/master/doc/overwrite-classes-and-markup.md 'How to overwrite classes and markup'
