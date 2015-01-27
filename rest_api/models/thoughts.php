<?php
/*This file holds all DB functions which effects Thoughts*/


/*Used to convert raw DB result to expected JSON-like result*/
function reorderThoughtRecord(&$thought) {
  if (gettype($thought) !== 'array' ||
      !sizeof($thought)
  ) {
    return $thought;
  }


  /*Convert author structure 'author_*' to array*/
  $thought['author'] = array(
    'bg' => $thought['author_bg'],
    'en' => $thought['author_en']
  );

  unset( $thought['author_bg'] );
  unset( $thought['author_en'] );


  /*Convert text structure 'text_*' to array*/
  $thought['text'] = array(
    'bg' => $thought['text_bg'],
    'en' => $thought['text_en']
  );

  unset( $thought['text_bg'] );
  unset( $thought['text_en'] );

  return $thought;
}



/*Return all the info we have for Thought with ID '$id' or return 'false'*/
function getThoughtByID($id) {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $query = $db->prepare('SELECT
                          thought.id        as id,
                          thought.startDate as startDate,
                          thought.endDate   as endDate,

                          thought_author.id as author_id,
                          thought_author.bg as author_bg,
                          thought_author.en as author_en,

                          thought_text.id   as text_id,
                          thought_text.bg   as text_bg,
                          thought_text.en   as text_en

                        FROM thought

                        JOIN thought_author
                        ON thought.authorId = thought_author.id

                        JOIN thought_text
                        ON thought.textId = thought_text.id

                        WHERE thought.id = :id
                        LIMIT 1');
  $result = $query->execute(array(
    'id' => $id
  ));

  /*Prevent further calculation if the query fail to load*/
  if (!$result) {
    return;
  }


  /*Set DB values in ordered JSON-like list*/
  return reorderThoughtRecord( $query->fetch(PDO::FETCH_ASSOC) );
}



/*Returns an indexed array of all Thoughts records we have in DB*/
function getAllThoughts() {

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Gets a list of all currently saved Thoughts*/
  $query = $db->prepare('SELECT
                          thought.id        as id,
                          thought.startDate as startDate,
                          thought.endDate   as endDate,

                          thought_author.id as author_id,
                          thought_author.bg as author_bg,
                          thought_author.en as author_en,

                          thought_text.id   as text_id,
                          thought_text.bg   as text_bg,
                          thought_text.en   as text_en

                        FROM thought

                        JOIN thought_author
                        ON thought.authorId = thought_author.id

                        JOIN thought_text
                        ON thought.textId = thought_text.id');

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute()) {
    return;
  }


  $thoughts = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($thoughts) === 'array' &&
      sizeof( $thoughts)
  ) {
    foreach ($thoughts as &$thought) reorderThoughtRecord( $thought );
  }


  return $thoughts;
}



/*
  Will return an error string message if the incoming '$filters'
  are not suitable for filtering search for Thoughts.
  Please be advised that keys in '$filters' that are not used
  for filtering will be deleted from '$filters'.
*/
function validateThoughtsFilters(&$filters) {
  if (!isset( $filters) ||
      gettype($filters) !== 'array'
  ) {
    return 'Invalid filters';
  }


  if (isset($filters['ids'])) {
    if (gettype($filters['ids']) !== 'array') {
      return 'Filter "ids" must be an array';
    }

    foreach ($filters['ids'] as $index => $id) {
      if (!is_numeric($id) &&
          gettype($id) !== 'integer'
      ) {
        return 'Filter "ids" have an invalid ID at index '.$index;
      }
    }
  }


  if (isset($filters['author'])) {
    if (gettype($filters['author']) !== 'string' ||
        !sizeof($filters['author'])
    ) {
      return 'Filter "author" must be a non empty string';
    }
  }


  if (isset($filters['text'])) {
    if (gettype($filters['text']) !== 'string' ||
        !sizeof($filters['text'])
    ) {
      return 'Filter "text" must be a non empty string';
    }
  }


  $date_format = 'Y-m-d H:i:s';

  if (isset($filters['fromDate'])) {
    if (gettype($filters['fromDate']) !== 'string' ||
        !getValidDate($filters['fromDate'], $date_format)
    ) {
      return 'Filter "fromDate" is not a valid "'.$date_format.'" date';
    }
  }


  if (isset($filters['toDate'])) {
    if (gettype($filters['toDate']) !== 'string' ||
        !getValidDate($filters['toDate'], $date_format)
    ) {
      return 'Filter "toDate" is not a valid "'.$date_format.'" date';
    }
  }


  if (isset($filters['fromDate']) &&
      isset($filters['toDate'  ])
  ) {
    $fromDate = strtotime($filters['fromDate']);
    $toDate   = strtotime($filters['toDate'  ]);

    if ($fromDate > $toDate) {
      return 'Filter "fromDate" must not be greater then filter "toDate"';
    }
  }


  /*Be sure to return a list containing only valid filters as keys*/
  $valid_filtering_keys = array('ids', 'author', 'text', 'fromDate', 'toDate');
  foreach ($filters as $key => $value) {
    if (!in_array($key, $valid_filtering_keys, true)) {
      unset( $filters[ $key ] );
    }
  }


  /*
    Return error free response
    since none of the above found an issue
  */
  return;
}



/*Returns an indexed array of all Thoughts records we have in DB*/
function getFilteredThoughts($filters) {

  /*Be sure we can safely use the input for filtering*/
  if ($error_message = validateThoughtsFilters( $filters )) {
    return $error_message;
  }

  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  $where_clauses = array();

  if (isset($filters['ids'])) {
    $where_clauses []= 'thought.id IN ('.implode(', ', $filters['ids']).')';
    unset( $filters['ids'] );
  }

  if (isset($filters['author'])) {
    $where_clauses []= '(thought_author.bg LIKE :author OR
                         thought_author.en LIKE :author)';
    $filters['author'] = '%'.$filters['author'].'%';
  }

  if (isset($filters['text'])) {
    $where_clauses []= '(thought_text.bg LIKE :text OR
                         thought_text.en LIKE :text)';
    $filters['text'] = '%'.$filters['text'].'%';
  }

  if (isset($filters['fromDate'])) {
    $where_clauses []= '(thought.startDate >= :fromDate OR (
                          thought.startDate <= :fromDate AND
                          thought.endDate   > :fromDate
                        ))';
  }

  if (isset($filters['toDate'])) {
    $where_clauses []= '(thought.endDate <= :toDate OR (
                          thought.startDate <= :toDate AND
                          thought.endDate   >  :toDate
                        ))';
  }


  /*Filter the Thoughts by the saved rules in '$where_clauses'*/
  $query = $db->prepare('SELECT
                          thought.id        as id,
                          thought.startDate as startDate,
                          thought.endDate   as endDate,

                          thought_author.id as author_id,
                          thought_author.bg as author_bg,
                          thought_author.en as author_en,

                          thought_text.id   as text_id,
                          thought_text.bg   as text_bg,
                          thought_text.en   as text_en

                        FROM thought

                        JOIN thought_author
                        ON thought.authorId = thought_author.id

                        JOIN thought_text
                        ON thought.textId = thought_text.id
                        '.(sizeof($where_clauses) ? 'WHERE '.implode(' AND ', $where_clauses) : '')
  );

  /*Prevent further calculation if the query fail to load*/
  if (!$query->execute( $filters )) {
    return 'Unable to load results from DB';
  }


  $thoughts = $query->fetchAll(PDO::FETCH_ASSOC);

  /*Reposition raw DB values*/
  if (gettype($thoughts) === 'array' &&
      sizeof( $thoughts)
  ) {
    foreach ($thoughts as &$thought) reorderThoughtRecord( $thought );
  }


  return $thoughts;
}



/*
  Returns a list out of '$properties' that can safely be used to create or update a Thought.
  if 2nd argument '$mandatory_validation' is set to 'true' then only set properties will be validated,
  e.g. if 'type' is set to 'Z' it will be invalid but if not set at all, no error will be send.
*/
function validateThoughtProperties(&$properties, $mandatory_validation = true) {
  if (!isset( $properties) ||
      gettype($properties) !== 'array'
  ) {
    return 'Invalid properties';
  }


  if ($mandatory_validation ||
      isset($properties['author'])
  ) {
    if (!isset( $properties['author']) ||
        gettype($properties['author']) !== 'array'
    ) {
      return 'Invalid "author" property';
    }


    if ($mandatory_validation ||
        isset($properties['author']['bg'])
    ) {
      if (!isset( $properties['author']['bg']) ||
          gettype($properties['author']['bg']) !== 'string'
      ) {
        return 'Invalid "author"->"bg" property';
      }
    }


    if ($mandatory_validation ||
        isset($properties['author']['en'])
    ) {
      if (!isset( $properties['author']['en']) ||
          gettype($properties['author']['en']) !== 'string'
      ) {
        return 'Invalid "author"->"en" property';
      }
    }
  }


  if ($mandatory_validation ||
      isset($properties['text'])
  ) {
    if (!isset( $properties['text']) ||
        gettype($properties['text']) !== 'array'
    ) {
      return 'Invalid "text" property';
    }


    if ($mandatory_validation ||
        isset($properties['text']['bg'])
    ) {
      if (!isset( $properties['text']['bg']) ||
          gettype($properties['text']['bg']) !== 'string'
      ) {
        return 'Invalid "text"->"bg" property';
      }
    }


    if ($mandatory_validation ||
        isset($properties['text']['en'])
    ) {
      if (!isset( $properties['text']['en']) ||
          gettype($properties['text']['en']) !== 'string'
      ) {
        return 'Invalid "text"->"en" property';
      }
    }
  }


  $date_format = 'Y-m-d H:i:s';


  if ($mandatory_validation ||
      isset($properties['startDate'])
  ) {
    if (!isset( $properties['startDate'] ) ||
        !getValidDate( $properties['startDate'], $date_format)
    ) {
      return 'Invalid "startDate" property';
    }
  }


  if ($mandatory_validation ||
      isset($properties['endDate'])
  ) {
    if (!isset( $properties['endDate'] ) ||
        !getValidDate( $properties['endDate'], $date_format)
    ) {
      return 'Invalid "endDate" property';
    }
  }


  if (isset($properties['startDate']) &&
      isset($properties['endDate'  ])
  ) {
    $startDate = strtotime($properties['startDate']);
    $endDate   = strtotime($properties['endDate'  ]);

    if ($startDate > $endDate) {
      return 'Thought "startDate" must not be greater then its "endDate"';
    }
  }


  /*Be sure to return a list containing only valid properties as keys*/
  $valid_property_keys = array('author', 'text', 'startDate', 'endDate');
  foreach ($properties as $key => $value) {
    if (!in_array($key, $valid_property_keys, true)) {
      unset( $properties[ $key ] );
    }

    /*Be sure to leave only valid author values*/
    if ($key === 'author' &&
        gettype($properties['author']) === 'array'
    ) {
      $valid_author_keys = array('bg', 'en');

      foreach ($properties['author'] as $author_key => $author_value) {
        if (!in_array($author_key, $valid_author_keys, true)) {
          unset( $properties['author'][ $author_key ] );
        }
      }
    }/*End of "if key is author"*/


    /*Be sure to leave only valid text values*/
    if ($key === 'text' &&
        gettype($properties['text']) === 'array'
    ) {
      $valid_text_keys = array('bg', 'en');

      foreach ($properties['text'] as $text_key => $text_value) {
        if (!in_array($text_key, $valid_text_keys, true)) {
          unset( $properties['text'][ $text_key ] );
        }
      }
    }/*End of "if key is text"*/
  }


  if (isset(   $properties['author'] ) &&
      !sizeof( $properties['author'] )
  ) {
    unset( $properties['author'] );
  }

  if (isset(   $properties['text'] ) &&
      !sizeof( $properties['text'] )
  ) {
    unset( $properties['text'] );
  }


  return $properties;
}



/*
  Use 'validateThoughtProperties() to save already valid Thought values.
  Will return error {string} in case of failure or newly created Thought as {array}
*/
function createThought($properties) {

  /*Be sure we can use all of the user input to create new Thought*/
  $properties = validateThoughtProperties( $properties );
  if (gettype($properties) !== 'array') {
    return $properties;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*
    Try to create Thought Author first in order to use the Author ID later
    when we create the Thought in its main table
  */
  $query  = $db->prepare('INSERT INTO thought_author ( en,  bg) VALUES
                                                     (:en, :bg)');
  $result = $query->execute(array(
    'en' => $properties['author']['en'],
    'bg' => $properties['author']['bg']
  ));

  /*Prevent further creation if current query fail*/
  if (!$result) {
    return 'Unable to create Thought author';
  }


  /*Set reference ID to already saved author property*/
  unset( $properties['author'] );
  $properties['authorId'] = $db->lastInsertId();


  /*
    Try to create Thought Text first in order to use the Text ID later
    when we create the Thought in its main table
  */
  $query  = $db->prepare('INSERT INTO thought_text ( en,  bg) VALUES
                                                   (:en, :bg)');
  $result = $query->execute(array(
    'en' => $properties['text']['en'],
    'bg' => $properties['text']['bg']
  ));

  /*Prevent further creation if current query fail*/
  if (!$result) {
    return 'Unable to create Thought text';
  }


  /*Set reference ID to already saved text property*/
  unset( $properties['text'] );
  $properties['textId'] = $db->lastInsertId();


  /*Try to create Thought in its main table*/
  $query  = $db->prepare('INSERT INTO thought ( authorId,  textId,  startDate,  endDate) VALUES
                                              (:authorId, :textId, :startDate, :endDate)');
  $result = $query->execute( $properties );

  if (!$result) {
    return 'Unable to create Thought';
  }


  /*Try to show the creation result*/
  return getThoughtByID( $db->lastInsertId() );
}



/*
  Update the Thought with ID '$thought_id' with the values from '$properties'.
  Values validation is secured with 'validateThoughtProperties()'
*/
function updateThought($thought_id, $properties) {

  /*Be sure we have already existing record*/
  $thought = getThoughtByID( $thought_id );
  if (!$thought) {
    return 'Unable to find Thought with ID: '.$thought_id;
  }


  /*This will secure the rule "startDate <= endDate" even if only date needs to be updated*/
  if (isset($properties['startDate']) ||
      isset($properties['endDate'  ])
  ) {
    if (!isset($properties['startDate'])) $properties['startDate'] = $thought['startDate'];
    if (!isset($properties['endDate'  ])) $properties['endDate'  ] = $thought['endDate'  ];
  }


  /*Be sure we can use only correctly set Thought properties*/
  $properties = validateThoughtProperties( $properties, false);
  if (gettype($properties) !== 'array') {
    return $properties;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Check if we need to update Thought author since its not in Thought main table*/
  if (isset(  $properties['author'])             &&
      gettype($properties['author']) === 'array' &&
      sizeof( $properties['author'])
  ) {

    $set_clauses = array();
    foreach ($properties['author'] as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Thought author*/
    $query  = $db->prepare('UPDATE thought_author SET '.implode(', ', $set_clauses).' WHERE id='.$thought['author_id']);
    $result = $query->execute( $properties['author'] );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Thought author';
    }


    /*
      Remove the need for updating this property since
      no updating errors were triggered so far
    */
    unset( $properties['author'] );
  }


  /*Check if we need to update Thought text since its not in Thought main table*/
  if (isset(  $properties['text'])             &&
      gettype($properties['text']) === 'array' &&
      sizeof( $properties['text'])
  ) {

    $set_clauses = array();
    foreach ($properties['text'] as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Thought text*/
    $query  = $db->prepare('UPDATE thought_text SET '.implode(', ', $set_clauses).' WHERE id='.$thought['text_id']);
    $result = $query->execute( $properties['text'] );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Thought text';
    }


    /*
      Remove the need for updating this property since
      no updating errors were triggered so far
    */
    unset( $properties['text'] );
  }


  /*Check if there are properties to be updated in the main table*/
  if (gettype($properties) === 'array' &&
      sizeof( $properties)
  ) {
    $set_clauses = array();
    foreach ($properties as $key => $value) {
      $set_clauses []= $key.'=:'.$key;
    }

    /*Try to update Thought in its main table*/
    $query  = $db->prepare('UPDATE thought SET '.implode(', ', $set_clauses).' WHERE id='.$thought['id']);
    $result = $query->execute( $properties );

    /*Prevent further update if current query fail*/
    if (!$result) {
      return 'Unable to update Thought';
    }
  }


  /*Return the updated record so callee can verify the process too*/
  return getThoughtByID( $thought['id'] );
}



/*
  Tries to remove all known records for Thought with ID '$thought_id'.
  Returns an error {string} or {boolean} 'false'.
*/
function deleteThought($thought_id) {

  /*Be sure we have already existing record*/
  $thought = getThoughtByID( $thought_id );
  if (gettype($thought) !== 'array') {
    return 'Unable to find Thought with ID: '.$thought_id;
  }


  /*Access the common DB connection handler*/
  require_once('./models/db_manager.php');
  $db = getDBConnection();


  /*Try to delete Thought author first hence the foreign key constraint*/
  $query  = $db->prepare('DELETE FROM thought_author WHERE id=:id');
  $result = $query->execute(array('id' => $thought['author_id']));

  /*Prevent further delete if current query fail*/
  if (!$result) {
    return 'Unable to delete Thought author';
  }


  /*Try to delete Thought text first hence the foreign key constraint*/
  $query  = $db->prepare('DELETE FROM thought_text WHERE id=:id');
  $result = $query->execute(array('id' => $thought['text_id']));

  /*Prevent further delete if current query fail*/
  if (!$result) {
    return 'Unable to delete Thought text';
  }


  $query  = $db->prepare('DELETE FROM thought WHERE id=:id');
  $result = $query->execute(array('id' => $thought['id']));

  if (!$result) {
    return 'Unable to delete Thought from its main table';
  }


  /*Be sure no one can get the deleted information*/
  $deleted_thought = getThoughtByID( $thought['id'] );
  if (gettype($deleted_thought) === 'array') {
    return 'Unable to delete Thought';
  }

  return;
}