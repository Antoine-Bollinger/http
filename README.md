# Abollinger\Http\Client

**Abollinger\Http\Client** is a lightweight, static PHP HTTP client for making GET, POST, PUT, and DELETE requests. It simplifies HTTP interactions with support for custom headers, data payloads, and response parsing (including headers and body).

---

## Features

- **Static Methods**: Easy-to-use static methods for common HTTP verbs (`get`, `post`, `put`, `delete`).
- **Header Support**: Custom headers for requests and parsed response headers.
- **JSON Parsing**: Automatic JSON decoding for response bodies, with fallback to raw output.
- **Error Handling**: Clear error messages and HTTP status code validation.
- **cURL-Based**: Uses PHP’s cURL for reliable and flexible HTTP communication.

---

## Requirements

- PHP 7.4 or higher
- cURL extension enabled
- Composer autoloading

---

## Installation

Include the class via Composer autoloading. Install the Abollinger\Http package using composer. Run the following command: 

```bash
composer require abollinger/http
```

Then, load the class in your code:

```php
require_once 'vendor/autoload.php';

use Abollinger\Http\Client;
```

---

## Usage

### Making a GET Request

```php
$response = Client::get([
    'url' => 'https://api.example.com/data',
    'headers' => ['Authorization' => 'Bearer token123']
]);
```

### Making a POST Request

```php
$response = Client::post([
    'url' => 'https://api.example.com/data',
    'data' => ['key' => 'value'],
    'headers' => ['Content-Type' => 'application/json']
]);
```

### Retrieving Headers Only

```php
$headers = Client::getHeaders([
    'url' => 'https://api.example.com/data'
]);
```

You can use this method to obtain normalized headers (example: "content type" becomes "Content-Type"):

```php
$headers = Client::getNormalizedHeaders([
    'url' => 'https://api.example.com/data'
]);
```

### Handling the Response

The response is always an associative array with the following structure:

```php
[
    'status' => 200, // HTTP status code
    'headers' => ['Content-Type' => 'application/json', ...], // Parsed headers
    'body' => ['data' => '...'] // Parsed body (or ['raw' => '...'] if not JSON)
]
```

### Error Handling

If the request fails, the response will include an `error` key:

```php
if (isset($response['error'])) {
    echo "Error: " . $response['error'];
}
```

---

## API Reference

### Static Methods

#### `get(array $params = []): array`
Performs a GET request. Returns the parsed response body.

#### `getHeaders(array $params = []): array`
Performs a GET request. Returns the parsed response headers.

#### `getNormalizedHeaders(array $params = []): array`
Performs a GET request. Normalize and returns the parsed response headers.

#### `post(array $params = []): array`
Performs a POST request. Returns the parsed response body.

#### `put(array $params = []): array`
Performs a PUT request. Returns the parsed response body.

#### `delete(array $params = []): array`
Performs a DELETE request. Returns the parsed response body.

---

### Parameters

All methods accept an associative array of parameters:

| Key      | Type   | Description                                                                 |
|----------|--------|-----------------------------------------------------------------------------|
| `url`    | string | **Required.** The target URL.                                               |
| `method` | string | HTTP method (GET, POST, PUT, DELETE). Default: `GET`.                       |
| `data`   | array  | Request payload. Automatically URL-encoded for GET, or sent as-is for POST/PUT. |
| `headers`| array  | Custom headers. Default: `['Content-Type: application/x-www-form-urlencoded']`. |

---

## Example Workflow

```php
use Abollinger\Http\Client;

// GET request
$data = Client::get(['url' => 'https://api.example.com/users']);

// POST request with custom headers
$result = Client::post([
    'url' => 'https://api.example.com/users',
    'data' => ['name' => 'John Doe'],
    'headers' => ['Authorization' => 'Bearer token123']
]);
```

---

## License

This library is licensed under the MIT License. For full license details, see the `LICENSE` file distributed with this source code.

---

## Author

Antoine Bollinger
Email: abollinger@partez.net

For contributions, issues, or feedback, feel free to reach out or open an issue on the relevant repository.
