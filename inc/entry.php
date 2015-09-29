<?php

class Entry {
	public $hash;
	public $editor_hash;
	public $text_content;
	public $date_created;
	public $date_edited;
	public $title;
	public $description;
	
	public static function createFromDBRow($row) {
        $obj = new Entry(
	        $row['hash'],
	        $row['editor_hash'],
	        $row['text_content'],
	        $row['date_created'],
	        $row['date_edited'],
	        $row['title'],
	        $row['description']
		);
        return $obj;
    }
	
	function __construct($hash, $editor_hash, $text_content, $date_created, $date_edited, $title, $description) {
		$this->hash = $hash;
		$this->editor_hash = $editor_hash;
		$this->text_content = $text_content;
		$this->date_created = $date_created;
		$this->date_edited = $date_edited;
		$this->title = $title;
		$this->description = $description;
	}
	
	function getTextContent() {
		return htmlspecialchars($this->text_content);
	}
	
	function getTitle() {
		return htmlspecialchars($this->title);
	}
	
	function getDescription() {
		return htmlspecialchars($this->description);
	}
}
?>