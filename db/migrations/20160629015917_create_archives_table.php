<?php

use Phinx\Migration\AbstractMigration;

class CreateArchivesTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('archives')
             ->addColumn('uri', 'string', ['null' => false])
             ->addColumn('title', 'string', ['null' => false])
             ->addColumn('description', 'text', ['null' => true])
             ->addColumn('image', 'string', ['null' => true])
             ->addColumn('link_url', 'string', ['null' => true])
             ->addColumn('category', 'string', ['limit' => 50, 'null' => false])
             ->addColumn('keywords', 'string', ['null' => true])
             ->addColumn('author_user_id', 'integer', ['null' => true])
             ->addColumn('author_name', 'string', ['limit' => 80, 'null' => false])
             ->addColumn('author_twitter', 'string', ['limit' => 140, 'null' => true])
             ->addColumn('author_url', 'string', ['null' => true])
             ->addColumn('post_date', 'datetime', ['null' => false])
             ->addColumn('updated_date', 'datetime', ['null' => true])
             ->addColumn('status', 'integer', ['null' => false, 'limit' => 1, 'default' => 0])
             ->addIndex('title')
             ->addIndex('uri')
             ->addIndex('category')
             ->addIndex('author_user_id')
             ->addIndex('author_name')
             ->addIndex('author_twitter')
             ->addIndex('author_url')
             ->addIndex('post_date')
             ->addIndex('updated_date')
             ->addIndex('status')
             ->create();
    }
}
