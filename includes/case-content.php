<?php
declare(strict_types=1);
function case_records(?PDO $connection=null): array {
 try{
  if(!$connection){
   if(function_exists('db'))$connection=db();
   else{$settings=require __DIR__.'/config.php';$connection=new PDO($settings['database']['dsn'],$settings['database']['user'],$settings['database']['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}
  }
  return $connection->query("SELECT * FROM case_studies WHERE status='published' AND evidence_approved=1 ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
 }catch(Throwable $e){return [];}
}
