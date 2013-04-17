<?php

abstract class peAsync
{
    public static function main($data) 
    {
        print(
            $data->html
        );
    }
}

/*
$.ajax({
  type: "POST",
  url: "async.php",
  data: { name: "John", location: "Boston" }
}).done( function( msg ) {
  alert( "Data Saved: " + msg );
});
 */