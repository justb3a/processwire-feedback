# HMAC SHA Authentication

HMAC-SHA authentication allows you to implement very simple key / secret authentication for your API using hashed signatures.

## Making a request

**Given prerequisites:**

- **method**: POST
- **endpoint**: https://api.url/endpoint/
- **auth params**: auth_key, auth_timestamp, auth_version = “5.1.2”
- **request params**: fields e.g. email, message, name, subject
- **auth_secret**

**Steps:**

1. merge auth and request params, urlencode them
2. prepend urlencoded string with method and endpoint, divide them by `\n`
3. generate a keyed hash value using the HMAC method and the hashing algorithm sha256 by using the auth_secret
4. merge auth_params, hashed signature and request params, send the request via POST

## Example

### 1. merge auth and request params

```
{
  "auth_key" => $key,
  "auth_timestamp" => 1489405278,
  "auth_version" => "5.1.2",
  "email" => "test@web.de",
  "message" => "some content",
  "name" => "a name",
  "subject" => "2"
}
```

**`= $params`**

### 2. prepend urlencoded string with method and endpoint

- method **POST**
- endpoint
- urlencoded **$params**

Divide them by `\n`.

```
POST\n
https://api.url/endpoint/\n
auth_key=xxx&auth_timestamp=1489405425&auth_version=5.1.2&email=test@kfi.io&message=some content&name=a name&subject=2
```

**`= $payload`**

### 3. Generate Hash

Generate a keyed hash value using the HMAC method. Used hashing algorithm: **sha256**.

```
hash_hmac(
  'sha256',
  $payload,
  $secret
)
```

**= $signature**

### 4. Merge params and send request

Merge auth params, hashed signature and request params.

```
{ 
  "auth_version" => "5.1.2", 
  "auth_key" => "xxx", 
  "auth_timestamp" => 1489405704, 
  "auth_signature" => $signature,
  "email" => "test@web.de",
  "message" => "some content",
  "name" => "a name",
  "subject" => "2"
}
```

**`= $queryParams`**

Send it via POST.

```
$http->post($path, json_encode($queryParams));
```
