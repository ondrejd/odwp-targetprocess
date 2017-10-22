# odwp-targetprocess

[WordPress][1] plugin that uses [Targetprocess][2] API to publish user stories on your site.

## Main features

* ...

## Screenshots

### Settings ([WordPress][1] administration > Settings > General)

![Plugin settings](screenshot-1.png)

## TODO

* [x] ~~create settings with `odwptp_login` and `odwptp_password` (using [Settings API][4])~~
* [ ] if `odwptp_login` and `odwptp_password` are set try to get [token][3]
* [ ] create shortcode which will present the data on front-end
* [ ] check if `WP_HTTP_BLOCK_EXTERNAL` is `TRUE` and take appropriate action if not
* [ ] __add setting `odwptp_url` which holds URL of Targetprocess API serverhost__
* [ ] enable localization (Czech and English)

[1]: https://wordpress.org/
[2]: https://www.targetprocess.com/
[3]: https://dev.targetprocess.com/docs/authentication
[4]: https://developer.wordpress.org/plugins/settings/settings-api/
