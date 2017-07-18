<!--h-->
# Data Table
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/ee67de45d1f14dbd98eb72c8cf972902)](https://www.codacy.com/app/laravel-enso/DataTable?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=laravel-enso/DataTable&amp;utm_campaign=Badge_Grade)
[![StyleCI](https://styleci.io/repos/85495802/shield?branch=master)](https://styleci.io/repos/85495802)
[![License](https://poser.pugx.org/laravel-enso/datatable/license)](https://https://packagist.org/packages/laravel-enso/datatable)
[![Total Downloads](https://poser.pugx.org/laravel-enso/datatable/downloads)](https://packagist.org/packages/laravel-enso/datatable)
[![Latest Stable Version](https://poser.pugx.org/laravel-enso/datatable/version)](https://packagist.org/packages/laravel-enso/datatable)
<!--/h-->

DataTable package for the [DataTables.net](https://datatables.net/) library with server-side processing and a VueJS component.

[![Watch the demo](https://laravel-enso.github.io/datatable/screenshots/Selection_001_thumb.png)](https://laravel-enso.github.io/datatable/videos/demo_01.webm)
<sup>click on the photo to view a short demo in compatible browsers</sup>

### Details
Supports:
- searching in all the columns of the table (you can also choose to exclude some columns)
- hiding of columns
- reordering of columns
- sorting on any column (you can also choose to exclude some columns within the configuration)
- totals
- user configurable pagination
- reloading of data
- visual aides, directly from the interface, such as displaying a table as compact and adding alternate row coloring
- custom rendering of data in columns
- automatic display of show/edit/delete buttons based on available permissions
- excel exporting of the table data, using current search filtering, up to a configurable limit
- permits overriding of the appends attribute for the main query model
- inline editing of values<sup>1</sup> <sup>2</sup>
and more

<sup>1</sup> requires the purchase of the DataTables.net Editor library, as it's a commercial feature

<sup>2</sup> there are some limitations when editing aggregated data

### Installation

1. Add `LaravelEnso\DataTable\DataTableServiceProvider::class` to `config/app.php`.

2. Publish the vue component with `php artisan vendor:publish --tag=datatable-component`.

3. Publish the sample structure class with `php artisan vendor:publish --tag=datatable-structure`.

4. Include the vue component in your app.js.

5. Run `gulp` / `npm run dev`.

6. In your blade page add:

    ```
    <data-table source="/myRoute">
        <span slot="data-table-title">{{ __("Title") }}</span>
        <span slot="data-table-totals">{{ __("Totals") }}</span>
        @include('laravel-enso/core::partials.modal')
    </data-table>
    ```

7. In your controller add `use DataTable` to include the trait. This adds to your controller two helper methods that will manage the builder:
	- initTable
	- getTableData

8. In the controller you must define a method for the query builder, such as:

    ```
    public function getTableQuery()
    {
        return MyModel::select(\DB::raw('id as DT_RowId, attribute1, ..., attributeN'));
    }
    ```

    Note it should return a QueryBuilder object and not a collection of results.

9. Also in the controller add `protected $tableStructureClass = MyTableStructure::class` which should be the fully qualified class name describing the structure of the table rendered in your page

10. In your routes files add three routes for the helper methods, and name them `myRoute.initTable`, `myRoute.getTableData` and optionally `myRoute.exportExcel` if you want the results exporting functionality.

11. Configure the table from the structure class.

### Options

- `source` - required, must reference the controllers base route, where both initTable & getTableData endpoints exist
- `header-class` - header class for the box element: info (default option) / default / primary / warning / danger / default
- `extra-filters` - reactive option array of the following format:
    ```
    "extraFilters": {
        "table": {
            "field_1" : '',
            "field_2" : '',
        }
    }
    ```
- `params` -
    ```
    "customParams": {
        "orders": {
            dispatched: ''
        }
    }
    ```
- 'interval-filters' - where `dbDateFormat` is REQUIRED if the filter values are dates. The given format has to match the database date format
    ```
    "intervalFilters": {
       "table":{
          "created_at": {
             "min":"value",
             "max":"value",
             "dbDateFormat": "Y-m-d"
          },
          "amount": {
            "min": 0,
            "max": 1000
          }
       }
    }
    ```

### TableStructure
  - `crtNo` - the label for the current number column
  - `actionButtons` - if it exists, it generates the action buttons column with the proper buttons. The value of the parameter is the column label
  - `headerAlign` & `bodyAlign` - type of alignment for the text in cells, eg. 'center'
  - `tableClass` - the table classes, eg. 'table display'
  - `notSearchable` - simple array w/ the column indexes that are **NOT** searchable using the component search
  - `enumMappings` - KV array, where key is the column name, and value is the Enum class name used for translation. These enums contain the translations for the flag-type values in your table, which you want to be presented in a more human friendly way, i.e. `Active`/`Inactive` instead of 0 / 1.
  - `appends` - optional array of model attributes, which can be appended before returning the query results <sup>1</sup> <sup>2</sup>  
  - `columns` - array of arrays. Each inner array contains:
     - `label` - table column header label
     - `data` - the alias of data in query result, eg. 'owner'
     - `name` - the table column used when searching, eg. 'owner.name'

<sup>1</sup> If you are appending attributes fetched through a relationship, you'll need to include the model's id attribute in the raw query  
<sup>2</sup> When using appended attributes, since these do not actually exist in the model's DB table, you need to exclude them using the `notSearchable` option, or you'll get errors when searching 

### Config

  - `excelRowLimit` - the maximum number of exported entries, when using the export function. You may need to adjust this depending on your server's RAM and PHP settings.
    
### Notes

- You may clone and/or install the [Laravel Enso](https://github.com/laravel-enso/Enso) package where you'll find working examples for using the component
- In the snippets folder you'll find a sublime snippet for quickly creating a stub table-structure class



### Publishes

- `php artisan vendor:publish --tag=datatable-component` - the VueJS component file
- `php artisan vendor:publish --tag=datatable-options` - the json options file
- `php artisan vendor:publish --tag=datatable-lang` - the default lang files
- `php artisan vendor:publish --tag=datatable-class` - the abstract TableStructure class that must be extended when creating specific structures
- `php artisan vendor:publish --tag=enso-update` - a common alias for when wanting to update the VueJS component,
once a newer version is released.

<!--h-->
### Contributions

are welcome. Pull requests are great, but issues are good too.

### License

This package is released under the MIT license.
<!--/h-->