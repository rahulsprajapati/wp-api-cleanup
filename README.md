WP API Cleanup
===

Cleanup data of WP using Rest API Endpoint. ie. posts, media, tags.
Note: Usecase is to reset data before reimport using REST API.

Works like wp cli command: wp site empty --yes --uploads 

## How it works

1. Register Endpoint: `wp-json/webhook/v1/cleanup`
2. Delete all posts, media files and terms on `DELETE` request. With a token value which need to be set from `WP_API_CLEANUP_TOKEN` constant.

### Example: 

Set constant in wp-config.php 

define( 'WP_API_CLEANUP_TOKEN', 'token_value' );

Use:

https://example.com/wp-json/webhook/v1/cleanup with DELETE method and { token: 'token_value' } data.


