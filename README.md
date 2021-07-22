# wc-filter
Plugin for woocommerce that filters products by categories, name, slug etc ...


###EndPoints:

Filter products by name

https://store-example.com/wp-json/wc-filter/v1/filter/

###Response:

```json
[
    {
        "id": "6978",
        "post_name": "chanel-clermont-rosa",
        "post_title": "Chanel Clermont Rosa",
        "guid": "http://tubolso.com.co/producto/chanel-clermont-rosa/",
        "image": "https://tubolso.com.co/wp-content/uploads/2021/07/6-360x210.jpg"
    }
    ...
]
```