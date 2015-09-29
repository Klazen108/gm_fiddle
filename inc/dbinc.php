<?php

include "dbconfig.php";
include "HashGenerator.php";
include "Hashids.php";

function addEntry($text,$title,$description) {
	$success=false;
	$db = connectDB();
	$db->beginTransaction();
	$query ="INSERT INTO ".DB_TABLE." (text_content,title,description) VALUES ( :text , :title , :desc )";
	$params = array(
		":text"=>$text,
		":title"=>$title,
		":desc"=>$description,
	);
	$rowCount = execute($db,$query,$params);
	if ($rowCount>0) {
		$last_id = $db->lastInsertId();
		
		$hashids = new Hashids\Hashids('welcome to the salty splatoon how tough are ya');
		$hash = $hashids->encode($last_id);
		$editor_hash = bin2hex(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));

		$query ="
		UPDATE ".DB_TABLE."
		SET hash = :hash ,
			editor_hash = :ehash 
		WHERE id = :id ";
		$params = array(
			":hash"=>$hash,
			":ehash"=>$editor_hash,
			":id"=>$last_id
		);
		$rowCount = execute($db,$query,$params);
		
		if ($rowCount>0) {
			$db->commit();
			$success=true;
		} else {
			$db->rollBack();
		}
	} else {
		$db->rollBack();
		//if ($last_id) execute($db,"DELETE FROM ".DB_TABLE." WHERE id = :id ",array(":id"=>$last_id));
	}
	close($db);
	if ($success) return getEntryById($last_id);
	else return null;
}

function updateEntry($hash,$editor_hash,$text,$title,$description) {
	$db = connectDB();
	$query ="
UPDATE ".DB_TABLE."
SET text = :text ,
	title = :title ,
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
	$entry = Entry::createFromDBRow($results[0]);
	close($db);
	return $entry;
}

function getEntryById($id) {
	$db = connectDB();
	$results = query($db,"SELECT * FROM ".DB_TABLE." WHERE id = :id",array(":id"=>$id));
	
	//should only be one
	$entry = Entry::createFromDBRow($results[0]);
	close($db);
	return $entry;
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
			} else {
				$result = $stmt->rowCount();
			}
		} catch (PDOException $e) {
			$result = 'Wrong SQL: ' . $query . ' Error: ' . $e->getMessage();
		}
	}
	return $result;
}
?>