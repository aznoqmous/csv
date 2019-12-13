<?php

namespace Aznoqmous;

class CSV {

  public $file = null;
  public $rows = null;
  public $bannedRows= [];

  public function __construct($file, $rows=false){
    $this->file = $file;
    $this->rows = $rows;

    $this->lineBreaks = '\n';
    $this->separator = ';';
    $this->textDelimiter = '"';

    $this->lineBreaks_replacement = '_EOL_';
    $this->separator_replacement = '_SEPARATOR_';
    $this->textDelimiter_replacement = '_TEXT_';

  }

  public function load($file=false){
    $file = ($file)?:$this->file;
    $handle = fopen($file, 'r');
    $content = file_get_contents($file);
    $content = preg_replace("/\"{$this->lineBreaks}\"/", '_DELIMITER_"', $content);
    $lines = explode("_DELIMITER_", $content);

    if(!$this->rows) {
      $firstLine = $lines[0];
      $firstLine = preg_replace('/[^A-z_\-;,]/s', '', $firstLine);
      $this->rows = explode($this->separator, $firstLine);
      array_splice($lines, 0, 1);
    }

    $this->content = $this->extractContent($lines, $this->bannedRows);

    return $this->content;
  }

  public function replaceTextSpecialsChars($content, $char, $replacement){
    $reg = "/{$this->textDelimiter}[^{$this->textDelimiter}{$this->separator}]*?{$char}[^{$this->textDelimiter}{$this->separator}]*?{$this->textDelimiter}/s";
    preg_match_all("$reg", $content, $matches);
    foreach($matches as $match){
      $re_match = preg_replace('/\n/', $replacement, $match);
      $content = str_replace($match, $re_match, $content);
    }
    return $content;
  }

  public function extractContent($lines, $bannedRows=false)
  {
    $bannedRows=($bannedRows)?:$this->bannedRows;
    $contents = [];
    foreach($lines as $i => $line){
      $line = html_entity_decode($line);
      $line = $this->replaceTextSpecialsChars($line, $this->separator, $this->separator_replacement);
      $line = $this->replaceTextSpecialsChars($line, $this->separator, $this->separator_replacement);
      $line = $this->textDelimiter . $this->separator . $line . $this->separator . $this->textDelimiter;

      $values = explode($this->textDelimiter . $this->separator . $this->textDelimiter, $line);
      array_splice($values, 0, 1);
      array_splice($values, -1, 1);

      // preg_match_all("/{$this->separator}{$this->textDelimiter}(.*?){$this->textDelimiter}{$this->separator}/s", $line, $matches);
      foreach($values as $key => $value){
        if(array_key_exists($key, $this->rows) && !in_array($this->rows[$key], $bannedRows)){
           $content[$this->rows[$key]] = $value;
        }
      }
      $contents[] = $content;
    }
    return $contents;
  }

}
