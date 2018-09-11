<?php
namespace Dialect\Scrive\Model;

use Illuminate\Support\Facades\File;
use phpDocumentor\Reflection\DocBlock;

class Document extends Model {



    public function __construct($id = null, $data = null)
    {
        parent::__construct($id, $data);
    }

    public function create($filePath = null, $saved = false) {
      $data = [];

      $data[] = [
          'name' => 'saved',
          'contents' => $saved ? 'true' : 'false'
      ];

      if($filePath) {
          $data[] = [
              'name' => 'file',
              'contents' => fopen($filePath, 'r'),
              'filename' => basename($filePath),
          ];
      }

      $this->data = $this->callApi('POST', 'documents/new', $data);
      $this->id = $this->data->id;
      return $this;
    }

    public function get() {
        $this->data = $this->callApi('GET', 'documents/'.$this->id.'/get');
        return $this;
    }

    public function list($offset = 0, $max = null, $filter = [], $sorting = []) {

        $query = '?offset='.$offset;
        if($max) $query .= '&max='.$max;
        if($filter) $query .= '&filter=' .json_encode($filter);
        if($sorting) $query .= '&sorting='.json_encode($sorting);

        $data = $this->callApi('GET', 'documents/list'.$query);
        $documents = collect();
        foreach($data->documents as $rawDoc){
            $documents->push(new Document($rawDoc->id, $rawDoc));
        }
        return $documents;
    }

    public function start() {
        if(!$this->id) {
            throw new \Exception('Invalid id '.$this->id);
        }
        $this->data = $this->callApi('POST', 'documents/'.$this->id.'/start');
        return $this;
    }

}
