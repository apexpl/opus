<?php
declare(strict_types = 1);

namespace Apex\Opus\Builders;

use Apex\Opus\Builders\{Builder, ModelBuilder};
use Apex\Opus\Helpers\DatabaseHelper;
use Apex\App\Pkg\Helpers\Registry;
use Apex\Container\Di;
use Apex\Db\Interfaces\DbInterface;

/**
 * CRUD builder
 */
class CrudBuilder extends AbstractBuilder
{

    /**
     * Constructor
     */
    public function __construct(
        protected Builder $builder,
        protected ModelBuilder $model_builder,
        protected DatabaseHelper $db_helper
    ) {

    }

    /**
     * Build
     */
    public function build(string $filename, string $dbtable, string $view, bool $with_magic, string $rootdir = '', bool $auto_confirm = false):array
    {

        // Get namespace
        list($namespace, $class_name) = $this->pathToNamespace($filename);
        $alias = $class_name;
        $alias_single = $this->applyFilter($alias, 'single');
        $alias_plural = $this->applyFilter($alias, 'plural');
        $controller_name = $alias . 'Controller';

        // Get package alias
        $parts = explode("\\", $namespace);
        $pkg_alias = $parts[1] ?? '';

        // Table to properties
        $props = $this->db_helper->tableToProperties($dbtable, dirname("$rootdir/$filename"));

        // Generate needed code
        $view = '/' . trim($view, '/');
        $code = $this->generateTableColumns($dbtable, $alias, $view, $props);

        // Build model
        $files = [];
        if (!file_exists($filename)) {
            $files = $this->model_builder->build($filename, $rootdir, $dbtable, 'php8', $with_magic, $auto_confirm);
        }

        // Build controller
        $files[] = $this->buildController($pkg_alias, $controller_name, [
            'model_name' => $alias_single,
            'model_namespace' => $namespace,
            'package' => $pkg_alias,
            'controller_name' => $controller_name,
            'insert_code' => $code['insert_code'],
            'update_code' => $code['update_code']
        ]);

        // Build data table
        list($dirs, $tmp_files) = $this->builder->build('data_table', $rootdir, [
            'alias' => $alias_plural,
            'package' => $pkg_alias,
            'dbtable' => $dbtable,
            'columns' => $code['columns'],
            'format_code' => $code['format_code']
        ]);
        array_push($files, ...$tmp_files);

        // Build form
        list($dirs, $tmp_files) = $this->builder->build('form', $rootdir, [
            'alias' => $alias_single,
            'package' => $pkg_alias,
            'dbtable' => $dbtable
        ]);
        array_push($files, ...$tmp_files);

        // Get parent namespace
        $parts = explode('/', trim($view, '/'));
        $view_alias = array_pop($parts);
        $parent_nm = count($parts) > 0 ? "\\" . implode("\\", $parts) : '';

        // Generate views
        list($dirs, $tmp_files) = $this->builder->build('crud_views', $rootdir, [
            'package' => $pkg_alias,
            'alias_single' => $alias_single,
            'alias_plural' => $alias_plural,
            'model_namespace' => $namespace,
            'model_class_name' => $class_name,
            'parent_namespace' => $parent_nm,
            'class_name' => $view_alias,
            'controller_name' => $controller_name,
            'view_path' => trim($view, '/'),
            'record_id_merge_field' => '~' . $this->applyFilter($alias, 'lower') . '_id~'
        ]);
        array_push($files, ...$tmp_files);

        // Add view to registry
        $registry = Di::make(Registry::class, ['pkg_alias' => $pkg_alias]);
        $registry->add('views', $view);
        $registry->add('views', $view . '_manage');

        // Return
        return $files;
    }

    /**
     * Generate table columns
     */
    private function generateTableColumns(string $dbtable, string $name, string $view, array $props):array
    {

        // Initialize
        $code = [
            'columns' => '',
            'format_code' => '',
            'insert_code' => '',
            'update_code' => ''
        ];

        // Get primary key
        $db = Di::get(DbInterface::class);
        $primary_key = $db->getPrimaryKey($dbtable);

        // GO through properties
        foreach ($props as $alias => $vars) {

            // Skip, if primary key
            if ($alias == $primary_key) {
                continue;
            }

            // Add to insert / update code
            if ($vars['type'] != 'DateTime') {
                $code['insert_code'] .= "            '$alias' => \$post['$alias'],\n";
                $code['update_code'] .= "            '$alias' => \$post['$alias'],\n";
            }

            // Skip, if needed
            if ($vars['type'] == 'bool') {
                continue;
            } elseif ($vars['type'] == 'DateTime' && $alias != 'created_at') {
                continue;
            }

            // Add to columns
            $code['columns'] .= "        '$alias' => '" . $this->applyFilter($alias, 'phrase') . "',\n";

            // Add to format code
            if ($vars['type'] == 'DateTime') {
                $code['format_code'] .= "        \$row['$alias'] = \$row['$alias'] === null ? '' : \$this->convert->date(\$row['$alias'], true);\n";
            } elseif ($vars['type'] == 'float') {
                $code['format_code'] .= "        \$row['$alias'] = \$this->convert->money(\$row['$alias']);\n";
            } elseif ($vars['type'] == 'bool') {
                $code['format_code'] .= "        \$row['$alias'] = \$row['$alias'] == 1 ? 'Yes' : 'No';\n";
            }
        }

        // Add manage button
        $code['columns'] .= "        'manage' => 'Manage',\n";
        $code['format_code'] .= "        \$row['manage'] = \"<center><s:button href=\\\"" . $view . "_manage?" . $this->applyFilter($name, 'lower') . "_id=\$row[id]\\\" label=\\\"Manage\\\"></center>\";\n";

        // Trim code
        $code['columns'] = rtrim($code['columns'], ",\n");
        $code['format_code'] = rtrim($code['format_code']);

        // Return
        return $code;
    }

    /**
     * Build controller
     */
    private function buildController(string $pkg_alias, string $controller_name, array $vars): string
    {

        // Get code
        $code = file_get_contents(__DIR__ . '/../../skel/crud/controller.php');
        $vars['namespace'] = "App\\" . $pkg_alias . "\\Controllers";

        // Replace code
        foreach ($vars as $key => $value) {
            $code = str_replace("~$key~", $value, $code);
        }

        // Save file
        $filename = '/src/' . $pkg_alias . '/Controllers/' . $controller_name . '.php';
        if (!is_dir(dirname(SITE_PATH . $filename))) {
            mkdir(dirname(SITE_PATH . $filename), 0755, true);
        }
        file_put_contents(SITE_PATH . $filename, $code);

        // return
        return $filename;
    }

}




