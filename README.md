# WARNING: This repository is no longer maintained :warning:

> This repository will not be updated. The repository will be kept available in read-only mode.

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

#### Spam Protection: Hide honeypot field using CSS

Spam bots fill in automatically all form fields. By adding an invisible field you're able to trick the bots. The key to the honeypot technique is that the form only can be sent when the honeypot field remains empty otherwise it will be treated as spam.

The honeypot technique doesn't interfere with the user experience. It demands nothing extra of them like a captcha does. In fact, user won't even notice you're using it.

All that's required is a visually hidden form field. This form adds such a field named `scf-website` by default but you have to make sure to add a **display: none;** CSS rule on it.

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

## Read more

- [Common Errors][2]
- [Testing][3]
- [HTTP Basic Authentication][4]
- [Key/Secret using hashed signatures Authentication][5]

[1]: https://github.com/justb3a/processwire-simplecontactform/blob/master/doc/overwrite-classes-and-markup.md 'How to overwrite classes and markup'
[2]: doc/errors.md      "Common Errors"
[3]: doc/testing.md     "Testing"
[4]: doc/httpauth.md    "HTTP Basic Authentication"
[5]: doc/hashedauth.md  "Key/Secret using hashed signatures Authentication"

