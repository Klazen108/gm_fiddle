<?php

include "dbconfig.php";

function addEntry($hash,$editor_hash,$text,$title,$description) {
	$db = connectDB();
	$query ="INSERT INTO ".DB_TABLE." (hash,editor_hash,text_content,title,description) VALUES ( :hash , :ehash , :text , :title , :desc )";
	$params = array(
		":hash"=>$hash,
		":ehash"=>$editor_hash,
		":text"=>$text,
		":title"=>$title,
		":desc"=>$description,
	);

	$result = execute($db,$query,$params);
	close($db);
	
	return $result;
}

function updateEntry($hash,$editor_hash,$text,$title,$description) {
	$db = connectDB();
	$query ="
UPDATE ".DB_TABLE."
SET text = :text 
	title = :title 
	description = :desc 
WHERE hash = :hash 
AND   editor_hash = :ehash ";
	$params = array(
		":hash"=>$hash,
		":ehash"=>$editor_hash,
		":text"=>$text,
		":title"=>$title,
		":desc"=>$description,
	);

	$result = execute($db,$query,$params);
	close($db);
	
	return $result;
}

function getEntry($hash) {
	$db = connectDB();
	$results = query($db,"SELECT * FROM ".DB_TABLE." WHERE hash = :hash",array(":hash"=>$hash));
	
	//should only be one
	$user = User::createFromDBRow($results[0]);
	close($db);
	return $user;
}

/* Private convenience functions - do not use outside of this file! */

function connectDB() {
	try {
		$db = new PDO(DB_PDO_DSN,DB_USER,DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
		return $db;
	}
	catch (PDOException $e) {
		trigger_error('DB Connection failed: ' . $e->getMessage(),E_USER_ERROR);
		return null;
	}
}

function close($db) {
	$db = null;
}

function query($db, $query, $parameters) {
	$result=null;
	if (!is_null($db)) {
		try {
			$stmt = $db->prepare($query);
			if (!$stmt) throw new PDOException('Bad query');
			
			$stmt->execute($parameters);
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $e->getMessage(), E_USER_ERROR);
		}
	}
	return $result;
}

/**
 * Returns null if successful, or an error string if an error occurred
 */
function execute($db, $query, $parameters) {
	$result=null;
	if (!is_null($db)) {
		try {
			$stmt = $db->prepare($query);
			if (!$stmt->execute($parameters)) {
				$result = $stmt->errorInfo();
				$result = $result[2];
			}
		} catch (PDOException $e) {
			$result = 'Wrong SQL: ' . $query . ' Error: ' . $e->getMessage();
		}
	}
	return $result;
}
?>