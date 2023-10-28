<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\DataTables;

use Apex\Svc\{App, Db, Convert};
use Apex\App\Interfaces\Opus\DataTableInterface;

/**
 * Data Table - ~alias~
 */
class ~alias.title~ implements DataTableInterface
{

    // Columns
    public array $columns = [
~columns~
    ];
    public array $sortable = ['id'];

    // Other variables
    public int $rows_per_page = 25;
    public bool $has_search = false;

    // Form field (left-most column)
    public string $form_field = 'checkbox';
    public string $form_name = '~alias.single.lower~_id';
    public string $form_value = 'id'; 

    // Delete button
    public string $delete_button = 'Delete Checked ~alias.phrase.plural~';
    public string $delete_dbtable = '~dbtable~';
    public string $delete_dbcolumn = 'id';

    /**
     * Constructor
     *
     * All attributes within the <s:function> tag are passed to this method during instantiation, 
     * and here you can define various properties (ie. userid, type, et al) to be used in below methods.
     */
    public function __construct(
        private array $attr, 
        private App $app, 
        private Db $db,
        private Convert $convert
    ) { 

    }

    /**
     * Get total rows in data set - used for pagination.
     */
    public function getTotal(string $search_term = ''):int 
    {

        // Get total
        if ($search_term != '') { 
            $total = $this->db->getField("SELECT count(*) FROM ~dbtable~ WHERE some_column LIKE %ls", $search_term);
        } else { 
            $total = $this->db->getField("SELECT count(*) FROM ~dbtable~");
        }

        // Return
        return (int) $total;
    }

    /**
     * Get rows to display on current page.
     *
     * Should return an array with each element being an associative array representing one table row.
     */ 
    public function getRows(int $start = 0, string $search_term = '', string $order_by = 'id asc'):array 
    {

        // Get rows
        if ($search_term != '') { 
            $rows = $this->db->query("SELECT * FROM ~dbtable~ WHERE some_column LIKE %ls ORDER BY $order_by LIMIT $start,$this->rows_per_page", $search_term);
        } else { 
            $rows = $this->db->query("SELECT * FROM ~dbtable~ ORDER BY $order_by LIMIT $start,$this->rows_per_page");
        }

        // Go through rows
        $results = [];
        foreach ($rows as $row) { 
            $results[] = $this->formatRow($row);
        }

        // Return
        return $results;
    }

    /**
     * Format individual row for display to browser.
     */
    public function formatRow(array $row):array
    {

        // Format row
~format_code~

        // Return
        return $row;
    }

}

