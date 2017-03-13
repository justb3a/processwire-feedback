# Basic HTTP Authentication

##  Example using curl 

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
