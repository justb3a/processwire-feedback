# Common Errors

There are an additional set of errors that can occur. The following examples show JSON responses.

## Incorrect client credentials

If the apiUser or apiKey you pass are incorrect you will receive this error response.

**Status Code**: 401 Unauthorized

```
{  
  "success":false, 
  "error": "incorrect_client_credentials", 
  "error_description":"Authorization failed."
}
```

## Incorrect request method

If the request method does not equal **`POST`** you will receive this error response.

**Status Code**: 400 Bad Request

```
{  
  "success":false, 
  "error": "incorrect_request_method", 
  "error_description":"Incorrect request method."
}
```

## Incorrect content type

If the content type does not match **`application/json`** you will receive the following error response.

**Status Code**: 400 Bad Request

```
{  
  "success":false, 
  "error":"incorrect_content_type",
  "error_description":"Incorrect content type."
}
```

## No params have been passed

If the request does not contain any parameters the response looks like the following:

**Status Code**: 400 Bad Request

```
{  
  "success":false, 
  "error":"missing_parameters",
  "error_description":"Parameters are missing."
}
```

## Feedback could not be saved

If the feedback could not be saved you will receive this error message:

**Status Code**: 503 Service Unavailable

```
{  
  "success":false, 
  "error":"error_saving_request",
  "error_description":"Page could not be successfully created."
}
```

## Validation Error

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

