<?php

/*Leaves a 'lg()' function, suitable for all types of debug*/
require('../debug.php');


/*Used as a source for all API calls to the Store REST server*/
function restCall($method, $uri, $data, $cookie_session_id) {

  /*List of all possible response HTTP codes*/
  $status_codes = array(
    '0'   => 'Host Not Found',

    '100' => 'Continue',
    '101' => 'Switching Protocols',

    '200' => 'OK',
    '201' => 'Created',
    '202' => 'Accepted',
    '203' => 'Non-Authoritative Information',
    '204' => 'No Content',
    '205' => 'Reset Content',
    '206' => 'Partial Content',

    '300' => 'Multiple Choices',
    '301' => 'Moved Permanently',
    '302' => 'Found',
    '303' => 'See Other',
    '304' => 'Not Modified',
    '305' => 'Use Proxy',
    '307' => 'Temporary Redirect',

    '400' => 'Bad Request',
    '401' => 'Unauthorized',
    '402' => 'Payment Required',
    '403' => 'Forbidden',
    '404' => 'Not Found',
    '405' => 'Method Not Allowed',
    '406' => 'Not Acceptable',
    '407' => 'Proxy Authentication Required',
    '408' => 'Request Time-out',
    '409' => 'Conflict',
    '410' => 'Gone',
    '411' => 'Length Required',
    '412' => 'Precondition Failed',
    '413' => 'Request Entity Too Large',
    '414' => 'Request-URI Too Large',
    '415' => 'Unsupported Media Type',
    '416' => 'Requested range not satisfiable',
    '417' => 'Expectation Failed',

    '500' => 'Internal Server Error',
    '501' => 'Not Implemented',
    '502' => 'Bad Gateway',
    '503' => 'Service Unavailable',
    '504' => 'Gateway Time-out',
    '505' => 'HTTP Version not supported'
  );


  $curl = curl_init();

  /*Compose query*/
  $service_url = 'http://idimanov.com/sedmataposoka/rest_api/';
  $method      = strtoupper($method);
  $options     = array(
    CURLOPT_URL            => $service_url.$uri,
    CURLOPT_CUSTOMREQUEST  => $method,
    CURLOPT_POSTFIELDS     => http_build_query( $data ),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 2,
    CURLOPT_HEADER         => true,
    CURLOPT_HTTPHEADER     => array(
      'Cookie: '.$cookie_session_id
    )
  );

  curl_setopt_array($curl, $options);

  /*Execute & collect result*/
  $response  = curl_exec($curl);
  $info      = curl_getinfo($curl);

  /*Print the REST result action without mentioning the '$response' variable name in the output*/
  echo $info['http_code'].' - '.$status_codes[ $info['http_code'] ];
  lg( $response, '', false);
}


/*
  $data = array(
    'email'    => 'admin@email.com',
    'password' => '1234'
  );
  $cookie_session_id = '';
  restCall('POST', '/login', $data, $cookie_session_id);
/**/

/*
  $data = array(
    'email'      => 'mail@idimanov.com',
    'password'   => '14*15',
    'first_name' => 'Ivan',
    'last_name'  => 'Dimanov',
    'phone'      => '+359 887 489 420',
    'address'    => array(
      'city'   => 'Sofia',
      'street' => 'Mladost 4, bl. 418'
    ),
    'invoice_required' => true,
    'invoice'          => array(
      'company_name'    => 'IdeaSoft',
      'company_number'  => '10001',
      'company_bulstat' => '10001',
      'company_city'    => 'Svishtov',
      'company_address' => 'Gradevo 10a',
      'company_dds'     => '12'
    )
  );
  $cookie_session_id = 'PHPSESSID=opg789egjigeoqnlep50pnnsb7';
  restCall('POST', '/users/102', $data, $cookie_session_id);
/**/

/*
  $data = array(
    'email'    => 'mail@idimanov.com',
    'password' => 'ZZZZ'
  );
  $cookie_session_id = '';
  restCall('POST', '/login', $data, $cookie_session_id);
/**/

/*
  $data = array(
    'email'            => 'mail@idimanov.com',
    'captchaResponse'  => '1111',
    'captchaChallenge' => '1111'
  );
  $cookie_session_id = '';
  restCall('POST', '/users/102/request_new_password', $data, $cookie_session_id);
/**/

/*
  $data = array(
    'email'      => 'mail@idimanov.com',
    'password'   => 'YYYY',
    'resetToken' => 'cZ3G9I4ueHr6-1zCfqd2'
  );
  $cookie_session_id = '';
  restCall('POST', '/users/102/set_new_password', $data, $cookie_session_id);
/**/

/*
  $data = array();
  $cookie_session_id = '';
  restCall('GET', '/translations', $data, $cookie_session_id);
  // restCall('GET', '/translations/en', $data, $cookie_session_id);
  // restCall('GET', '/translations/bg', $data, $cookie_session_id);
/**/

/*
  $data = array();
  $cookie_session_id = '';
  restCall('GET', '/categories', $data, $cookie_session_id);
/**/

/*
  $data = array(
    'name' => array(
      'bg' => 'ЦЦЦ',
      'en' => 'TsTsTs'
    ),
    'enabled' => false
  );
  $cookie_session_id = 'PHPSESSID=o6bbi26535uu3b3plracoh41i6';
  restCall('DELETE', '/categories/ccc', $data, $cookie_session_id);

  // restCall('POST', '/categories/furniture/desks' , $data, $cookie_session_id);
  // restCall('POST', '/categories/furniture/chairs', $data, $cookie_session_id);
  // restCall('POST', '/categories/furniture/tables', $data, $cookie_session_id);
/**/

/*
  $data = array(
    'name' => array(
      'en' => 'volume',
      'bg' => 'обем'
    ),
    'type'           => 'double_slider',
    'values'         => array('min' => 50, 'max' => 1500),
    'default_values' => array('min' => 50, 'max' => 1000)
  );
  $cookie_session_id = 'PHPSESSID=o6bbi26535uu3b3plracoh41i6';
  restCall('POST', '/filters/volume', $data, $cookie_session_id);
/**/

/*
  $data = array(
    'name' => array(
      'en' => 'weight - updated',
      'bg' => 'тежест - обновена'
    ),
    'type'           => 'select',
    'values'         => array('light', 'medium', 'heavy'),
    'default_values' => 'medium'
  );
  $cookie_session_id = 'PHPSESSID=o6bbi26535uu3b3plracoh41i6';
  restCall('PUT', '/filters/weight', $data, $cookie_session_id);
/**/

/*
  $data = array();
  $cookie_session_id = 'PHPSESSID=o6bbi26535uu3b3plracoh41i6';
  restCall('DELETE', '/filters/weight', $data, $cookie_session_id);
/**/

/*
  $data = array(
    'name' => array(
      'en' => 'Length',
      'bg' => 'Дължина'
    ),
    'units' => 'cm'
  );
  $cookie_session_id = 'PHPSESSID=o6bbi26535uu3b3plracoh41i6';
  restCall('POST', '/tech_parameters/length', $data, $cookie_session_id);
/**/

/*
  $data = array();
  $cookie_session_id = 'PHPSESSID=o6bbi26535uu3b3plracoh41i6';
  restCall('DELETE', '/tech_parameters/length', $data, $cookie_session_id);
/**/

/*
  $data = array(
    'products' => array(
      100 => array(
        'name'       => 'Notebook Dell, серия Latitude 6380',
        'short-desc' => 'Най-добра покупка!',
        'long-desc'  => 'Добър за работа, домашна употреба и игра!'
      )
    )
  );
  $cookie_session_id = 'PHPSESSID=o6bbi26535uu3b3plracoh41i6';
  restCall('PUT', '/translations/bg', $data, $cookie_session_id);
/**/

/*
  $data = array();
  $cookie_session_id = '';
  restCall('GET', '/marketing_ads', $data, $cookie_session_id);
/**/

/*
  $data = array(
    'category_name' => 'mens-clothes',
    'enabled'       => false,
    'price'         => 1500,

    'tech_parameters' => array(
      array('id' => 'cpu', 'value' => '3.2'),
      array('id' => 'ram', 'value' => '8')
    ),

    'filters' => array(
      array(
        'id'     => 'weight',
        'values' => array(
          array('id' => 50 , 'added_price' => 0),
          array('id' => 150, 'added_price' => 1)
        ),
        'default_value' => 50
      ),

      array(
        'id'     => 'gift',
        'values' => array(
          array('id' => 'true' , 'added_price' => 0),
          array('id' => 'false', 'added_price' => 1)
        ),
        'default_value' => 'false'
      ),

      array(
        'id'     => 'size',
        'values' => array(
          array('id' => 's' , 'added_price' => 0  ),
          array('id' => 'm' , 'added_price' => 1  ),
          array('id' => 'l' , 'added_price' => 2  ),
          array('id' => 'xl', 'added_price' => 3.5)
        ),
        'default_value' => 's'
      ),

      array(
        'id'     => 'region',
        'values' => array(
          array('id' => 'sofia'  , 'added_price' => 0),
          array('id' => 'plovdiv', 'added_price' => 1)
        ),
        'default_value' => 'plovdiv'
      )

    )
  );
  $cookie_session_id = 'PHPSESSID=o6bbi26535uu3b3plracoh41i6';
  restCall('POST', '/products/male-shirt-1', $data, $cookie_session_id);
/**/

/*
  $data = array(
    '_category_name' => 'mens-clothes',
    '_enabled'       => false,
    '_price'         => 1500,

    '_tech_parameters' => array(
      array('id' => 'cpu', 'value' => '3.2'),
      array('id' => 'ram', 'value' => '100')
    ),

    'filters' => array(
      array(
        'id'     => 'weight',
        'values' => array(
          array('id' => 50 , 'added_price' => 0),
          array('id' => 150, 'added_price' => 1)
        ),
        'default_value' => 50
      ),

      array(
        'id'     => 'gift',
        'values' => array(
          array('id' => 'true' , 'added_price' => 0),
          array('id' => 'false', 'added_price' => 1)
        ),
        'default_value' => 'false'
      ),

      array(
        'id'     => 'size',
        'values' => array(
          array('id' => 's' , 'added_price' => 0  ),
          array('id' => 'm' , 'added_price' => 1  ),
          array('id' => 'l' , 'added_price' => 2  ),
          array('id' => 'xl', 'added_price' => 3.5)
        ),
        'default_value' => 's'
      ),

      array(
        'id'     => 'region',
        'values' => array(
          array('id' => 'sofia'  , 'added_price' => 0),
          array('id' => 'plovdiv', 'added_price' => 1)
        ),
        'default_value' => 'plovdiv'
      )

    )
  );
  $cookie_session_id = 'PHPSESSID=o6bbi26535uu3b3plracoh41i6';
  restCall('PUT', '/products/male-shirt-1', $data, $cookie_session_id);
/**/

/*
  $data = array();
  $cookie_session_id = 'PHPSESSID=o6bbi26535uu3b3plracoh41i6';
  restCall('DELETE', '/products/male-shirt-1', $data, $cookie_session_id);
/**/

/*
  $data = array();
  $cookie_session_id = '';
  restCall('GET', '/products', $data, $cookie_session_id);
/**/

/*
  $data = array(
    'page'             => 1,
    'count'            => 3,
    'sort_by'          => 'price',
    'category_id'      => 'mens-clothes',
    'filters_required' => true,
    'text_search'      => 'shirt',
    'lang'             => 'en',
    'filters'          => array(
      'weight' => array('min' => 100, 'max' => 500),
      'size'   => array('l', 'xl')
    )
  );
  $cookie_session_id = '';
  restCall('POST', '/products', $data, $cookie_session_id);
/**/