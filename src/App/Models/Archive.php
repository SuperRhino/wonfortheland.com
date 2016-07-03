<?php
namespace App\Models;

use Core\Database\Model;
use Core\Http\Exception\BadRequestException;

class Archive extends Model {

    var $id;
    var $title;
    var $uri;
    var $description;
    var $image;
    var $link_url;
    var $category;
    var $keywords;
    var $author_user_id;
    var $author_name;
    var $author_twitter;
    var $author_url;
    var $post_date;
    var $updated_date;
    var $status;

    function __construct($values = [])
    {
        $this->setTableName('archives');

        $this->id = (int) array_get($values, 'id');
        $this->title = array_get($values, 'title');
        $this->uri = array_get($values, 'uri', 'page-'.time());
        $this->description = array_get($values, 'description');
        $this->image = array_get($values, 'image');
        $this->link_url = array_get($values, 'link_url');
        $this->category = array_get($values, 'category', 'Other') ?: 'Other';
        $this->keywords = array_get($values, 'keywords');
        $this->author_user_id = (int) array_get($values, 'author_user_id') ?: null;
        $this->author_name = array_get($values, 'author_name', 'Anonymous') ?: 'Anonymous';
        $this->author_twitter = array_get($values, 'author_twitter');
        $this->author_url = array_get($values, 'author_url');
        $this->post_date = array_get($values, 'post_date') ?: date('Y-m-d H:i:s');
        $this->updated_date = array_get($values, 'updated_date');
        $this->status = array_get($values, 'status') ? 1 : 0;
    }

    public function updateData($values = [])
    {
        if (isset($values['title'])) {
            $this->title = array_get($values, 'title');
        }
        if (isset($values['uri'])) {
            $this->uri = array_get($values, 'uri');
        }
        if (isset($values['description'])) {
            $this->description = array_get($values, 'description');
        }
        if (isset($values['image'])) {
            $this->image = array_get($values, 'image');
        }
        if (isset($values['link_url'])) {
            $this->link_url = array_get($values, 'link_url');
        }
        if (isset($values['category'])) {
            $this->category = array_get($values, 'category');
        }
        if (isset($values['keywords'])) {
            $this->keywords = array_get($values, 'keywords');
        }
        if (isset($values['author_user_id'])) {
            $this->author_user_id = (int) array_get($values, 'author_user_id');
        }
        if (isset($values['author_name'])) {
            $this->author_name = array_get($values, 'author_name');
        }
        if (isset($values['author_twitter'])) {
            $this->author_twitter = array_get($values, 'author_twitter');
        }
        if (isset($values['author_url'])) {
            $this->author_url = array_get($values, 'author_url');
        }
        if (isset($values['post_date'])) {
            $this->post_date = array_get($values, 'post_date');
        }
        if (isset($values['status'])) {
            $this->status = array_get($values, 'status') ? 1 : 0;
        }
    }

    public function save()
    {
        if (! $this->id) {
            $this->createPage();
        } else {
            $this->updatePage();
        }

        return $this;
    }

    protected function delete()
    {
        $query = static::$app->query->newDelete();
        $query->from($this->table_name)
              ->where('id = ?', $this->id);

      // prepare the statement + execute with bound values
      $sth = static::$app->db->prepare($query->getStatement());
      $sth->execute($query->getBindValues());
    }

    protected function createPage()
    {
        // TODO (see below)
        //$description = str_ireplace('<img ', '<img class="img-responsive" ', $this->description);

        $insert = static::$app->query->newInsert();
        $insert->into($this->table_name)
               ->cols($this->getQueryCols());

        // prepare the statement + execute with bound values
        $sth = static::$app->db->prepare($insert->getStatement());

        try {
            $sth->execute($insert->getBindValues());
        } catch (\PDOException $e) {
            // TODO Send to e->getMessage to error log, fail with generic error:
            throw new BadRequestException($e->getMessage());
        }

        $this->id = static::$app->db->lastInsertId();

        return $this->id;
    }

    protected function updatePage()
    {
        $update = static::$app->query->newUpdate();
        $update->table($this->table_name)
               ->cols($this->getQueryCols())
               ->where('id = ?', $this->id);

        // prepare the statement + execute with bound values
        $sth = static::$app->db->prepare($update->getStatement());
        $sth->execute($update->getBindValues());

        return $this->id;
    }

    protected function getQueryCols()
    {
        return [
            'title' => $this->title,
            'uri' => $this->uri,
            'description' => $this->description,
            'image' => $this->image,
            'link_url' => $this->link_url,
            'category' => $this->category,
            'keywords' => $this->keywords,
            'author_user_id' => $this->author_user_id,
            'author_name' => $this->author_name,
            'author_twitter' => $this->author_twitter,
            'author_url' => $this->author_url,
            'post_date' => $this->post_date,
            'status' => $this->status,
        ];
    }

    public function toArray()
    {
        return [
            'id' => (int) $this->id,
            'title' => $this->title,
            'uri' => $this->uri,
            'description' => $this->description,
            'image' => $this->image,
            'link_url' => $this->link_url,
            'category' => $this->category,
            'keywords' => $this->keywords,
            'author_user_id' => $this->author_user_id,
            'author_name' => $this->author_name,
            'author_twitter' => $this->author_twitter,
            'author_url' => $this->author_url,
            'post_date' => $this->post_date,
            'updated_date' => $this->updated_date,
            'status' => $this->status,
        ];
    }

    public static function findMostRecent($limit = 3)
    {
        $query = static::$app->query->newSelect();
        $query->cols(['*'])
              ->from('archives')
              ->where('status=1')
              ->orderBy(['post_date desc'])
              ->limit($limit);

        $pages = [];
        $res = static::$app->db->fetchAll($query);
        foreach ($res as $page) {
            $pages []= new Archive($page);
        }

        return $pages;
    }

    public static function findActiveByCategory($category)
    {
        $query = static::$app->query->newSelect();
        $query->cols(['*'])
              ->from('archives')
              ->where('status=1')
              ->where('category LIKE "'. $category .'"')
              ->orderBy(['post_date desc'])
              ->limit(100);

        $pages = [];
        $res = static::$app->db->fetchAll($query);
        foreach ($res as $page) {
            $pages []= new Archive($page);
        }

        return $pages;
    }

    public static function findByPageName($pageName)
    {
        $query = static::$app->query->newSelect();
        $query->cols(['*'])
              ->from('archives')
              ->where('status=1')
              ->where('uri="'.$pageName.'"')
              ->limit(1);

        $result = static::$app->db->fetchOne($query);
        if (! $result) {
            return null;
        }

        return new Archive($result);
    }

    public static function findById($pageId)
    {
        $query = static::$app->query->newSelect();
        $query->cols(['*'])
              ->from('archives')
              ->where('id='.$pageId);

        $result = static::$app->db->fetchOne($query);
        if (! $result) {
            return null;
        }

        return new Page($result);
    }

    public static function findAll()
    {
        $query = static::$app->query->newSelect();
        $query->cols(['*'])
              ->from('archives')
              ->orderBy(['if(updated_date, updated_date, post_date) desc']);

        $pages = [];
        $res = static::$app->db->fetchAll($query);
        foreach ($res as $page) {
            $pages []= (new Page($page))->toArray();
        }

        return $pages;
    }
}