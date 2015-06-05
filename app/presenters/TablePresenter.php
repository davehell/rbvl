<?php

require_once dirname(__FILE__) . '/BasePresenter.php';

class TablePresenter extends BasePresenter
{
    /** @persistent */
    public $page = 0;

    /** @persistent */
    public $order = '';


    public function renderDefault($table, $orderBy)
    {
        // sorting
        parse_str($this->order, $list);
        if ($orderBy) {
            if (!isset($list[$orderBy])) {
                $list[$orderBy] = 'a';
            } elseif ($list[$orderBy] === 'd') {
                unset($list[$orderBy]);
            } else {
                $list[$orderBy] = 'd';
            }
        }
        $this->order = http_build_query($list, '', '&');

        // paging
        $rowsPerPage = 15;
        $numOfRows = $this->db->select('count(*)')->from($table)->fetchSingle();
        $numOfPages = (int) ceil($numOfRows / $rowsPerPage);
        $this->page = max(0, min($this->page, $numOfPages - 1));

        // paginator
        if ($numOfPages > 1) {
            $steps = range(max(0, $this->page - 3), min($numOfPages - 1, $this->page + 3));
            $steps = array_merge($steps, range(0, $numOfPages - 1, (int) ceil($numOfPages / 5)));
            $steps[] = $numOfPages - 1;
            sort($steps);
            $steps = array_unique($steps);
        } else {
            $steps = array();
        }

        // retrieving data
        $query = $this->db->select('*')
            ->from($table)
            ->offset($this->page * $rowsPerPage)
            ->limit($rowsPerPage);

        $i = 1;
        foreach ($list as $field => $dir) {
            $query->orderBy($field, $dir === 'a' ? 'ASC' : 'DESC');
            $list[$field] = array($dir, $i++);
        }

        $rowset = $query->execute();

        $this->template->table = $table;
        $this->template->order = $list;
        $this->template->currentOrder = $this->order;
        $this->template->currentPage = $this->page;
        $this->template->numOfPages = $numOfPages;
        $this->template->steps = $steps;
        $this->template->rows = $rowset->fetchAll();
        $this->template->columns = $rowset->getColumnNames();
    }

}
