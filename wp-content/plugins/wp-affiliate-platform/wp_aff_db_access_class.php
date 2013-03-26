<?php
class WP_Affiliate_Db_Access
{
   function  __construct() {
      die();
   }
   function WP_eStore_Db_Access(){
      die();
   }
   function find($inTable, $condition)
   {
      global $wpdb;
      if(empty($condition)){
          return null;
      }
      $resultset = $wpdb->get_row("SELECT * FROM $inTable WHERE $condition", OBJECT);
      return $resultset;
   }
   function findAll($inTable, $condition=null, $orderby=null)
   {
      global $wpdb;
      $condition = empty ($condition)? '' : ' WHERE ' .$condition;
      $condition .= empty($orderby)? '': ' ORDER BY ' . $orderby;
      $resultSet = $wpdb->get_results("SELECT * FROM $inTable $condition ", OBJECT);
      return $resultSet;
   }
   function delete($fromTable, $condition)
   {
      global $wpdb;
      $resultSet = $wpdb->query("DELETE FROM $fromTable WHERE $condition ");
      return $resultSet;
   }
   function insert($inTable, $fields)
   {
      global $wpdb;
      $fieldss = '';
      $valuess = '';
      $first = true;
      foreach($fields as $field=>$value)
      {
         if($first)
            $first = false;
         else
         {
            $fieldss .= ' , ';
            $valuess .= ' , ';
         }
         $fieldss .= " $field ";
         $valuess .= " '" .$wpdb->escape($value)."' ";
      }

      $query = " INSERT INTO $inTable ($fieldss) VALUES ($valuess)";

      $results = $wpdb->query($query);
      return $results;
   }
   function update($inTable, $condition, $fields)
   {
      global $wpdb;
      $query = " UPDATE $inTable SET ";
      $first = true;
      foreach($fields as $field=>$value)
      {
         if($first) $first = false; else $query .= ' , ';
         $query .= " $field = '" . $wpdb->escape($value) ."' ";
      }

      $query .= empty($condition)? '': " WHERE $condition ";
      $results = $wpdb->query($query);
      return $results;
   }
}
?>