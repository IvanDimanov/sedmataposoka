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
 - 200 - ok                    - must be accompanied by server response data
 - 201 - created successfully  - no returned data
 - 204 - ok                    - no returned data
 - 400 - request data error    - must be accompanied by meaningful description of the error
 - 401 - not authenticated     - this should be returned when user tries to access some private data, but does not have an active session on the server
 - 403 - forbidden             - cannot call this method duo specific reasons different from "authenticated" accessed
 - 404 - resource not found    - no returned data
 - 409 - duplicate record      - returned for POST and PUT request
 - 500 - internal server error - error in the back-end core function

PUT requests may not contain all data. If some resource data was existing before the PUT request and it is not mentioned in the request, it should not be updated by the server.
Example - send PUT request to /ads/:ad_id with data {startDate: "2018-12-10"}, should update only the ad startDate property and leave all remaining fields untouched.
By default, PUT requests should return the resulting object.

CSRF Prevention:
With each PUT, POST or DELETE request, the UI will send a XSRF-TOKEN cookie and a X-XSRF-TOKEN header, which must be identical.
If they are missing or mismatched, return error 401 and log the event on the server side, because this was a possible hacker attack.


SUPER ADMIN USER ACCESS:
Accessible only to users with admin type "super_admin".


Name        : Login as Admin
Route       : /login
Request type: POST
Request data: {
  "name"    : "7Admin",
  "password": "6c730ae5d030587ee60254aa4d0eb4174f9e8b4fc4a8cf59e5388cba396c77af"
}

/*
  Please be advised that plain text passwords should not be transfered through the web.
  That's is why if client password is '7@posoka'
  then we should send sha3('7@posoka') = '6c730ae5d030587ee60254aa4d0eb4174f9e8b4fc4a8cf59e5388cba396c77af'
*/

Response type: 200
Response data: '{"id":"1","name":"7Admin","type":"super_admin","password":"6828d28372d2b369efefbbf75dcb58a49ba0df67","salt":"0b5745acf1e6c9","is_active":"1","access_token_value":null,"access_token_created_at":null,"createdAt":"2015-01-21 09:13:52","login":true}'

Response type: 400
Response data: '{"login":false}'
Response data: '{"login":false,"captcha_required":true}'
Response data: '{"login":false,"captcha_required":true,"captcha_correct":false}'


-----------------------------------------------------------------------


Name        : Get login details of an already logged user
Route       : /login
Request type: POST
Request data: ''

Response type: 200
Response data: '{"id":"1","name":"7Admin","type":"super_admin","password":"6828d28372d2b369efefbbf75dcb58a49ba0df67","salt":"0b5745acf1e6c9","is_active":"1","access_token_value":null,"access_token_created_at":null,"createdAt":"2015-01-21 09:13:52","login":true}'

Response type: 400
Response data: '{"login":false}'


-----------------------------------------------------------------------


Name        : Logout
Route       : /login
Request type: DELETE
Request data: ''

Response type: 204
Response data: ''

Response type: 500
Response data: '{"error":"Unable to destroy the user session completely"}'


-----------------------------------------------------------------------


Name        : Create Ad
Route       : /ads
Request type: POST
Request data: {
  "title": {
    "bg": "Тестови баннер",
    "en": "Test banner"
  },

  "type"     : 1
  "link"     : "http://test_add_link.com",

  "startDate": "2013-11-16 00:00:00",
  "endDate"  : "2013-11-17 00:00:00"
}

Response type: 201
Response data: {
  "title": {
    "bg": "Тестови баннер",
    "en": "Test banner"
  },

  "type"     : 1
  "imagePath": "",
  "link"     : "http://test_add_link.com",

  "startDate": "2013-11-16 00:00:00",
  "endDate"  : "2013-11-17 00:00:00"
}

Response type: 400
Response data: '{error:"Missing property 'type'"}'
Response data: '{error:"Invalid 'startDate' property"}'

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Get all Ads
Route       : /ads
Request type: GET
Request data: ''

Response type: 200
Response data: [{"id":1,"imagePath":"ads/add_1.jpg", ...},{"id":2,"imagePath":"ads/add_2.jpg", ...}, ...]

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Get specific Ad
Route       : /ads/:ad_id
Request type: GET
Request data: ''

Response type: 200
Response data: {"id":1,"imagePath":"ads/add_1.jpg", ...}

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Ad ID"}'


-----------------------------------------------------------------------


Name        : Get specific Ads
Route       : /ads
Request type: GET
Request data: {
  "ids"     : [1, 2],
  "types"   : [1],
  "fromDate": "2000-11-16 00:00:00",
  "toDate"  : "2013-11-16 00:00:00"
}

/*
  All of the searching rules are optional.
  All of them will be concatenated with logical AND.
  If no (valid) searching rules ware sent, this request will return all Ads
*/

Response type: 200
Response data: [{"id":1,"imagePath":"ads/add_1.jpg", ...},{"id":2,"imagePath":"ads/add_2.jpg", ...}, ...]

Response type: 400
Response data: '{error:"Invalid 'fromDate' property"}'

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Update specific Ad
Route       : /ads/:ad_id
Request type: PUT
Request data: {
  "endDate": "2015-11-16 00:00:00"
}

Response type: 200
Response data: {"id":1,"imagePath":"ads/add_1.jpg", ...}

Response type: 400
Response data: '{error:"Invalid 'fromDate' property"}'

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Ad ID"}'


-----------------------------------------------------------------------


Name        : Update Ad Image
Route       : /ads/:ad_id/image
Request type: POST
Request data: Image data

Response type: 200
Response data: {"id":1,"type":2,"imagePath":"ads/add_1.jpg", ...}

Response type: 400
Response data: '{error:"Invalid 'fromDate' property"}'
Response data: '{error:"Invalid 'fromDate' property"}'
Response data: '{error:"Invalid 'fromDate' property"}'
Response data: '{error:"Invalid 'fromDate' property"}'
Response data: '{error:"Invalid 'fromDate' property"}'
Response data: '{error:"Invalid 'fromDate' property"}'
Response data: '{error:"Invalid 'fromDate' property"}'

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Ad ID"}'


-----------------------------------------------------------------------


Name        : Delete specific Ad
Route       : /ads/:ad_id
Request type: DELETE
Request data: ''

Response type: 204
Response data: ''

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Ad ID"}'


-----------------------------------------------------------------------


Name        : Create Thought
Route       : /thoughts
Request type: POST
Request data: {
  "text": {
    "bg": "Неможем да разрешим проблемите използвайки същото мислене, когато сме ги създали.",
    "en": "We can't solve problems by using the same kind of thinking we used when we created them."
  },
  "author": {
    "bg": "Алберт Айнщаин",
    "en": "Albert Einstein"
  },
  "startDate": "2013-11-16 00:00:00",
  "endDate"  : "2013-11-17 00:00:00"
}

Response type: 201
Response data: {
  "text": {
    "bg": "Неможем да разрешим проблемите използвайки същото мислене, когато сме ги създали.",
    "en": "We can't solve problems by using the same kind of thinking we used when we created them."
  },
  "author": {
    "bg": "Алберт Айнщаин",
    "en": "Albert Einstein"
  },
  "startDate": "2013-11-16 00:00:00",
  "endDate"  : "2013-11-17 00:00:00"
}


Response type: 400
Response data: '{error:"Missing property 'text'->'en'"}'
Response data: '{error:"Invalid 'startDate' property"}'

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Get all Thoughts
Route       : /thoughts
Request type: GET
Request data: ''

Response type: 200
Response data: [{"id":1,"text":{"bg":"Неможем да разрешим ..."}, ...},{"id":2,"text":{"bg":"Неможем да разрешим ..."}, ...}, ...]

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Get specific Thought
Route       : /thoughts/:thought_id
Request type: GET
Request data: ''

Response type: 200
Response data: {"id":1,"text":{"bg":"Неможем да разрешим ..."}, ...}

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Thought ID"}'


-----------------------------------------------------------------------


Name        : Get specific Thoughts
Route       : /thoughts
Request type: GET
Request data: {
  "ids"     : [1, 2],
  "text": {
    "bg": "Неможем да разрешим",
  },
  "fromDate": "2000-11-16 00:00:00",
  "toDate"  : "2013-11-16 00:00:00"
}

Response type: 200
Response data: [{"id":1,"text":{"bg":"Неможем да разрешим ..."}, ...},{"id":2,"text":{"bg":"Неможем да разрешим ..."}, ...}, ...]

Response type: 400
Response data: '{error:"Invalid 'fromDate' property"}'

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Update specific Thought
Route       : /thoughts/:thought_id
Request type: PUT
Request data: {
  "text": {
    "bg": "Неможем да разрешим въобще",
  },
  "endDate": "2015-11-16 00:00:00"
}

Response type: 200
Response data: {"id":1,"text":{"bg":"Неможем да разрешим ..."}, ...}

Response type: 400
Response data: '{error:"Invalid 'endDate' property"}'

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Thought ID"}'


-----------------------------------------------------------------------


Name        : Delete specific Thought
Route       : /thoughts/:thought_id
Request type: DELETE
Request data: ''

Response type: 204
Response data: ''

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Thought ID"}'


-----------------------------------------------------------------------


Name        : Create Partner
Route       : /partners
Request type: POST
Request data: {
  "name": {
    "bg": "Тестови партньор",
    "en": "Test Partner"
  },
  "logoSrc": "partners/test_partner.png",
  "link"   : "http://test_partner.com"
}

Response type: 201
Response data: {
  "name": {
    "bg": "Тестови партньор",
    "en": "Test Partner"
  },
  "logoSrc": "partners/test_partner.png",
  "link"   : "http://test_partner.com"
}


Response type: 400
Response data: '{error:"Missing property 'name'->'en'"}'
Response data: '{error:"Invalid 'link' property"}'

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Get all Partners
Route       : /partners
Request type: GET
Request data: ''

Response type: 200
Response data: [{"id":1,"name":{"bg":"Тестови партньор 1"}, ...},{"id":2,"name":{"bg":"Тестови партньор 2"}, ...}, ...]

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Get specific Partner
Route       : /partners/:partner_id
Request type: GET
Request data: ''

Response type: 200
Response data: {"id":1,"name":{"bg":"Тестови партньор 1"}, ...}

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Partner ID"}'


-----------------------------------------------------------------------


Name        : Get specific Partners
Route       : /partners
Request type: GET
Request data: {
  "ids"     : [1, 2],
  "name": {
    "bg": "Тестови",
  }
}

Response type: 200
Response data: [{"id":1,"name":{"bg":"Тестови партньор 1"}, ...},{"id":2,"name":{"bg":"Тестови партньор 2"}, ...}, ...]

Response type: 400
Response data: '{error:"Invalid 'fromDate' property"}'

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Update specific Partner
Route       : /partners/:partner_id
Request type: PUT
Request data: {
  "name": {
    "bg": "Тестови",
  },
  "link": "http://test_partner_udpate.com"
}

Response type: 200
Response data: {"id":1,"name":{"bg":"Тестови"},"link":"http://test_partner_udpate.com", ...}

Response type: 400
Response data: '{error:"Invalid 'link' property"}'

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Partner ID"}'


-----------------------------------------------------------------------


Name        : Delete specific Partner
Route       : /partners/:partner_id
Request type: DELETE
Request data: ''

Response type: 204
Response data: ''

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Partner ID"}'


-----------------------------------------------------------------------


Name        : Create Category
Route       : /categories
Request type: POST
Request data: {
  "name": {
    "bg": "Книги",
    "en": "Books"
  },
  "descr": {
    "bg": "Четенето на книги е великолепен начин за достъпване на дълбините на вашето въображение.",
    "en": "Reading books is a great way to step deep into new endeavors of you imagination."
  },
  "pictureSrc": "category/books.png"
}

Response type: 201
Response data: {
  "name": {
    "bg": "Книги",
    "en": "Books"
  },
  "descr": {
    "bg": "Четенето на книги е великолепен начин за достъпване на дълбините на вашето въображение.",
    "en": "Reading books is a great way to step deep into new endeavors of you imagination."
  },
  "pictureSrc": "category/books.png"
}


Response type: 400
Response data: '{error:"Missing property 'name'->'en'"}'
Response data: '{error:"Invalid 'pictureSrc' property"}'

Response type: 401
Response data: ''

Response type: 409
Response data: ''


-----------------------------------------------------------------------


Name        : Get all Categories
Route       : /categories
Request type: GET
Request data: ''

Response type: 200
Response data: [{"id":1,"name":{"bg":"Книги"}, ...},{"id":2,"name":{"bg":"Йога"}, ...}, ...]

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Get specific Category
Route       : /categories/:category_id
Request type: GET
Request data: ''

Response type: 200
Response data: {"id":1,"name":{"bg":"Книги"}, ...}

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Category ID"}'


-----------------------------------------------------------------------


Name        : Get specific Categories
Route       : /categories
Request type: GET
Request data: {
  "ids"     : [1, 2],
  "name": {
    "bg": "Книги",
  }
}

Response type: 200
Response data: [{"id":1,"name":{"bg":"Книги романи"}, ...},{"id":2,"name":{"bg":"Книги приключения"}, ...}, ...]

Response type: 400
Response data: '{error:"Invalid 'name' property"}'

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Update specific Category
Route       : /categories/:category_id
Request type: PUT
Request data: {
  "name": {
    "bg": "Развлекателни книги",
  },
  "pictureSrc": "category/advertising_books.png"
}

Response type: 200
Response data: {"id":1,"name":{"bg":"Развлекателни книги","en":"..."},"pictureSrc":"category/advertising_books.png", ...}

Response type: 400
Response data: '{error:"Invalid 'pictureSrc' property"}'

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Category ID"}'


-----------------------------------------------------------------------


Name        : Delete specific Category
Route       : /categories/:category_id
Request type: DELETE
Request data: ''

Response type: 204
Response data: ''

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Category ID"}'


-----------------------------------------------------------------------


Name        : Create Subcategory
Route       : /subcategory
Request type: POST
Request data: {
  "catId": 3,

  "name": {
    "bg": "Хатха",
    "en": "Hatha"
  },
  "descr": {
    "bg": "Хáтха йога или още на български често наричана Хáта йога е вид Йога, клон на Раджа йога, създадена през XV век от мъдрецът Йоги Сватмарама и описана от него в съчинението Хатха Йога Прадипика (прадипика означава буквално "това, което хвърля светлина.",
    "en": "Hatha yoga, also called hatha vidya, is a kind of yoga focusing on physical and mental strength building exercises and postures described primarily in three texts of Hinduism."
  },
  "pictureSrc": "subcategory/yoga_hatha.png"
}

Response type: 201
Response data: {
  "catId": 3,

  "name": {
    "bg": "Хатха",
    "en": "Hatha"
  },
  "descr": {
    "bg": "Хáтха йога или още на български често наричана Хáта йога е вид Йога, клон на Раджа йога, създадена през XV век от мъдрецът Йоги Сватмарама и описана от него в съчинението Хатха Йога Прадипика (прадипика означава буквално "това, което хвърля светлина.",
    "en": "Hatha yoga, also called hatha vidya, is a kind of yoga focusing on physical and mental strength building exercises and postures described primarily in three texts of Hinduism."
  },
  "pictureSrc": "subcategory/yoga_hatha.png"
}


Response type: 400
Response data: '{error:"Missing property 'name'->'en'"}'
Response data: '{error:"Invalid 'pictureSrc' property"}'

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Category with ID 'catId' was not found"}'

Response type: 409
Response data: ''


-----------------------------------------------------------------------


Name        : Get all Subcategories
Route       : /subcategory
Request type: GET
Request data: ''

Response type: 200
Response data: [{"id":1,"catId":3,"name":{"bg":"Хатха"}, ...},{"id":2,"catId":3,"name":{"bg":"Виняса"}, ...}, ...]

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Get specific Subcategory
Route       : /subcategory/:subcategory_id
Request type: GET
Request data: ''

Response type: 200
Response data: {"id":1,"name":{"bg":"Книги"}, ...}

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Subcategory ID"}'


-----------------------------------------------------------------------


Name        : Get specific Subcategories
Route       : /subcategory
Request type: GET
Request data: {
  "ids"     : [1, 2],
  "catIds"  : [3, 4]
  "name": {
    "bg": "Книги",
  }
}

Response type: 200
Response data: [{"id":1,"catId":3,"name":{"bg":"Хатха"}, ...},{"id":2,"catId":3,"name":{"bg":"Виняса"}, ...}, ...]

Response type: 400
Response data: '{error:"Invalid 'name' property"}'

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Update specific Subcategory
Route       : /subcategory/:subcategory_id
Request type: PUT
Request data: {
  "name": {
    "bg": "Хатха - 2",
  },
  "pictureSrc": "subcategory/yoga_hatha_2.png"
}

Response type: 200
Response data: {"id":1,"catId":3,"name":{"bg":"Хатха - 2","en":"..."},"pictureSrc":"subcategory/yoga_hatha_2.png", ...}

Response type: 400
Response data: '{error:"Invalid 'pictureSrc' property"}'

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Subcategory ID"}'


-----------------------------------------------------------------------


Name        : Delete specific Subcategory
Route       : /subcategory/:subcategory_id
Request type: DELETE
Request data: ''

Response type: 204
Response data: ''

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Subcategory ID"}'


-----------------------------------------------------------------------


Name        : Create Event
Route       : /events
Request type: POST
Request data: {
  "subcatId": 3,

  "title": {
    "bg": "В мир с учителя Гупта",
    "en": "In peace with master Gupta"
  },
  "descr": {
    "bg": "Тестово описание на събитието: В мир с учителя  ГуптаLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
    "en": "Test event description for event name: In peace with master Gupta. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."
  },

  "link"     : "http://test_event.com",
  "fee"      : "20 BGN/лв.",
  "startDate": "2015-01-19 13:00:00",
  "endDate"  : "2015-01-19 15:00:00"
}

Response type: 201
Response data: {
  "subcatId": 3,

  "title": {
    "bg": "В мир с учителя Гупта",
    "en": "In peace with master Gupta"
  },
  "descr": {
    "bg": "Тестово описание на събитието: В мир с учителя  ГуптаLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
    "en": "Test event description for event name: In peace with master Gupta. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."
  },

  "link"     : "http://test_event.com",
  "fee"      : "20 BGN/лв.",
  "startDate": "2015-01-19 13:00:00",
  "endDate"  : "2015-01-19 15:00:00"
}


Response type: 400
Response data: '{error:"Missing property 'title'->'en'"}'
Response data: '{error:"Invalid 'link' property"}'

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Subategory with ID 'subcatId' was not found"}'

Response type: 409
Response data: ''


-----------------------------------------------------------------------


Name        : Get all Events
Route       : /events
Request type: GET
Request data: ''

Response type: 200
Response data: [{"id":1,"subcatId":3,"title":{"bg":"В мир с учителя Гупта"}, ...},{"id":2,"subcatId":3,"title":{"bg":"Основен клас"}, ...}, ...]

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Get specific Event
Route       : /events/:event_id
Request type: GET
Request data: ''

Response type: 200
Response data: {"id":1,"subcatId":3,"title":{"bg":"В мир с учителя Гупта"}, ...}

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Event ID"}'


-----------------------------------------------------------------------


Name        : Get specific Events
Route       : /events
Request type: GET
Request data: {
  "ids"      : [1, 2],
  "subcatIds": [3, 4]
  "title"    : {
    "bg": "Гупта",
  }
}

Response type: 200
Response data: [{"id":1,"subcatId":3,"title":{"bg":"В мир с учителя Гупта"}, ...},{"id":2,"subcatId":3,"title":{"bg":"Основен Гупта клас"}, ...}, ...]

Response type: 400
Response data: '{error:"Invalid 'title' property"}'

Response type: 401
Response data: ''


-----------------------------------------------------------------------


Name        : Update specific Event
Route       : /events/:event_id
Request type: PUT
Request data: {
  "title": {
    "bg": "В мир с учителя Комар",
  },
  "fee": "25 BGN/лв."
}

Response type: 200
Response data: {"id":1,"subcatId":3,"title":{"bg":"В мир с учителя Комар"},"fee":"25 BGN/лв.", ...}

Response type: 400
Response data: '{error:"Invalid 'fee' property"}'

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Event ID"}'


-----------------------------------------------------------------------


Name        : Delete specific Event
Route       : /events/:event_id
Request type: DELETE
Request data: ''

Response type: 204
Response data: ''

Response type: 401
Response data: ''

Response type: 404
Response data: '{error:"Unknown Event ID"}'