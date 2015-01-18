The purpose of this document is to describe all communication b/n the UI and the Server.
The data structures are just examples. 
They can only be accessed from a logged admin.

Request Types:
 - GET    - used when UI needs data from the server
 - POST   - used when UI needs data from the server, that is filtered in some way
 - POST   - used when creating new record
 - PUT    - used when updating existing record
 - DELETE - used when removing existing record

Response Types:
 - 200 - ok                   - must be accompanied by server response data
 - 201 - created successfully - no returned data
 - 204 - ok                   - no returned data
 - 400 - request data error   - must be accompanied by meaningful description of the error
 - 401 - not authenticated    - this should be returned when user tries to access some private data, but does not have an active session on the server
 - 403 - forbidden            - cannot call this method duo specific reasons different from "authenticated" accessed
 - 404 - resource not found   - no returned data
 - 409 - duplicate record     - returned for POST and PUT request

PUT requests may not contain all data. If some resource data was existing before the PUT request and it is not mentioned in the request, it should not be updated by the server.
Example - send PUT request to /user/:userId with data {phone: +359 888 777 666}, should update only the users phone and leave all remaining fields untouched.
By default, PUT requests should return the resulting object.

CSRF Prevention:
With each PUT, POST or DELETE request, the UI will send a XSRF-TOKEN cookie and a X-XSRF-TOKEN header, which must be identical. If they are missing or mismatched, return error 401 with appropriate message and log the event on the server side, because this was a possible hacker attack.


GUEST USER ACCESS:

Name          : Categories
route         : /categories
HTTP types    : GET
Response Type : 200
Response Data : {
            "categories": [
              {
                "id": "electronics",
                "total_items": 230,
                "subcategories": [
                  {
                    "id": "cameras",
                    "total_items": 100
                  }
                ]
              }
            ]
          }
Additional    : Top level categories must return an empty array for subcategories. Only one dept level is supported for now.
Admin users get one additional property for each category and sub-category - "enabled" of type boolean. Disabled categories will not be shown to the normal users - this is usefull when doing maintenance and adding new items.


Name          : Marketing Ads
route         : /marketing_ads
HTTP types    : GET
Response Type : 200
Request Data  : {
          "lang": "en"
        }
Response Data : {
            "slides": [
              {
                "image": "images/carousel/1.png",
                "heading": "Heading 1",
                "text": "Slide 1",
                "link": "#/faq"
              }
            ]
          }
Additional    : Link is not necessary. If given, it must be either a valid link or a valid route for the application. 


Name          : Products
route         : /products
HTTP types    : POST
Response Type : 200, 400
Request Data  : {
          "page"            : 2,
          "count"           : 20,
          "sort_by"         : "price",
          "category_id"     : "notebooks",
          "filters_required": true,
          "text_search"     : "acer",
          "lang"            : "en",
          "filters"         : {
            "filter_id"   : "filter value according to the filter type",
            "price"       : {"min": 100, "max": 500},
            "size"        : ["l", "xl"],
            "region"      : "Sofia",
            "promotion"   : true,
            "discount_pct": 30
          }
        }
Response Data : {
          "products": [
                  {
                    "id": "1",
                    "price": 100,
                    "thumbnail": "images/products/1.jpg"
                  }
                ],
          "filters": [
                  {
                    "id": "price",
                    "type": "double_slider",
                    "values": {"min": 0, "max": 640}
                  },
                  {
                    "id": "size",
                    "type": "multiple_choice",
                    "values": ["s", "m", "l", "xl"]
                  },
                  {
                    "id": "region",
                    "type": "select",
                    "values": ["sofia", "plovdiv", "svishtov", "burgas"]
                  },
                  {
                    "id": "promotion",
                    "type": "checkbox"
                  }
                 ],
          "total_available_products": 137
        }
Additional    : If either page or count is not passed - return all products. Sort_by defaults to price. If Category_id is not given or it equals "All", return results for all categories. Filters_required defaults to true. Text_search defaults to empty string.
Total_available_products returns the total items that match the given filter values for all pages.
Filter id and values are translation keys.
If text_search is an empty string - do not filter by text.
Admin users get one additional property for each category and sub-category - "enabled" of type boolean. Disabled categories will not be shown to the normal users - this is usefull when doing maintenance and adding new items.



Name          : Product Details
route         : /products/:product_id
HTTP types    : GET
Response Type : 200, 404
Response Data : {
          "price": 1500,
          "images": [{"id": "1", "path": "images/products/1.png", "title": "First Image"},
                {"id": "2", "path": "images/products/2.png", "title": "Second Image"},
                {"id": "3", "path": "images/products/3.png", "title": "Third Image"}],
          "tech_parameters": [{"id": "cpu", "data": "3.2"}, {"id": "ram", "data": "8"}],
          "filters": [
                  {
                    "id": "size",
                    "type": "select",
                    "values": [
                            {
                              "id": "s",
                              "added_price": 0
                            },
                            {
                              "id": "m",
                              "added_price": 1
                            },
                            {
                              "id": "l",
                              "added_price": 2
                            },
                            {
                              "id": "xl",
                              "added_price": 3.5
                            }
                          ],
                    "default_value": "s"
                  }
                ]
        }
Additional  : Filters id and values id are used as translation keys. Filters added_price will be added to the initial price. Possible to give negative values as discounts.
Admin users get one additional property for each category and sub-category - "enabled" of type boolean. Disabled categories will not be shown to the normal users - this is usefull when doing maintenance and adding new items.



Name          : Product Details Images
route         : /products/:product_id/images
HTTP types    : GET
Response Type : 200, 404
Response Data : {
          "images": [{"id": "1", "path": "images/products/1.png", "title": "First Image"},
                {"id": "2", "path": "images/products/2.png", "title": "Second Image"},
                {"id": "3", "path": "images/products/3.png", "title": "Third Image"}],
        }
Additional  : Can be used only to get the images related to the product. Useful for performance benefits, instead of requesting the complete product data.



Name          : Place Order
route         : /order
HTTP types    : POST
Response Type : 204, 400
Request Data  : {
          "products": [
                  {
                      "id": "1",
                      "quantity": 1,
                      "filters": {
                              "size": "s",
                              "region": "sofia"
                             }
                    }
                ],
          "first_name": "Admin",
            "last_name": "Adminov",
            "email": "admin@email.com",
            "phone": "+359 123 456 789",
            "address": {
              "street": "Mladost 2, bl 257A",
              "city": "Sofia",
              "postcode": "1000"
            },
            "additional_info": "Please make the delivery on thursday!",
            "invoice_required": true,
            "invoice": {
              "company_name": "My Company",
              "company_number": "987654321",
              "company_bulstat": "123QWERTY321",
              "company_address": "Mladost 4, bl. 403",
              "company_city": "Sofia",
              "company_dds": "12345"
            },
            "payment_type": "to_courier"
          }
Additional    : 



Name          : Login
route         : /login
HTTP types    : POST
Response Type : 200, 400
Request Data  : {} //Empty request with a session cookie is send - if it is valid, server returns login true + user information
Response Data : {
          "login"           : true,
          "captcha_correct" : false,
          "captcha_required": false,
          "user_type"       : "admin",
          "user_data"       : {
              'id'        : '1',
              'first_name': 'Admin',
              'last_name' : 'Adminov',
              'email'     : 'admin@email.com',
              'phone'     : '+359 123 456 789',
              'address'   : {
                'street'  : 'Mladost 2, bl 257A',
                'city'    : 'Sofia',
                'postcode': '1000'
              },
              'invoice_required': true,
              'additional_info' : 'Please make the delivery on thursday!',
              'invoice'         : {
                'company_name'   : 'My Company',
                'company_number' : '987654321',
                'company_bulstat': '123QWERTY321',
                'company_address': 'Mladost 4, bl. 403',
                'company_city'   : 'Sofia',
                'company_dds'    : '12345'
              }
        }

        //Second example - no session cookie is send
        Request Data :
        {
                    "email"           : "admin.adminov@email.com",
                    "password"        : "1234",
                    "captchaResponse" : "firstword secondword",
                    "captchaChallenge": "1232412rwredf34refca43rgtfeq35ref"
                }
        Response Data :
        {
            "login"           : false,
            "captcha_required": true
        }
Additional    : Captcha is not mandatory to be send in the request if server has not requested it.
If captcha is required, captcha_correct should also be returned.
If login true and no session cookie has been send, server should generate and record a valid session cookie and set it on the client, as a httpOnly cookie.
On initial load, the UI will make an empty request to /login. If a session cookie exists, it will be send to the server.
If the session cookie exists and it is still valid, the server will respond with login true and the user info, otherwise it will return login false.
Possible user roles are - guest (default one, when no active session), user (normal. logged user), admin
Upon 3 unsuccessful login attempts, containing email or password (empty requests to /login should not be counted), coming from the same IP address and in the timeframe of 5 minutes,
reCAPTCHA should be required for this user.
reCAPTCHA keys and php tutorial:
reCAPTCHA public key  - 6LeE4O4SAAAAAJA32ejfNwDIF9_SqlpsfhETcwYl
reCAPTCHA private key - 6LeE4O4SAAAAAHJ62NTvY3Lu46cz7AYc5wYU4v7D
php tutorial - https://developers.google.com/recaptcha/docs/php?csw=1



Name          : Logout
route         : /login
HTTP types    : DELETE
Response Type : 204, 400, 404
Additional    : No response data here. UI will send the session cookie, which should be enough.
Server should remove the session validity from the DB and also delete the session cookie on the UI side.



Name          : Register User
route         : /users/:user_id
HTTP types    : POST
Response Type : 204, 400, 409
Request Data  : {
          "email"     : "admin.adminov@email.com",
          "first_name": "Admin",
          "last_name" : "Adminov",
          "phone"     : "+359 123 456 789",
          "address"   : {
            "street"  : "Mladost 2, bl 257A",
            "city"    : "Sofia",
            "postcode": "1000"
          },
          "additional_info" : "Please make the delivery on thursday!",
          "invoice_required": true,
          "invoice"         : {
            "company_name"   : "My Company",
            "company_number" : "987654321",
            "company_bulstat": "123QWERTY321",
            "company_address": "Mladost 4, bl. 403",
            "company_city"   : "Sofia",
            "company_dds"    : "12345"
          },
          "subscribe_for_emails": true
        }
Response Data : {}
Additional    : Email, password, first_name, last_name, phone, address street and address city are mandatory.
If invoice_required, all invoice fields are required as well.
If everything checks out, server should send an email with a special security token and save it in the DB, connected to a specific email and valid for 24 hours.
It will then be used by the user, to set a password.



Name          : Set New Password
route         : /users/:user_id/set_new_password
HTTP types    : POST
Response Type : 204, 400, 404
Request Data  : {
          "email": "admin.adminov@email.com",
          "password": "123456",
          "resetToken": "1234QWERTY"
        }
Additional    : resetToken is always required here. Server should match it for the one saved in the DB for this email.
When a new password request is made, an email should be sent to the user, containing a link with the following pattern:
"https://somedomain.com/#/password-reset?reset-token=1234QWERTY"
Make sure it has only numbers, letters, and dashes.



Name          : Request New Password
route         : /users/:user_id/request_new_password
HTTP types    : PUT
Response Type : 204, 400, 404
Request Data  : {
          "email": "admin.adminov@email.com",
          "captchaResponse": "firstword secondword",
          "captchaChallenge": "1232412rwredf34refca43rgtfeq35ref"
        }
Additional    : Captcha is always required here.



Name          : Translations
route         : /translations
HTTP types    : GET
Response Type : 200
Response Data : {
          "langs": {
            "bg": {"multiplier": 1},
            "en": {"multiplier": 0.51129}
          },
          "default": "bg"
        }
Additional    : Multiplier is used to determine currency exchange rates. The default language is expected to have a multiplier of 1.



Name          : Translation
route         : /translations/en
HTTP types    : GET
Response Type : 200, 404
Response Data : {
          "general": {
            "key": "value",
            "foo": "bar"
          },
          "products": {
            "dell-latitude-5130": {
              "name": "Laptop Dell Latitude 5130",
              "short_desc": "A very nice laptop",
              "long_desc": "A very nice laptop, good for business, games, multimedia and home entertainment." 
            },
            "product-key": {
              "name": "Product name",
              "short_desc": "A very short product description",
              "long_desc": "A very long product description" 
            }
          }
        }
Additional    : Checkout app/scripts/mocks/translation.js for a very detailed example.




NORMAL LOGGED USER ACCESS:
Accessible only to users with user_type user or higher.


Name          : User Details
route         : /users/:user_id
HTTP types    : GET, PUT
Response Type : 200, 204, 400, 404
Request Data  : {
          "first_name": "Admin",
            "last_name": "Adminov",
            "phone": "+359 123 456 789",
            "address": {
              "street": "Mladost 2, bl 257A",
              "city": "Sofia",
              "postcode": "1000"
            },
            "additional_info": "Please make the delivery on thursday!",
            "invoice_required": true,
            "invoice": {
              "company_name": "My Company",
            "company_number": "987654321",
              "company_bulstat": "123QWERTY321",
              "company_address": "Mladost 4, bl. 403",
              "company_city": "Sofia",
              "company_dds": "12345"
            },
            "subscribeForEmails": true
        }
Response Data : {
          "id": "admin@email.com",
          "first_name": "Admin",
            "last_name": "Adminov",
            "email": "admin@email.com",
            "phone": "+359 123 456 789",
            "address": {
              "street": "Mladost 2, bl 257A",
              "city": "Sofia",
              "postcode": "1000"
            },
            "additional_info": "Please make the delivery on thursday!",
            "invoice_required": true,
            "invoice": {
              "company_name": "My Company",
            "company_number": "987654321",
              "company_bulstat": "123QWERTY321",
              "company_address": "Mladost 4, bl. 403",
              "company_city": "Sofia",
              "company_dds": "12345"
            },
            "type": "admin",
            "subscribeForEmails": true
        }
Additional    : 
TODO: When regular users with role "user" GET an user details, they should not be able to see the full list of details of other users



Name          : User Orders
route         : /users/:user_id/orders
HTTP types    : GET
Response Type : 200, 400, 404
Response Data : {
          "orders": [
                {
                  "id": "1356",
                  "status": "completed",
                  "start_date": "Wed, 29 Jan 2014 22:03:55",
                  "end_date": "Wed, 31 Jan 2014 12:24:16",
                  "total_price": 40,
                  "delivery_price": 5
                },
                {
                  "id": "1378",
                  "status": "new",
                  "start_date": "Wed, 03 Feb 2014 19:27:20",
                  "total_price": 80,
                  "delivery_price": 5
                }
              ]
        }
Additional    :



Name          : User Orders Details
route         : /users/:user_id/orders/:order_id
HTTP types    : GET
Response Type : 200, 400, 404
Response Data : {
          "id": "1356",
          "status": "completed",
          "start_date": "Wed, 29 Jan 2014 22:03:55",
          "end_date": "Wed, 31 Jan 2014 12:24:16",
          "total_price": 40,
          "delivery_price": 5,
          "products": [
                  {
                      "id": "1",
                      "price": 40,
                      "quantity": 2,
                      "thumbnail": "images/products/1.jpg",
                      "filters": {
                              "size": "s",
                              "region": "sofia"
                             }
                    }
                ],
          
          "first_name": "Admin",
            "last_name": "Adminov",
            "email": "admin@email.com",
            "phone": "+359 123 456 789",
            "address": {
              "street": "Mladost 2, bl 257A",
              "city": "Sofia",
              "postcode": "1000"
            },
          "additional_info": "Please make the delivery on thursday!",
          "invoice_required": true,
          "invoice": {
                    "company_name": "My Company",
                  "company_number": "987654321",
                    "company_bulstat": "123QWERTY321",
                    "company_address": "Mladost 4, bl. 403",
                    "company_city": "Sofia",
                    "company_dds": "12345"
                   },
          "payment_type": "to_courier"
        }
Additional    :



Name          : Cancel Order
route         : /users/:user_id/orders/:order_id
HTTP types    : DELETE
Response Type : 204, 404, 400
Additional    : User should be able to cancel only orders that are in status "new".




ADMIN USER ACCESS:
Accessible only to users with user_type admin.


Name          : Category
route         : /categories/:category_id/:subcategory_id
HTTP types    : POST, PUT, DELETE
Response Type : 201, 204, 400, 404, 409
Request Data  : 
        // POST /categories/electronics
        {
            "name": {"bg": "Elektronika", "en": "Electronics"},
            "enabled": false
          }

          //PUT /categories/electronics/notebooks
        {
            "name": {"bg": "Prenosimi kompiutri"},
            "enabled": true
          }               
Additional: For update PUT requests, if one of the properties is not passed, then it should not be changed.
            For POST requests "enabled" should default to false.



Name          : Product Details
route         : /products/:product_id
HTTP types    : POST, PUT, DELETE
Response Type : 201, 204, 400, 404, 409
Request Data  : {
          "price": 1500,
          "tech_parameters": [{"id": "cpu", "value": "3.2"}, {"id": "ram", "value": "8"}],
          "filters": [
                  {
                    "id": "size",
                    "values": [
                            {
                              "id": "s",
                              "added_price": 0
                            },
                            {
                              "id": "m",
                              "added_price": 1
                            },
                            {
                              "id": "l",
                              "added_price": 2
                            },
                            {
                              "id": "xl",
                              "added_price": 3.5
                            }
                          ],
                    "default_value": "s"
                  }
                ],
          "enabled": false
        }
Additional    : "tech_parameters" parameter is optional.
                All the rest ("price", "enabled", and "filters") are mandatory.



Name          : Product Add Image
route         : /products/:product_id/images
HTTP types    : POST
Response Type : 201, 400, 404
Request Data  : {<image_binary_data>}
Additional    : 



Name          : Product Delete Image
route         : /products/:product_id/images/:image_id
HTTP types    : DELETE
Response Type : 204, 400, 404
Additional    : 



Name          : Product Reorder Images
route         : /products/:product_id/images
HTTP types    : PUT
Response Type : 204, 400
Request Data  : {
          'id1': 0,
          'id3': 1,
          'id2': '2'
        }
Additional    : 



Name          : User Orders
route         : /orders/:order_id
HTTP types    : PUT
Response Type : 204, 400, 404
Request Data  : {
          "status": "new",
          "end_date": "Wed, 29 Jan 2014 22:03:55",
          "user_id": "ivan.ivanov@email.com",
          "products": [
                  {
                      "id": "1",
                      "price": 40,
                      "thumbnail": "images/products/1.jpg",
                      "filters": {
                              "size": "s",
                              "region": "sofia"
                             }
                  }
                ],
          "total_price": 40,
          "delivery_price": 5,
          "first_name": "Ivan",
            "last_name": "Ivanov",
            "email": "ivan.ivanov@email.com",
            "phone": "+359 123 456 789",
            "address": {
              "street": "Mladost 2, bl 257A",
              "city": "Sofia",
              "postcode": "1000"
            },
          "additional_info": "Please make the delivery on thursday!",
          "invoice_required": true,
          "invoice": {
                    "company_name": "My Company",
                    "company_number": "987654321",
                    "company_bulstat": "123QWERTY321",
                    "company_address": "Mladost 4, bl. 403",
                    "company_city": "Sofia",
                    "company_dds": "12345"
                   },
          "payment_type": "to_courier"
        }
Additional    :



Name          : All Site Orders
route         : /orders
HTTP types    : POST
Response Type : 200, 400
Request Data  : {
          "status": "new"
        }
Response Data : {
          "data": [
                {
                  "id": "1356",
                  "user_id": "ivan.ivanov@email.com", //If order was not made from a registered user, return 'guest'
                  "status": "new",
                  "start_date": "Wed, 29 Jan 2014 22:03:55",
                  "products": [
                          {
                            "id": "1",
                            "price": 40,
                            "thumbnail": "images/products/1.jpg",
                            "filters": {
                                    "size": "s",
                                    "region": "sofia"
                                   }
                          }
                        ],
                  "total_price": 40,
                  "delivery_price": 5,
                  "first_name": "Ivan",
                    "last_name": "Ivanov",
                    "email": "ivan.ivanov@email.com",
                    "phone": "+359 123 456 789",
                    "address": {
                      "street": "Mladost 2, bl 257A",
                      "city": "Sofia",
                      "postcode": "1000"
                    },
                  "additional_info": "Please make the delivery on thursday!",
                  "invoice_required": true,
                  "invoice": {
                              "company_name": "My Company",
                              "company_number": "987654321",
                              "company_bulstat": "123QWERTY321",
                              "company_address": "Mladost 4, bl. 403",
                              "company_city": "Sofia",
                              "company_dds": "12345"
                             },
                  "payment_type": "to_courier"
                },
                {
                  "id": "1378",
                  "status": "new",
                  "start_date": "Wed, 03 Feb 2014 19:27:20",
                  "products": [...],
                  "total_price": 80,
                  "delivery_price": 5,
                  "first_name": "Admin",
                    "last_name": "Adminov",
                    "email": "admin@email.com",
                    "phone": "+359 123 456 789",
                    "address": {...},
                    "additional_info": "...",
                    "invoice_required": false,
                    "payment_type": "to_courier"
                  }
              ]
        }
Additional    : Request data can be used to filter the response. If the order is made by a logged in user, "user_id" will be returned.



Name          : Filters
route         : /filters
HTTP types    : GET
Response Type : 200
Response Data : {
          "data": [
                {
                  "id": "price",
                  "type": "double_slider",
                  "values": {"min": 0, "max": 640},
                  "default_values": {"min": 0, "max": 640}
                },
                {
                  "id": "size",
                  "type": "multiple_choice",
                  "values": ["s", "m", "l", "xl"],
                  "default_values": "s"
                },
                {
                  "id": "region",
                  "type": "select",
                  "values": ["sofia", "plovdiv", "svishtov", "burgas"],
                  "default_values": "sofia"
                },
                {
                  "id": "promotion",
                  "type": "checkbox",
                  "default_values": false
                }
             ]
        }
Additional    :



Name          : Filters
route         : /filters/:filter_id
HTTP types    : PUT, POST, DELETE
Response Type : 201, 204, 400, 404, 409
Request Data  : {
          "name": {"en": "Size", "bg": "Размер"},
          "type": "double_slider",
          "values": {"min": 0, "max": 640},
          "default_values": {"min": 0, "max": 640}
        }
Additional    :



Name          : Tech Parameters
route         : /tech_parameters
HTTP types    : GET
Response Type : 200
Response Data : {
          "data": [
                {
                  "id": "cpu",
                  "name": {"en": "CPU", "bg": "Процесор"},
                  "units": "GHz"
                },
                {
                  "id": "length",
                  "name": {"en": "Length", "bg": "Дължина"},
                  "units": "cm"
                }
              ]
        }
Additional    :



Name          : Tech Parameters
route         : /tech_parameters/:tech_param_id
HTTP types    : PUT, POST, DELETE
Response Type : 201, 204, 400, 404, 409
Request Data  : {
          "name": {"en": "CPU", "bg": "Procesor"},
          "units": "GHz"
        }
Additional    :



Name          : Translations
route         : /translations/en
HTTP types    : PUT
Response Type : 200, 404
Request Data  : {
          "products": {
            "product_id": {
              "name": "Notebook Dell, series Latitude 6380",
              "short-desc": "Bestseller!",
              "long-desc": "Good for business, home media or gaming!"
            }
          }
        }
Additional    :
